<?php

namespace App\Podcast;
use App\Entity\Episode;

interface Episodes
{
    /**
     * @return Episode[]
     */
    public function getAll();

    /**
     * @param $slug
     * @return Episode
     */
    public function findBySlug($slug);
}