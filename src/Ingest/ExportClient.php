<?php

namespace App\Ingest;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class ExportClient
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * ExportClient constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    public function fetchExport()
    {
        $response = $this->client->request('GET', 'https://thatpodcast.io/export.json');

        $json = json_decode($response->getBody(), true);
        return collect(array_values($json['episodes']));
    }
}