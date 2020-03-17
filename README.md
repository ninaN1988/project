We use Symfony as framework. Symfony project is created by using composer and has web-site skeleton. 
Responsive design makes Bootstrap class (container, row, btn btn-primary float-right, form-group, card card-body bg-light, table table-bordered, etc., bootstrap class container for block body in base.html.twig).

'base.html.twig' (templates folder) file includes all links and scripts for JQuery, Bootstrap and Bootstrap select, and bootrstap class container for div block body:

head section
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css">

<div class="container">
	{% block body %}{% endblock %}
</div>

end of body section
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>  
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>


Add this line in file 'twig.yaml' (config/packages folder):
form_theme: 'bootstrap_4_layout.html.twig'
This way twig forms use Bootstrap by default.

This is railway application. 
Application is based on one index page. I call ajax to get response from other page as 'services' (ServiceController - response is JSON object).
I was thinking about architecture and communication, so that was the reason I have implemented this way.

User chose start and end city sorted by name ASC in the index page. 
CityRepository.php
public function findAll()
    {
        return $this->findBy(array(), array('name' => 'ASC'));
   	 }
JQuery Ajax lib is used to search easily list of cities (head section). It is simple for implementation:
'attr' => array('class' => 'selectpicker', 'data-live-search' => 'auto') in TimeTableForm.php

City class is simple. It has id, name and schedule with OneToMany relation. For examle A-B, A-C, D-A,...

After click on 'Search' application displays Schedule table (city start, time start, city end, time end) without page refresh because of ajax call.
Schedule table presents data from schedule entity (database - Schedule table data) with cityStart ManyToOne relation and cityEnd ManyToOne relation in basic case. 
We will not consider carrier as application parameter, but we can have records in Schedules table with the same data because of relations in entity. 
One Schedule has many Nodes. Nodes has collection of cities and schedules. Can be true, or false.
Our example is daily data schedules, consider only time for view.
public function setTimeEnd(\DateTimeInterface $timeEnd): self
{
    $this->timeEnd->setDate(8,11,1988);
    $this->timeEnd->setTime(
    $timeEnd->format('G'),
    $timeEnd->format('i')
	);
    return $this;
}

I have used JsonSerializable in entity Schedule to manage JSON display of schedule objects.
public function jsonSerialize()
{
	return array(
		'id' => $this->getId(),
		'distance' => $this->getDistance(),
        'cityStart' => $this->cityStart->getName(),
        'cityEnd'=> $this->cityEnd->getName(),
        'timeStart' => $this->timeStart->format('H:i'),
        'timeEnd'=> $this->timeEnd->format('H:i'),
    );
} 

Communication with 'services' is based on standard ajax call from javascript. 
$('#IDbutton').click(function () {
	$.ajax({  
	url:        'url to service',  
		type:       'POST',   
		data: { data for send },
		dataType:   'json',  
		async:      true,  
			 
		success: function(data, status) {
			update table / do something					
		},  
		error : function(xhr, textStatus, errorThrown) {  
			alert('Problem'); 
		} 
	});
});
Send POST data to ServiseController from form. 
ServiseController has business logic.
ServiseController returns response data in json object format. 
If response is ok, schedule data table will be updated inside javascript by adding html in index page.

Repository part - functions for algorithm:
public function findByCityStart(int $idCityStart){
		$qb = $this->createQueryBuilder('t');
		$qb->select('t')
			->where('t.cityStart = :city_start_id')
			->setParameter('city_start_id', $idCityStart)
		;
		
		return $qb->getQuery()->getResult();
	}
	
	public function findByCityEnd(int $idCityEnd){
		$qb = $this->createQueryBuilder('t');
		$qb->select('t')
			->where('t.cityEnd = :city_end_id')
			->setParameter('city_end_id', $idCityEnd)
		;
		
		return $qb->getQuery()->getResult();
	}

Controller side
ServiceController serializes all Schedule objects from result array and returns json response.

Algorithm
There are a lot of parameters that affect to route: 
Max number of transit(stops), travel time period (hours, one day or more), value of ticket, distance(kilometers),...
In the project we use only City, Daily schedules, time and distance as parameters. 
City and Schedules data are records stored in database. 
It appeared need for new Entity, Nodes, so I can handle algorithm.
In this algorithm we don't want to repeate cities in route from start to end. 
We have city start and city end request from post data.

***Start from Schedules list. 
Looking for the first record Schedule in the list of all schedules where schedule's startCity(temp) is the same as startCity from post data from start.
It will be temp startCity, becase it will be changed during process. Schedule can change status - skip, bad, good, unvisited.
	
	Check if schedule's startCity is temp startCity and has other status then unvisted.

		add Schedule and startCity to our temp Nodes
		add Schedule status to 'skip' -> if we go again trought Schedules list, we will skip this schedule

		Checking if temp Nodes is in our result Nodes list.
			if yes:
				remove added schedule and city from current temp nodes
				if list schedules in current nodes has one schedule (that schedule has cityStart as post data)
					Schedule status = 'bad' - do not check anymore
					temp Nodes is now new Nodes, with property nodesOk = false
					temp Start city is start city from post data
				set all schedules's status from 'skip' to 'unvisited'
				temp Start city is Schedule's startCity 
				search again trought ***Schedule list using updated parameters - schedules, Nodes and Result List Nodes, temp start city
			
		if schedule's end city is the same as end city from post data
			set temp Nodes property to true
			Add temp Nodes to result Nodes List
			Check if Schedule's city start is the same as post data city start
				if yes: Set schedule status to good
			temp Nodes is now new Nodes - empty
			temp Start city is start city from post data
			set all schedules's status from 'skip' to 'unvisited'
			search again trought updated ***Schedule list
		if not:
			current Schedule's endCity become startCity
			search again trought ***Schedule list using updated parameters - schedules, Nodes and Result List Nodes, temp start city
			
