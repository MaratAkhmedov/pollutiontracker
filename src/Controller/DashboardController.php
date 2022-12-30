<?php

namespace App\Controller;

use App\Service\PollutionService;
use App\Service\TrafficService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(TrafficService $trafficService, PollutionService $pollutionService): Response
    {
        $trafficData = $trafficService->getTrafficData();
        $pollutionData = $pollutionService->getPollutionData();

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'trafficData' => $trafficData,
            'pollutionData' => $pollutionData
        ]);
    }
}
