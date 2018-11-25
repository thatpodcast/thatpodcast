<?php

namespace App\Controller;

use App\Repository\EpisodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ItunesController extends AbstractController
{
    /**
     * @Route("/itunes.rss", defaults={"_format"="rss+xml"}, name="itunes")
     */
    public function index(EpisodeRepository $episodeRepository)
    {
        $response = $this->render('itunes.xml.twig', [
            'episodes' => $episodeRepository->findAllPublishedSorted(),
        ]);

        $response->headers->set('Content-Type', 'application/rss+xml');

        return $response;
    }
}
