<?php

namespace App\Controller;

use App\Repository\EpisodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class VipController extends AbstractController
{
    /**
     * @Route("/vip.rss", defaults={"_format"="rss+xml"}, name="vip")
     */
    public function index(EpisodeRepository $episodeRepository)
    {
        $response = $this->render('itunes.xml.twig', [
            'episodes' => $episodeRepository->findAllSorted(),
            'block' => true,
        ]);

        $response->headers->set('Content-Type', 'application/rss+xml');

        return $response;
    }
}