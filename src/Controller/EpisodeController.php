<?php

namespace App\Controller;

use App\Entity\Episode;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class EpisodeController extends AbstractController
{
    /**
     * @Route("/episodes/{path}", name="episode")
     */
    public function episode(Episode $episode)
    {
        return $this->render('episode.html.twig', [
            'episode' => $episode,
        ]);
    }
}
