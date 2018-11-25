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
     * @var string
     */
    private $exportUrl;

    public function __construct($exportUrl)
    {
        $this->client = new Client();
        $this->exportUrl = $exportUrl;
    }

    public function fetchExport()
    {
        $response = $this->client->request('GET', $this->exportUrl);

        $json = json_decode($response->getBody(), true);
        return collect(array_values($json['episodes']));
    }
}