<?php

namespace App\Controller;

use App\Repository\EpisodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(EpisodeRepository $episodeRepository)
    {
        return $this->render('home.html.twig', [
            'episodes' => $episodeRepository->findAllPublishedSorted(),
        ]);
    }
}
