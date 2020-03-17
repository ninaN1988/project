<?php

namespace App\Controller;

use App\Form\TimeTableType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ScheduleController extends AbstractController
{
    /**
     * @Route("/", name="")
     */
    public function index(Request $request)
    {
		$form = $this->createForm(TimeTableType::class);
		$form->handleRequest($request);
		$form->getErrors();
		return $this->render('schedule/index.html.twig', [
			'form' => $form->createView(),
		]);
    }
}
