<?php

namespace App\Messages\Commands;

class CreateFacebookCard
{
    public $episodeId;

    public function __construct($episodeId)
    {
        $this->episodeId = $episodeId;
    }
}