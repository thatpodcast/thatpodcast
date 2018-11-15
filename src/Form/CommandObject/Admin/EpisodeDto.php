<?php

namespace App\Form\CommandObject\Admin;

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

    public $duration;
    public $fileSize;
    public $path;

    public $backgroundImage = null;

    public $backgroundImageCreditBy;
    public $backgroundImageCreditUrl;
    public $backgroundImageCreditDescription;

    public $contentHtml;
    public $itunesSummaryHtml;
    public $transcriptHtml;

    public $publishedDate;

    public $pristineMedia = null;
}
