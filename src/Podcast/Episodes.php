<?php

namespace App\Podcast;

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