This is the basic idea for algorithm. Here is implemented code for the algorithm:

	/**
     * @Route("/service/test", name="service.test")
	*/
	// Request post data from form. We use ScheduleRepository to access database and Schedules data.
	public function test(Request $request, ScheduleRepository $scheduleRepository){
			
		$postData = $request->request->all();
		$cityStart = $postData['CityStart'];
		//temp city start is city start from post data
		$cityStartTemp = $cityStart;
		$cityEnd = $postData['CityEnd'];
		//get all schedules using repository
		$schedules = $scheduleRepository->findAll();
		//get all schedules where cityStart is the same as city start from post data
		$schedulesStart = $scheduleRepository->findByCityStart($cityStart);
		//get all schedules where cityEnd is the same as city end from post data
		$schedulesEnd = $scheduleRepository->findByCityEnd($cityEnd);
		//temp variable Nodes
		$nodes = new Nodes() ;
		$nodes->setNodesOk(false);
		//our result - Schedule has Nodes list
		$schedulesRequest = new Schedule();
		// algorithm result - array of schedules, temp nodes, cityStartTemp, cityStart, cityEnd, schedulesRequest
		$result = array();
		
		//we start our search if post data cityStart and post data CityEnd are not the same (no city line), 
		//and if exist at least one record Schedule where Schedule.startCity is the same as post data start City, 
		//and if exist at least one Schedule where Schedule.endCity with post data end City
		if(($cityStart != $cityEnd) && (sizeof($schedulesStart) > 0) && (sizeof($schedulesEnd) > 0)){
			
			//do algorithm and get result
			$result = $this->algorithm($schedules, $nodes, $cityStartTemp, $cityStart, $cityEnd, $schedulesRequest);
			
			//1. check return result
			
			//check if temp nodes has one or more schedules
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
				//add false nodes to our result
				$schedulesRequest->addNode($nodes);
				//reset nodes to 'empty'
				$nodes = new Nodes();
				$nodes->setNodesOk(false);
				//cityStartTemp is post data cityStart
				$cityStartTemp = $cityStart;
				//do it again
				$this->algorithm($schedules, $nodes, $cityStartTemp, $cityStart, $cityEnd, $schedulesRequest);
			}
			
			//do again until every schedule in schedules has status different then unvisited (bad or good)
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
				&& ($schedule->getStatus() != 'bad') // that schedule do not check any more (startCity == post data start City) 
				&& ($schedule->getStatus() != 'good') //direct line
				&& ($schedule->getStatus() != 'skip') //skip element in list
				)
			{
				//schedule is visited now
				$schedule->setStatus('skip');
				
				//this is time check
				//if our temp nodes has at least one schedule
				if(sizeof($nodes->getSchedules()) > 0){
					//get last added schedule
					$scheduleNodesTempLast = $nodes->getSchedules()->last();
					//check if nodes's schedule timeEnd > schedule time Start -> do again algorithm if it is true
					if($scheduleNodesTempLast->getTimeEnd() > $schedule->getTimeStart()){
						return $this->algorithm($schedules, $nodes, $cityStartTemp, $cityStart, $cityEnd, $schedulesRequest);
					}
				}
				
				//add CityStart and Schedule to temp Nodes
				$nodes->addCity($schedule->getCityStart());
				$nodes->addSchedule($schedule);
					
				//1.Check if temp nodes is in our result (schedule has nodes collection)
				
				//get all nodes from our result schedule
				$allResultNodes = $schedulesRequest->getNodes();
				
				//get last added Schedule from temp nodes 
				$scheduleNodesTemp = $nodes->getSchedules()->last();
				
				//foreach nodes from our result
				foreach($allResultNodes as $an){
					//get last added Schedule for this nodes in list 
					$scheduleNodes = $an->getSchedules()->last();
					
					//check if the last added schedules are the same
					if(($scheduleNodes->getId() == $scheduleNodesTemp->getId())
					){
						//remove added city and schedules from temp nodes
						$nodes->removeCity($schedule->getCityStart());
						$nodes->removeSchedule($schedule);			
						
						//do it again
						return $this->algorithm($schedules, $nodes, $cityStartTemp, $cityStart, $cityEnd, $schedulesRequest);
					}
				}
				
				//if temp nodes is not in result, continue
				
				//our temp node is not in result
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
					//do again algorithm
					return $this->algorithm($schedules, $nodes, $cityStartTemp, $cityStart, $cityEnd, $schedulesRequest);
				}
				else{
					//Schedule end city = now temp city Start, do again algorithm
					$cityStartTemp = $schedule->getCityEnd()->getId();
					return $this->algorithm($schedules, $nodes, $cityStartTemp, $cityStart, $cityEnd, $schedulesRequest);
				}
			}
		}
		//return algorithm result
		return array($schedules, $nodes, $cityStartTemp, $cityStart, $cityEnd, $schedulesRequest);
	}










