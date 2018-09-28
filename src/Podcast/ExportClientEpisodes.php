<?php

namespace App\Podcast;

use App\Ingest\ExportClient;
use Tightenco\Collect\Support\Collection;

class ExportClientEpisodes implements Episodes
{
    /**
     * @var ExportClient
     */
    private $exportClient;

    /**
     * ExportClientEpisodes constructor.
     * @param ExportClient $exportClient
     */
    public function __construct(ExportClient $exportClient)
    {
        $this->exportClient = $exportClient;
    }

    public function getAll()
    {
        /** @var Collection $export */
        $export = $this->exportClient->fetchExport();

        return $export->map(function ($jsonEpisode) {
            return Episode::fromJsonExport($jsonEpisode);
        })->all();
    }

    public function findBySlug($slug)
    {
        /** @var Collection $export */
        $export = $this->exportClient->fetchExport();

        $path = '/episodes/'.$slug;

        return $export->filter(function ($jsonEpisode) use ($path) {
            return $jsonEpisode['path'] === $path;
        })->map(function ($jsonEpisode) {
            return Episode::fromJsonExport($jsonEpisode);
        })->first();
    }
}