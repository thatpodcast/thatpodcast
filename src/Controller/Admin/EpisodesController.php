<?php

namespace App\Controller\Admin;

use App\Entity\Episode;
use App\FlysystemAssetManager\File;
use App\FlysystemAssetManager\FlysystemAssetManager;
use App\Form\Admin\EpisodeType;
use App\Form\CommandObject\Admin\EpisodeDto;
use App\Repository\EpisodeRepository;
use Psr\Log\LoggerInterface;
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
            $episode->setPublished($episodeDto->published);

            $this->handleUploadedImage($flysystemAssetManager, $episodeDto->backgroundImage, function (UploadedFile $uploadedFile, $tmpFile, $width = null, $height = null) use ($episode) {
                $file = File::create(
                    'content',
                    Episode::generateBackgroundImagePath($episode, $uploadedFile->getClientOriginalName()),
                    $uploadedFile->getClientMimeType(),
                    filesize($tmpFile)
                );

                $episode->setBackgroundImage($file);
                $episode->setBackgroundImageWidth($width);
                $episode->setBackgroundImageHeight($height);

                return $file;
            });

            $this->handleUploadedFile($flysystemAssetManager, $episodeDto->pristineMedia, function (UploadedFile $uploadedFile, $tmpFile) use ($episode) {
                $file = File::create(
                    'content',
                    Episode::generatePristineMediaPath($episode, $uploadedFile->getClientOriginalName()),
                    $uploadedFile->getClientMimeType(),
                    filesize($tmpFile)
                );

                $episode->setPristineMedia($file);

                return $file;
            });

            $this->handleUploadedText($episodeDto->transcriptText, function (UploadedFile $uploadedFile, $tmpFile) use ($episode) {
                $episode->setTranscriptText(file_get_contents($tmpFile));
            });

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
    public function edit(Request $request, Episode $episode, FlysystemAssetManager $flysystemAssetManager, MessageBusInterface $commandBus, LoggerInterface $logger)
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
        $episodeDto->published = $episode->getPublished();

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
            $episode->setPublished($episodeDto->published);

            $this->handleUploadedImage($flysystemAssetManager, $episodeDto->backgroundImage, function (UploadedFile $uploadedFile, $tmpFile, $width = null, $height = null) use ($episode) {
                $file = File::create(
                    'content',
                    Episode::generateBackgroundImagePath($episode, $uploadedFile->getClientOriginalName()),
                    $uploadedFile->getClientMimeType(),
                    filesize($tmpFile)
                );

                $episode->setBackgroundImage($file);
                $episode->setBackgroundImageWidth($width);
                $episode->setBackgroundImageHeight($height);

                return $file;
            });

            $this->handleUploadedFile($flysystemAssetManager, $episodeDto->pristineMedia, function (UploadedFile $uploadedFile, $tmpFile) use ($episode) {
                $file = File::create(
                    'content',
                    Episode::generatePristineMediaPath($episode, $uploadedFile->getClientOriginalName()),
                    $uploadedFile->getClientMimeType(),
                    filesize($tmpFile)
                );

                $episode->setPristineMedia($file);

                return $file;
            });

            $this->handleUploadedText($episodeDto->transcriptText, function (UploadedFile $uploadedFile, $tmpFile) use ($episode) {
                $episode->setTranscriptText(file_get_contents($tmpFile));
            });

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_episodes_show', [
                'id' => $episode->getId(),
            ]);
        }

        return $this->render('admin/episodes/edit.html.twig', [
            'form' => $form->createView(),
            'episode' => $episode,
        ]);
    }

    private function handleUploadedText(UploadedFile $uploadedFile = null, $cb = null) {
        if (! $cb) {
            return;
        }

        if (! $uploadedFile) {
            return;
        }

        if (! $uploadedFile->isValid()) {
            return;
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'text-');
        $uploadedFile->move(dirname($tmpFile), basename($tmpFile));

        /** @var callable $cb */
        $cb($uploadedFile, $tmpFile);

        unlink($tmpFile);
    }
    private function handleUploadedFile(FlysystemAssetManager $flysystemAssetManager, UploadedFile $uploadedFile = null, $cb = null) {
        if (! $cb) {
            return;
        }

        if (! $uploadedFile) {
            return;
        }

        if (! $uploadedFile->isValid()) {
            return;
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'flysystem-asset-manager-');
        $uploadedFile->move(dirname($tmpFile), basename($tmpFile));

        /** @var callable $cb */
        $file = $cb($uploadedFile, $tmpFile);

        $flysystemAssetManager->writeOrUpdateFromFile($file, $tmpFile);

        unlink($tmpFile);
    }

    private function handleUploadedImage(FlysystemAssetManager $flysystemAssetManager, UploadedFile $uploadedFile = null, $cb = null) {
        return $this->handleUploadedFile($flysystemAssetManager, $uploadedFile, function (UploadedFile $uploadedFile, $tmpFile) use ($cb) {
            $dimensions = getimagesize($tmpFile);

            if (! $dimensions) {
                $dimensions = [0,0];
            }

            list ($width, $height) = $dimensions;

            /** @var callable $cb */
            return $cb($uploadedFile, $tmpFile, $width, $height);
        });
    }
}
