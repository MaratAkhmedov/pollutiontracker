<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class PollutionService
{
    protected HttpClientInterface $client;

    protected TrafficService $trafficService;

    /**
     * @param HttpClientInterface $client
     * @param TrafficService $trafficService
     */
    public function __construct(HttpClientInterface $client, TrafficService $trafficService)
    {
        $this->client = $client;
        $this->trafficService = $trafficService;
    }

    public function getPollutionData()
    {
        $featureCollection = $this->trafficService->getNeedMapPointsFromTrafficData();
        return $featureCollection;
    }
}