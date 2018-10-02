<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\EpisodeRepository")
 */
class Episode
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $number;

    /**
     * @ORM\Column(type="string")
     */
    private $guid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subtitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mediaUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $duration;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $fileSize;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $backgroundImageUrl;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $backgroundImageWidth;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $backgroundImageHeight;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $backgroundImageCreditBy;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $backgroundImageCreditUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $backgroundImageCreditDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $contentHtml;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $itunesSummaryHtml;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $transcriptHtml;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getGuid(): ?string
    {
        return $this->guid;
    }

    public function setGuid(string $guid): self
    {
        $this->guid = $guid;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getMediaUrl(): ?string
    {
        return $this->mediaUrl;
    }

    public function setMediaUrl(string $mediaUrl): self
    {
        $this->mediaUrl = $mediaUrl;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function setFileSize(?int $fileSize): self
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getBackgroundImageUrl(): ?string
    {
        return $this->backgroundImageUrl;
    }

    public function setBackgroundImageUrl(?string $backgroundImageUrl): self
    {
        $this->backgroundImageUrl = $backgroundImageUrl;

        return $this;
    }

    public function getBackgroundImageWidth(): ?int
    {
        return $this->backgroundImageWidth;
    }

    public function setBackgroundImageWidth(?int $backgroundImageWidth): self
    {
        $this->backgroundImageWidth = $backgroundImageWidth;

        return $this;
    }

    public function getBackgroundImageHeight(): ?int
    {
        return $this->backgroundImageHeight;
    }

    public function setBackgroundImageHeight(?int $backgroundImageHeight): self
    {
        $this->backgroundImageHeight = $backgroundImageHeight;

        return $this;
    }

    public function getBackgroundImageCreditBy(): ?string
    {
        return $this->backgroundImageCreditBy;
    }

    public function setBackgroundImageCreditBy(?string $backgroundImageCreditBy): self
    {
        $this->backgroundImageCreditBy = $backgroundImageCreditBy;

        return $this;
    }

    public function getBackgroundImageCreditUrl(): ?string
    {
        return $this->backgroundImageCreditUrl;
    }

    public function setBackgroundImageCreditUrl(?string $backgroundImageCreditUrl): self
    {
        $this->backgroundImageCreditUrl = $backgroundImageCreditUrl;

        return $this;
    }

    public function getBackgroundImageCreditDescription(): ?string
    {
        return $this->backgroundImageCreditDescription;
    }

    public function setBackgroundImageCreditDescription(?string $backgroundImageCreditDescription): self
    {
        $this->backgroundImageCreditDescription = $backgroundImageCreditDescription;

        return $this;
    }

    public function getContentHtml(): ?string
    {
        return $this->contentHtml;
    }

    public function setContentHtml(?string $contentHtml): self
    {
        $this->contentHtml = $contentHtml;

        return $this;
    }

    public function getItunesSummaryHtml(): ?string
    {
        return $this->itunesSummaryHtml;
    }

    public function setItunesSummaryHtml(?string $itunesSummaryHtml): self
    {
        $this->itunesSummaryHtml = $itunesSummaryHtml;

        return $this;
    }

    public function getTranscriptHtml(): ?string
    {
        return $this->transcriptHtml;
    }

    public function setTranscriptHtml(?string $transcriptHtml): self
    {
        $this->transcriptHtml = $transcriptHtml;

        return $this;
    }

    static public function fromJsonExport($export)
    {
        $instance = new static();

        $instance->number = $export['number'];
        $instance->guid = $export['guid'];
        $instance->title = $export['title'];
        $instance->subtitle = $export['subtitle'];
        $instance->date = $export['date'];
        $instance->mediaUrl = $export['media_url'];
        $instance->duration = $export['duration'];
        $instance->fileSize = $export['file_size'];
        $instance->path = $export['path'];

        $instance->backgroundImageUrl = $export['background_image']['url'];
        $instance->backgroundImageWidth = $export['background_image']['width'] ?: null;
        $instance->backgroundImageHeight = $export['background_image']['height'] ?: null;
        $instance->backgroundImageCreditBy = $export['background_image']['credit']['by'];
        $instance->backgroundImageCreditUrl = $export['background_image']['credit']['url'];
        $instance->backgroundImageCreditDescription = $export['background_image']['credit']['description'];

        $instance->contentHtml = $export['content'];
        $instance->itunesSummaryHtml = $export['itunes_summary'];
        $instance->transcriptHtml = $export['transcript'];

        return $instance;
    }
}
