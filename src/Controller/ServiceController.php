<?php

namespace App\Controller;

use App\Entity\Schedule;
use App\Entity\Nodes;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\ScheduleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ServiceController extends AbstractController
{
	
	/**
     * @Route("/service/test", name="service.test")
	*/
	public function test(Request $request, ScheduleRepository $scheduleRepository){
			
		$postData = $request->request->all();
		$cityStart = $postData['CityStart'];
		//$cityStart = 1;
		$cityStartTemp = $cityStart;
		$cityEnd = $postData['CityEnd'];
		//$cityEnd = 2;
		$schedules = $scheduleRepository->findAll();
		$schedulesStart = $scheduleRepository->findByCityStart($cityStart);
		$schedulesEnd = $scheduleRepository->findByCityEnd($cityEnd);
		//temp variable
		$nodes = new Nodes() ;
		$nodes->setNodesOk(false);
		//our result
		$schedulesRequest = new Schedule();
		
		$result = array();
		//we can start our search only if post data cityStart and post data CityEnd are not the same (no line), and if exist at least one Schedule where Schedule.startCity is the same as posta data start City, and at least one Schedule where Schedule.endCity with post data end City
		if(($cityStart != $cityEnd) && (sizeof($schedulesStart) > 0) && (sizeof($schedulesEnd) > 0)){
			
			//do algorithm
			$result = $this->algorithm($schedules, $nodes, $cityStartTemp, $cityStart, $cityEnd, $schedulesRequest);
			
			//1. check return result for temp nodes
			
			//check if nodes has one or more schedules
			if(sizeof($result[1]->getSchedules()) > 0){
				
				//set nodes to false and add to result
				$nodes = $result[1];
				$nodes->setNodesOk(false);
				$schedulesRequest = $result[5];
				$schedules = $result[0];
				foreach($schedules as $schedule){
					//always skip this schedule if temp node has only one added schedule in collection
					if(sizeof($result[1]->getSchedules()) == 1){
						if($nodes->getSchedules()->contains($schedule)){
							$schedule->setStatus('bad');
						}
					}
					//if it is direct line add nodes to our result
					if(($schedule->getCityEnd()->getId() == $cityEnd) && ($schedule->getCityStart()->getId() == $cityStart)){
						if($schedule->getStatus() != 'good'){							
							$schedule->setStatus('good');
							$nodes1 = new Nodes();
							$nodes1->addSchedule($schedule);
							$nodes1->setNodesOk(true);
							$schedulesRequest->addNode($nodes1);}				
					}
					//reset schedule status
					if($schedule->getStatus() == 'skip'){
                     		$schedule->setStatus('unvisited');
                    }
				}
				//add false node to our result
				$schedulesRequest->addNode($nodes);
				//reset nodes to 'empty'
				$nodes = new Nodes();
				$nodes->setNodesOk(false);
				//cityStartTemp is post data cityStart
				$cityStartTemp = $cityStart;
				//do again
				$this->algorithm($schedules, $nodes, $cityStartTemp, $cityStart, $cityEnd, $schedulesRequest);
			}
			
			//do again until every schedule in schedules has status different then unvisited
			foreach($result[0] as $schedule){
				if(($schedule->getCityStart()->getId() == $cityStart) && ($schedule->getStatus() == 'unvisited')){
					$nodes = new Nodes();
					$nodes->setNodesOk(false);
					$this->algorithm($result[0], $nodes, $cityStartTemp, $cityStart, $cityEnd, $schedulesRequest);
				}
			}
			
			$schedulesRequest = $result[5];
		}

		$allResultNodes = $schedulesRequest->getNodes();
		$schedulesResult = array();
		foreach($allResultNodes as $an){
			if($an->getNodesOk() == true){
				$schedulesNodes = $an->getSchedules();
				$o = array();
				foreach($schedulesNodes as $scheduleNodes){
					array_push($o,$scheduleNodes->jsonSerialize());
				}
				array_push($schedulesResult,$o);
			}
		}
		sort($schedulesResult);
		return new JsonResponse($schedulesResult);		
	}
	
	private function algorithm($schedules, $nodes, $cityStartTemp, $cityStart, $cityEnd, $schedulesRequest){
	
		foreach ($schedules as $schedule) {
			if (($schedule->getCityStart()->getId() == $cityStartTemp)		
				&& ($schedule->getStatus() != 'bad') // from that schedule do not check any more (startCity == post data start City) 
				&& ($schedule->getStatus() != 'good') //direct line
				&& ($schedule->getStatus() != 'skip') //skip element in list
				)
			{
				//schedule is visited
				$schedule->setStatus('skip');
				
				
				if(sizeof($nodes->getSchedules()) > 0){
					$scheduleNodesTempLast = $nodes->getSchedules()->last();
					if($scheduleNodesTempLast->getTimeEnd() > $schedule->getTimeStart()){
						return $this->algorithm($schedules, $nodes, $cityStartTemp, $cityStart, $cityEnd, $schedulesRequest);
					}
				}
				
				//add CityStart and Schedule to temp Nodes
				$nodes->addCity($schedule->getCityStart());
				$nodes->addSchedule($schedule);
					
				//1.Check if temp nodes in our result- schedule has nodes collection)
				
				//get all nodes from our result schedule
				$allResultNodes = $schedulesRequest->getNodes();
				
				//get last added Schedule from temp nodes 
				$scheduleNodesTemp = $nodes->getSchedules()->last();
				
				//foreach nodes in result
				foreach($allResultNodes as $an){
					//for this nodes get last added Schedule from result
					$scheduleNodes = $an->getSchedules()->last();
					
					//check if last added schedules are the same
					if(($scheduleNodes->getId() == $scheduleNodesTemp->getId())
					){
						//remove added city and schedules in temp nodes
						$nodes->removeCity($schedule->getCityStart());
						$nodes->removeSchedule($schedule);			
						
						//do again
						return $this->algorithm($schedules, $nodes, $cityStartTemp, $cityStart, $cityEnd, $schedulesRequest);
					}
				}
				
				//if temp nodes not in result, continue function
				
				//our temp node is not in result, continue
				//if Schedule End city == post cityEnd 
				if($schedule->getCityEnd()->getId() == $cityEnd){
					
					//nodes is good, add to result
					$nodes->setNodesOk(true);
					$schedulesRequest->addNode($nodes);
					//if it is direct line add nodes to our result
					if(($schedule->getCityEnd()->getId() == $cityEnd) && ($schedule->getCityStart()->getId() == $cityStart)){					
							$schedule->setStatus('good');				
					}
					//'empty' temp nodes
					$nodes = new Nodes();
					$nodes->setNodesOk(false);
					//cityStartTemp = post data cityStart
					$cityStartTemp = $cityStart;
					//reset schedules status
					foreach ($schedules as $schedule) {
                     	if($schedule->getStatus() == 'skip'){
                     		$schedule->setStatus('unvisited');
                     	}
                    }
					return $this->algorithm($schedules, $nodes, $cityStartTemp, $cityStart, $cityEnd, $schedulesRequest);
				}
				else{//Schedule end city = now temp city Start, do again 
					$cityStartTemp = $schedule->getCityEnd()->getId();
					return $this->algorithm($schedules, $nodes, $cityStartTemp, $cityStart, $cityEnd, $schedulesRequest);
				}
			}
		}
	
		return array($schedules, $nodes, $cityStartTemp, $cityStart, $cityEnd, $schedulesRequest);
	}
}
