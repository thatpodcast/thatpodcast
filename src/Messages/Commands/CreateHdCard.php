<?php

namespace App\Messages\Commands;

class CreateHdCard
{
    public $episodeId;

    public function __construct($episodeId)
    {
        $this->episodeId = $episodeId;
    }
}