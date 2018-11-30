<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Behat\Transliterator\Transliterator;
use Doctrine\ORM\Mapping as ORM;
use App\FlysystemAssetManager\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numberForSort;

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
    private $transcriptText;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $transcriptHtml;

    /**
     * @ORM\Column(type="utcdatetime", nullable=true)
     * @var \DateTime
     */
    private $published;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var \DateTimeZone
     */
    private $publishedTimeZone;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @var \DateTime
     */
    private $backgroundImageUpdated;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pristineMediaUrl;

    /**
     * @ORM\Column(type="utcdatetime", nullable=true)
     * @var \DateTime
     */
    private $pristineMediaUrlUpdated;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $itunesCardUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $twitterCardUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $facebookCardUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hdCardUrl;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function getNumberForSort(): ?string
    {
        return $this->numberForSort;
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

    public function setMediaUrl(?string $mediaUrl = null): self
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

    static public function generateBackgroundImagePath(Episode $episode, $fileName)
    {
        preg_match('/^(.+)\.(.+?)$/', $fileName, $matches);

        list ($fullMatch, $fileNameWithoutExtension, $extension) = $matches;

        return implode('/', [
                '',
                'episodes',
                Transliterator::transliterate($episode->getGuid()),
                'background-image',
                Transliterator::transliterate($fileNameWithoutExtension),
            ]) . '.' . $extension;
    }

    public function getBackgroundImageUrl(): ?string
    {
        return $this->backgroundImageUrl;
    }

    public function setBackgroundImageUrl(?string $backgroundImageUrl): self
    {
        $this->backgroundImageUrl = $backgroundImageUrl;
        $this->backgroundImageUpdated = new \DateTime('now', new \DateTimeZone('UTC'));

        return $this;
    }

    public function getBackgroundImage(): ?File
    {
        if (! $this->backgroundImageUrl) {
            return null;
        }

        return File::createFromUrl($this->backgroundImageUrl);
    }

    public function getBackgroundImageUploadedFile(): ?File
    {
        return $this->getBackgroundImage();
    }

    public function setBackgroundImageUploadedFile(UploadedFile $uploadedFile): self
    {
        $this->setBackgroundImage(new File(
            'content',
            self::generatePristineMediaPath($this, $uploadedFile->getClientOriginalName()),
            $uploadedFile->getClientMimeType(),
            $uploadedFile->getSize()
        ));

        return $this;
    }

    public function setBackgroundImage(?File $backgroundImage): self
    {
        return $this->setBackgroundImageUrl($backgroundImage ? $backgroundImage->toUrl() : null);
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

    public function getTranscriptText(): ?string
    {
        return $this->transcriptText;
    }

    public function setTranscriptText(?string $transcriptText): self
    {
        $this->transcriptText = $transcriptText;

        return $this;
    }

    /**
     * @return \DateTime
     * @deprecated
     */
    public function getPublishedDate(): ?\DateTime
    {
        return $this->published;
    }

    /**
     * @param \DateTime $publishedDate
     * @deprecated
     */
    public function setPublishedDate(\DateTime $publishedDate = null): void
    {
        $this->setPublished($publishedDate);
    }

    /**
     * @return \DateTime
     */
    public function getPublished(): ?\DateTime
    {
        return $this->published;
    }

    public function setPublished(\DateTime $published = null): void
    {
        if (! $published) {
            $this->published = null;
            $this->publishedTimeZone = null;

            return;
        }

        $this->publishedTimeZone = $published->getTimezone()->getName();

        $utcPublished = clone($published);
        $utcPublished->setTimezone(new \DateTimeZone('UTC'));

        $this->published = $utcPublished;
    }

    public function getLocalPublished(): ?\DateTime
    {
        if (! $this->published) {
            return null;
        }

        if (! $this->publishedTimeZone) {
            return clone($this->published);
        }

        $localPublished = clone($this->published);
        $localPublished->setTimezone($this->getPublishedTimeZone());

        return $localPublished;
    }

    /**
     * @return \DateTimeZone
     */
    public function getPublishedTimeZone(): ?\DateTimeZone
    {
        return new \DateTimeZone($this->publishedTimeZone ?? 'UTC');
    }



    public function getDownload(): ?File
    {
        return $this->getMedia();
    }

    public function getPlayer(): ?File
    {
        return $this->getMedia();
    }

    public function getRss(): ?File
    {
        return $this->getMedia();
    }

    public function getBackgroundImageDirectory()
    {
        return $this->getGuid();
    }

    /**
     * @return mixed
     */
    public function getPristineMediaUrl(): ?string
    {
        return $this->pristineMediaUrl;
    }

    static public function generatePristineMediaPath(Episode $episode, $fileName)
    {
        preg_match('/^(.+)\.(.+?)$/', $fileName, $matches);

        list ($fullMatch, $fileNameWithoutExtension, $extension) = $matches;

        return implode('/', [
            '',
            'episodes',
            Transliterator::transliterate($episode->getGuid()),
            'pristine-media',
            Transliterator::transliterate($fileNameWithoutExtension),
        ]) . '.' . $extension;
    }

    /**
     * @param mixed $pristineMediaUrl
     */
    public function setPristineMediaUrl($pristineMediaUrl): self
    {
        $this->pristineMediaUrl = $pristineMediaUrl;
        $this->pristineMediaUrlUpdated = new \DateTime('now', new \DateTimeZone('UTC'));

        return $this;
    }

    public function setPristineMedia(File $file): self
    {
        $this->setPristineMediaUrl($file->toUrl());

        return $this;
    }

    public function getPristineMedia(): ?File
    {
        if (! $this->pristineMediaUrl) {
            return null;
        }

        return File::createFromUrl($this->pristineMediaUrl);
    }

    static public function generateMediaPath(Episode $episode, $fileName)
    {
        preg_match('/^(.+)\.(.+?)$/', $fileName, $matches);

        list ($fullMatch, $fileNameWithoutExtension, $extension) = $matches;

        return implode('/', [
                '',
                'episodes',
                Transliterator::transliterate($episode->getGuid()),
                'media',
                Transliterator::transliterate($fileNameWithoutExtension),
            ]) . '.' . $extension;
    }

    public function setMedia(File $file): self
    {
        $this->mediaUrl = $file->toUrl();

        return $this;
    }

    public function getMedia(): ?File
    {
        if (! $this->mediaUrl) {
            return null;
        }

        return File::createFromUrl($this->mediaUrl);
    }

    /**
     * @return mixed
     */
    public function getItunesCardUrl()
    {
        return $this->itunesCardUrl;
    }

    /**
     * @param mixed $itunesCardUrl
     */
    public function setItunesCardUrl($itunesCardUrl): void
    {
        $this->itunesCardUrl = $itunesCardUrl;
    }

    public function getItunesCard(): ?File
    {
        if (! $this->itunesCardUrl) {
            return null;
        }

        return File::createFromUrl($this->itunesCardUrl);
    }

    public function setItunesCard(?File $file): void
    {
        $this->setItunesCardUrl($file ? $file->toUrl() : null);
    }

    static public function generateItunesCardPath(Episode $episode, $fileName)
    {
        preg_match('/^(.+)\.(.+?)$/', $fileName, $matches);

        list ($fullMatch, $fileNameWithoutExtension, $extension) = $matches;

        return implode('/', [
                '',
                'episodes',
                Transliterator::transliterate($episode->getGuid()),
                'itunes-card',
                Transliterator::transliterate($fileNameWithoutExtension),
            ]) . '.' . $extension;
    }

    /**
     * @return mixed
     */
    public function getTwitterCardUrl()
    {
        return $this->twitterCardUrl;
    }

    /**
     * @param mixed $twitterCardUrl
     */
    public function setTwitterCardUrl($twitterCardUrl): void
    {
        $this->twitterCardUrl = $twitterCardUrl;
    }

    public function getTwitterCard(): ?File
    {
        if (! $this->twitterCardUrl) {
            return null;
        }

        return File::createFromUrl($this->twitterCardUrl);
    }

    public function setTwitterCard(?File $file): void
    {
        $this->setTwitterCardUrl($file ? $file->toUrl() : null);
    }

    static public function generateTwitterCardPath(Episode $episode, $fileName)
    {
        preg_match('/^(.+)\.(.+?)$/', $fileName, $matches);

        list ($fullMatch, $fileNameWithoutExtension, $extension) = $matches;

        return implode('/', [
                '',
                'episodes',
                Transliterator::transliterate($episode->getGuid()),
                'twitter-card',
                Transliterator::transliterate($fileNameWithoutExtension),
            ]) . '.' . $extension;
    }

    /**
     * @return mixed
     */
    public function getFacebookCardUrl()
    {
        return $this->facebookCardUrl;
    }

    /**
     * @param mixed $facebookCardUrl
     */
    public function setFacebookCardUrl($facebookCardUrl): void
    {
        $this->facebookCardUrl = $facebookCardUrl;
    }

    public function getFacebookCard(): ?File
    {
        if (! $this->facebookCardUrl) {
            return null;
        }

        return File::createFromUrl($this->facebookCardUrl);
    }

    public function setFacebookCard(?File $file): void
    {
        $this->setFacebookCardUrl($file ? $file->toUrl() : null);
    }

    static public function generateFacebookCardPath(Episode $episode, $fileName)
    {
        preg_match('/^(.+)\.(.+?)$/', $fileName, $matches);

        list ($fullMatch, $fileNameWithoutExtension, $extension) = $matches;

        return implode('/', [
                '',
                'episodes',
                Transliterator::transliterate($episode->getGuid()),
                'facebook-card',
                Transliterator::transliterate($fileNameWithoutExtension),
            ]) . '.' . $extension;
    }

    public function getHdCardUrl()
    {
        return $this->hdCardUrl;
    }

    public function setHdCardUrl($hdCardUrl): void
    {
        $this->hdCardUrl = $hdCardUrl;
    }

    public function getHdCard(): ?File
    {
        if (! $this->hdCardUrl) {
            return null;
        }

        return File::createFromUrl($this->hdCardUrl);
    }

    public function setHdCard(?File $file): void
    {
        $this->setHdCardUrl($file ? $file->toUrl() : null);
    }

    static public function generateHdCardPath(Episode $episode, $fileName)
    {
        preg_match('/^(.+)\.(.+?)$/', $fileName, $matches);

        list ($fullMatch, $fileNameWithoutExtension, $extension) = $matches;

        return implode('/', [
                '',
                'episodes',
                Transliterator::transliterate($episode->getGuid()),
                'hd-card',
                Transliterator::transliterate($fileNameWithoutExtension),
            ]) . '.' . $extension;
    }

    static private function convertHoursMinutesSecondsToSeconds($input): int
    {
        list ($hours, $minutes, $seconds) = explode(':', $input);

        return $seconds + ($minutes * 60) + ($hours * 60 * 60);
    }

    static private function extractPathFromExportedPath($exportedPath): string
    {
        list ($junk, $junk, $path) = explode('/', $exportedPath);

        return $path;
    }

    public function refreshFrom(Episode $episode)
    {
        $this->number = $episode->getNumber();
        $this->numberForSort = $episode->getNumberForSort();
        $this->guid = $episode->getGuid();
        $this->title = $episode->getTitle();
        $this->subtitle = $episode->getSubtitle();
        $this->setPublished($episode->getLocalPublished());
        $this->mediaUrl = $episode->getMediaUrl();
        $this->duration = $episode->getDuration();
        $this->fileSize = $episode->getFileSize();
        $this->path = $episode->getPath();

        $this->backgroundImageUrl = $episode->getBackgroundImageUrl();
        $this->backgroundImageWidth = $episode->getBackgroundImageWidth();
        $this->backgroundImageHeight = $episode->getBackgroundImageHeight();
        $this->backgroundImageCreditBy = $episode->getBackgroundImageCreditBy();
        $this->backgroundImageCreditUrl = $episode->getBackgroundImageCreditUrl();
        $this->backgroundImageCreditDescription = $episode->getBackgroundImageCreditDescription();

        $this->contentHtml = $episode->getContentHtml();
        $this->itunesSummaryHtml = $episode->getItunesSummaryHtml();
        $this->transcriptHtml = $episode->getTranscriptHtml();
    }

    static public function fromJsonExport($export)
    {
        $instance = new static();

        $instance->number = $export['number'];
        $instance->numberForSort = sprintf('%010.1f', $export['number']);
        $instance->guid = $export['guid'];
        $instance->title = $export['title'];
        $instance->subtitle = $export['subtitle'];
        $published = new \DateTime(sprintf('@%d', $export['date']));
        $published->setTimezone(new \DateTimeZone('UTC'));
        $instance->setPublished($published);
        $instance->mediaUrl = $export['media_url'];
        $instance->duration = static::convertHoursMinutesSecondsToSeconds($export['duration']);
        $instance->fileSize = $export['file_size'];
        $instance->path = static::extractPathFromExportedPath($export['path']);

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
