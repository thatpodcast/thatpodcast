<?php

namespace App\Form\CommandObject\Admin;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class EpisodeDto
{
    /**
     * @var Assert\NotBlank
     */
    public $number;

    /**
     * @var Assert\NotBlank
     */
    public $title;

    public $subtitle = null;

    public $guid;

    public $path;

    /**
     * @var UploadedFile
     */
    public $backgroundImage = null;

    public $backgroundImageCreditBy;
    public $backgroundImageCreditUrl;
    public $backgroundImageCreditDescription;

    public $contentHtml;
    public $itunesSummaryHtml;
    public $transcriptHtml;

    /**
     * @var UploadedFile
     */
    public $transcriptText;

    public $published;
    public $publishedTimeZone = 'UTC';

    /**
     * @var UploadedFile
     */
    public $pristineMedia = null;
}
