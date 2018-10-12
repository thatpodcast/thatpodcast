<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Repository\EpisodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TwitterPlayerController extends AbstractController
{
    /**
     * @Route("/player.html", name="twitter_player")
     */
    public function index()
    {
        return $this->render('twitter_player/index.html.twig', [
            'controller_name' => 'TwitterPlayerController',
        ]);
    }

    /**
     * @Route("/episodes.json", name="twitter_player_episodes")
     */
    public function episodes(UrlGeneratorInterface $urlGenerator, EpisodeRepository $episodeRepository)
    {
        $episodes = collect($episodeRepository->findAllSorted())
            ->mapWithKeys(function (Episode $item) use ($urlGenerator) {
                return [$item->getNumber() => [
                    'title' => $item->getTitle(),
                    'subtitle' => $item->getSubtitle(),
                    'audio_url' => $item->getMediaUrl(),
                    'duration' => date("H:i:s", strtotime('@'.$item->getDuration())),
                    'site_url' => $urlGenerator->generate('episode', ['path' => $item->getPath()], UrlGeneratorInterface::ABSOLUTE_URL),
                    'image' => $item->getBackgroundImageUrl(),
                ]];
            });

        return $this->json(['episodes' => $episodes]);
    }
}
