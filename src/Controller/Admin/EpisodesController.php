<?php

namespace App\Controller\Admin;

use App\Entity\Episode;
use App\FlysystemAssetManager\File;
use App\FlysystemAssetManager\FlysystemAssetManager;
use App\Form\Admin\EpisodeType;
use App\Form\CommandObject\Admin\EpisodeDto;
use App\Messages\Commands\ProcessPristineMedia;
use App\Repository\EpisodeRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class EpisodesController extends AbstractController
{
    /**
     * @Route("/admin/episodes", name="admin_episodes")
     */
    public function index(EpisodeRepository $episodeRepository)
    {
        return $this->render('admin/episodes/index.html.twig', [
            'episodes' => $episodeRepository->findAllSorted(),
        ]);
    }

    /**
     * @Route("/admin/episodes/{id}", name="admin_episodes_show", requirements={"id"="\d+"})
     */
    public function show(Episode $episode)
    {
        return $this->render('admin/episodes/show.html.twig', [
            'episode' => $episode,
        ]);
    }

    /**
     * @Route("/admin/episodes/create", name="admin_episodes_create")
     */
    public function create(Request $request, FlysystemAssetManager $flysystemAssetManager)
    {
        $episodeDto = new EpisodeDto();

        $form = $this->createForm(EpisodeType::class, $episodeDto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $episode = new Episode();

            $episode->setGuid((string) Uuid::uuid4());
            $episode->setNumber($episodeDto->number);
            $episode->setTitle($episodeDto->title);
            $episode->setSubtitle($episodeDto->subtitle);
            $episode->setBackgroundImageCreditBy($episodeDto->backgroundImageCreditBy);
            $episode->setBackgroundImageCreditUrl($episodeDto->backgroundImageCreditUrl);
            $episode->setBackgroundImageCreditDescription($episodeDto->backgroundImageCreditDescription);
            $episode->setContentHtml($episodeDto->contentHtml);
            $episode->setItunesSummaryHtml($episodeDto->itunesSummaryHtml);
            $episode->setTranscriptHtml($episodeDto->transcriptHtml);
            $episode->setPublishedDate($episodeDto->publishedDate);

            if ($episodeDto->backgroundImage) {
                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $episodeDto->backgroundImage;

                $file = new File(
                    'content',
                    Episode::generateBackgroundImagePath($episode, $uploadedFile->getClientOriginalName()),
                    $uploadedFile->getClientMimeType(),
                    $uploadedFile->getSize()
                );

                $episode->setBackgroundImage($file);
                $dimensions = getimagesize($uploadedFile->getRealPath());

                if ($dimensions) {
                    list ($width, $height) = $dimensions;

                    $episode->setBackgroundImageWidth($width);
                    $episode->setBackgroundImageHeight($height);
                }

                $flysystemAssetManager->writeFromFile($file, $uploadedFile->getRealPath());
            }

            if ($episodeDto->pristineMedia) {
                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $episodeDto->pristineMedia;

                $file = new File(
                    'content',
                    Episode::generatePristineMediaPath($episode, $uploadedFile->getClientOriginalName()),
                    $uploadedFile->getClientMimeType(),
                    $uploadedFile->getSize()
                );

                $episode->setPristineMedia($file);

                $flysystemAssetManager->writeFromFile($file, $uploadedFile->getRealPath());
            }

            $this->getDoctrine()->getManager()->persist($episode);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_episodes');
        }

        return $this->render('admin/episodes/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/episodes/{id}/edit", name="admin_episodes_edit", requirements={"id"="\d+"})
     */
    public function edit(Request $request, Episode $episode, FlysystemAssetManager $flysystemAssetManager, MessageBusInterface $commandBus)
    {
        $episodeDto = new EpisodeDto();

        $episodeDto->number = $episode->getNumber();
        $episodeDto->title = $episode->getTitle();
        $episodeDto->subtitle = $episode->getSubtitle();
        $episodeDto->backgroundImageCreditBy = $episode->getBackgroundImageCreditBy();
        $episodeDto->backgroundImageCreditUrl = $episode->getBackgroundImageCreditUrl();
        $episodeDto->backgroundImageCreditDescription = $episode->getBackgroundImageCreditDescription();
        $episodeDto->contentHtml = $episode->getContentHtml();
        $episodeDto->itunesSummaryHtml = $episode->getItunesSummaryHtml();
        $episodeDto->transcriptHtml = $episode->getTranscriptHtml();
        $episodeDto->publishedDate = $episode->getPublishedDate();

        $form = $this->createForm(EpisodeType::class, $episodeDto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $episode->setNumber($episodeDto->number);
            $episode->setTitle($episodeDto->title);
            $episode->setSubtitle($episodeDto->subtitle);
            $episode->setBackgroundImageCreditBy($episodeDto->backgroundImageCreditBy);
            $episode->setBackgroundImageCreditUrl($episodeDto->backgroundImageCreditUrl);
            $episode->setBackgroundImageCreditDescription($episodeDto->backgroundImageCreditDescription);
            $episode->setContentHtml($episodeDto->contentHtml);
            $episode->setItunesSummaryHtml($episodeDto->itunesSummaryHtml);
            $episode->setTranscriptHtml($episodeDto->transcriptHtml);
            $episode->setPublishedDate($episodeDto->publishedDate);

            if ($episodeDto->backgroundImage) {
                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $episodeDto->backgroundImage;

                $file = new File(
                    'content',
                    Episode::generateBackgroundImagePath($episode, $uploadedFile->getClientOriginalName()),
                    $uploadedFile->getClientMimeType(),
                    $uploadedFile->getSize()
                );

                $episode->setBackgroundImage($file);
                $dimensions = getimagesize($uploadedFile->getRealPath());

                if ($dimensions) {
                    list ($width, $height) = $dimensions;

                    $episode->setBackgroundImageWidth($width);
                    $episode->setBackgroundImageHeight($height);
                }

                $flysystemAssetManager->writeFromFile($file, $uploadedFile->getRealPath());
            }

            if ($episodeDto->pristineMedia) {
                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $episodeDto->pristineMedia;

                $file = new File(
                    'content',
                    Episode::generatePristineMediaPath($episode, $uploadedFile->getClientOriginalName()),
                    $uploadedFile->getClientMimeType(),
                    $uploadedFile->getSize()
                );

                $episode->setPristineMedia($file);

                $flysystemAssetManager->writeFromFile($file, $uploadedFile->getRealPath());

                // TODO: Move to Doctrine Lifecycle Handler
                $commandBus->dispatch(new ProcessPristineMedia($episode->getId()));
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_episodes');
        }

        return $this->render('admin/episodes/edit.html.twig', [
            'form' => $form->createView(),
            'episode' => $episode,
        ]);
    }
}
