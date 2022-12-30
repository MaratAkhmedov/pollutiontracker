<?php

namespace App\Service;

use App\Data\GeojsonGeometryPoint;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TrafficService
{
    public HttpClientInterface $client;

    /**
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getTrafficData()
    {
        //TODO: Move https url to config
        $response = $this->client->request(
            'GET',
            'https://valencia.opendatasoft.com/api/v2/catalog/datasets/intensitat-transit-trams-intensidad-trafico-tramos/exports/json?limit=-1&offset=0&timezone=UTC'
        );
        //TODO: get only needed values from API (save to database), save geojson as file
        return $this->buildGeojsonFeatureCollection($response->toArray());
    }

    public function getNeedMapPointsFromTrafficData()
    {
        $trafficData = $this->getTrafficData();
        $consumptionPoints = $this->buildGeojsonCentralMapPointsFeatureCollection($trafficData);
        return $consumptionPoints;
    }

    private function buildGeojsonFeatureCollection(array $data)
    {

        $features = [];
        foreach ($data as $element)
        {
            if($element['tipo_vehiculo'] !== 'TRAFICO')
                continue;

            if($element['lectura'] === -1)
                continue;

            $element['geo_shape']['properties'] = [
                'des_tramo' => $element['des_tramo'],
                'idtramo' => $element['idtramo'],
                'imv' => $element['imv'],
                'lectura' => $element['lectura'],
                'tipo_vehiculo' => $element['tipo_vehiculo']
            ];
            $features[] = $element['geo_shape'];
        }

        return [
            'type' => 'FeatureCollection',
            'features' => $features
        ];
    }

    private function buildGeojsonCentralMapPointsFeatureCollection(array $data)
    {
        $features = [];
        foreach ($data['features'] as $feature)
        {
            if($feature['geometry']['type'] !== 'LineString')
                continue;

            $numLines = sizeof($feature['geometry']['coordinates']) - 1;
            for ($i = 0; $i < $numLines; $i++)
            {
                $coordinate1 = $feature['geometry']['coordinates'][$i];
                $coordinate2 = $feature['geometry']['coordinates'][$i+1];
                $x = ($coordinate1[0] + $coordinate2[0]) / 2;
                $y = ($coordinate1[1] + $coordinate2[1]) / 2;

                $features[] = [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [$x, $y]
                    ],
                    'properties' => []
                ];
            }
        }

        return [
            'type' => 'FeatureCollection',
            'features' => $features
        ];
    }



}