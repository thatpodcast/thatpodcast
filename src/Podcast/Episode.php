<?php

namespace App\Podcast;

class Episode
{
    private $number;
    private $guid;
    private $title;
    private $subtitle;
    private $date;
    private $mediaUrl;
    private $duration;
    private $fileSize;
    private $path;
    private $backgroundImageUrl;
    private $backgroundImageWidth;
    private $backgroundImageHeight;
    private $backgroundImageCreditBy;
    private $backgroundImageCreditUrl;
    private $backgroundImageCreditDescription;
    private $contentHtml;
    private $itunesSummaryHtml;
    private $transcriptHtml;

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return mixed
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return mixed
     */
    public function getMediaUrl()
    {
        return $this->mediaUrl;
    }

    /**
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @return mixed
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return mixed
     */
    public function getBackgroundImageUrl()
    {
        return $this->backgroundImageUrl;
    }

    /**
     * @return mixed
     */
    public function getBackgroundImageWidth()
    {
        return $this->backgroundImageWidth;
    }

    /**
     * @return mixed
     */
    public function getBackgroundImageHeight()
    {
        return $this->backgroundImageHeight;
    }

    /**
     * @return mixed
     */
    public function getBackgroundImageCreditBy()
    {
        return $this->backgroundImageCreditBy;
    }

    /**
     * @return mixed
     */
    public function getBackgroundImageCreditUrl()
    {
        return $this->backgroundImageCreditUrl;
    }

    /**
     * @return mixed
     */
    public function getBackgroundImageCreditDescription()
    {
        return $this->backgroundImageCreditDescription;
    }

    /**
     * @return mixed
     */
    public function getContentHtml()
    {
        return $this->contentHtml;
    }

    /**
     * @return mixed
     */
    public function getItunesSummaryHtml()
    {
        return $this->itunesSummaryHtml;
    }

    /**
     * @return mixed
     */
    public function getTranscriptHtml()
    {
        return $this->transcriptHtml;
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

        return $instance;
    }
}