<?php

namespace App\Messages\Commands;

class ProcessPristineMedia
{
    /**
     * @var int
     */
    public $episodeId;

    /**
     * ProcessPristineMedia constructor.
     * @param int $episodeId
     */
    public function __construct(int $episodeId)
    {
        $this->episodeId = $episodeId;
    }
}
