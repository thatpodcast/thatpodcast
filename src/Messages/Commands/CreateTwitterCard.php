<?php

namespace App\Messages\Commands;

class CreateTwitterCard
{
    public $episodeId;

    public function __construct($episodeId)
    {
        $this->episodeId = $episodeId;
    }
}