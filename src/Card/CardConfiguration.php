<?php

namespace App\Card;

use App\Entity\Episode;
use App\FlysystemAssetManager\FlysystemAssetManager;

class CardConfiguration
{
    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var int
     */
    private $margin = 16;

    /**
     * @var string
     */
    private $patternFileName;

    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $dateFontFileName;

    /**
     * @var string
     */
    private $dateFontSize = 16;

    /**
     * @var string
     */
    private $number;

    /**
     * @var string
     */
    private $numberFontFileName;

    /**
     * @var string
     */
    private $numberFontSize = 16;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $titleFontFileName;

    /**
     * @var string
     */
    private $titleFontSize = 16;

    /**
     * @var string
     */
    private $subtitle;

    /**
     * @var string
     */
    private $subtitleFontFileName;

    /**
     * @var string
     */
    private $subtitleFontSize = 16;

    /**
     * @var string
     */
    private $backgroundFileName;

    /**
     * @var string
     */
    private $logoFileName;

    /**
     * @var int
     */
    private $logoWidth = 128;

    /**
     * @var int
     */
    private $logoHeight = 128;

    private function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getMargin(): int
    {
        return $this->margin;
    }

    public function withMargin($margin): self
    {
        $instance = clone($this);
        $instance->margin = $margin;

        return $instance;
    }

    public function getPatternFileName(): string
    {
        return $this->patternFileName;
    }

    public function hasPatternFileName(): bool
    {
        return $this->patternFileName !== null;
    }

    public function withPatternFileName($patternFileName): self
    {
        $instance = clone($this);
        $instance->patternFileName = $patternFileName;

        return $instance;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function hasDate(): bool
    {
        return $this->date !== null;
    }

    public function withDate($date): self
    {
        $instance = clone($this);
        $instance->date = $date;

        return $instance;
    }

    public function getDateFontFileName(): string
    {
        return $this->dateFontFileName;
    }

    public function getDateFontSize(): string
    {
        return $this->dateFontSize;
    }

    public function withDateFontFileName($fileName): self
    {
        $instance = clone($this);
        $instance->dateFontFileName = $fileName;

        return $instance;
    }

    public function withDateFontSize($size): self
    {
        $instance = clone($this);
        $instance->dateFontSize = $size;

        return $instance;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function hasNumber(): bool
    {
        return $this->number !== null;
    }

    public function withNumber($number): self
    {
        $instance = clone($this);
        $instance->number = $number;

        return $instance;
    }

    public function getNumberFontFileName(): string
    {
        return $this->numberFontFileName;
    }

    public function getNumberFontSize(): string
    {
        return $this->numberFontSize;
    }

    public function withNumberFontFileName($fileName): self
    {
        $instance = clone($this);
        $instance->numberFontFileName = $fileName;

        return $instance;
    }

    public function withNumberFontSize($size): self
    {
        $instance = clone($this);
        $instance->numberFontSize = $size;

        return $instance;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function hasTitle(): bool
    {
        return $this->title !== null;
    }

    public function withTitle($title): self
    {
        $instance = clone($this);
        $instance->title = $title;

        return $instance;
    }
    public function getTitleFontFileName(): string
    {
        return $this->titleFontFileName;
    }

    public function getTitleFontSize(): string
    {
        return $this->titleFontSize;
    }

    public function withTitleFontFileName($fileName): self
    {
        $instance = clone($this);
        $instance->titleFontFileName = $fileName;

        return $instance;
    }
    public function withTitleFontSize($size): self
    {
        $instance = clone($this);
        $instance->titleFontSize = $size;

        return $instance;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function hasSubtitle(): bool
    {
        return $this->subtitle !== null;
    }

    public function withSubtitle($subtitle): self
    {
        $instance = clone($this);
        $instance->subtitle = $subtitle;

        return $instance;
    }
    public function getSubtitleFontFileName(): string
    {
        return $this->subtitleFontFileName;
    }

    public function getSubtitleFontSize(): string
    {
        return $this->subtitleFontSize;
    }

    public function withSubtitleFontFileName($fileName): self
    {
        $instance = clone($this);
        $instance->subtitleFontFileName = $fileName;

        return $instance;
    }

    public function withSubtitleFontSize($size): self
    {
        $instance = clone($this);
        $instance->subtitleFontSize = $size;

        return $instance;
    }

    public function getBackgroundFileName(): string
    {
        return $this->backgroundFileName;
    }

    public function hasBackgroundFileName(): bool
    {
        return $this->backgroundFileName !== null;
    }

    public function withBackgroundFileName($backgroundFileName): self
    {
        $instance = clone($this);
        $instance->backgroundFileName = $backgroundFileName;

        return $instance;
    }

    public function getLogoFileName(): string
    {
        return $this->logoFileName;
    }

    public function getLogoWidth(): int
    {
        return $this->logoWidth;
    }

    public function getLogoHeight(): int
    {
        return $this->logoHeight;
    }

    public function hasLogo(): bool
    {
        return $this->logoFileName !== null;
    }

    public function withLogoFileName($fileName): self
    {
        $instance = clone($this);
        $instance->logoFileName = $fileName;

        return $instance;
    }

    public function withLogoDimensions($width, $height): self
    {
        $instance = clone($this);
        $instance->logoWidth = $width;
        $instance->logoHeight = $height;

        return $instance;
    }

    public static function create($width, $height)
    {
        return new static($width, $height);
    }

    public function withDefaultFonts($projectDirectory): self
    {
        $instance = clone($this);

        $fontDirectory = $projectDirectory . '/assets/fonts';

        return $instance
            ->withDateFontFileName($fontDirectory . '/RobotoCondensed-Regular.ttf')
            ->withNumberFontFileName($fontDirectory . '/RobotoCondensed-Bold.ttf')
            ->withTitleFontFileName($fontDirectory . '/RobotoCondensed-Light.ttf')
            ->withSubtitleFontFileName($fontDirectory . '/RobotoCondensed-Regular.ttf')
            ;
    }

    public function withDefaultLogo($projectDirectory): self
    {
        $instance = clone($this);

        $logoDirectory = $projectDirectory . '/assets/png';

        return $instance
            ->withLogoFileName($logoDirectory.'/that_podcast_with_proper_case_1000x1000.png')
            ;
    }

    public function withEpisode(Episode $episode, FlysystemAssetManager $flysystemAssetManager): self
    {
        $cardConfiguration = clone($this);

        if ($episode->getBackgroundImageUrl()) {
            $backgroundImageTmpFile = $flysystemAssetManager->getTemporaryLocalFileName($episode->getBackgroundImage());

            $cardConfiguration = $cardConfiguration
                ->withBackgroundFileName($backgroundImageTmpFile)
            ;
        }

        if ($episode->getPublished()) {
            $cardConfiguration = $cardConfiguration
                ->withDate($episode->getPublished()->format('F jS, Y'))
            ;
        }

        if ($episode->getNumber()) {
            $cardConfiguration = $cardConfiguration
                ->withNumber($episode->getNumber())
            ;
        }

        if ($episode->getTitle()) {
            $cardConfiguration = $cardConfiguration
                ->withTitle($episode->getTitle())
            ;
        }

        if ($episode->getSubtitle()) {
            $cardConfiguration = $cardConfiguration
                ->withSubtitle($episode->getSubtitle());
            ;
        }

        return $cardConfiguration;
    }

    public static function createTwitterCard(): self
    {
        return CardConfiguration::create(876, 438)
            ->withMargin(32)
            ->withDateFontSize('16')
            ->withNumberFontSize('48')
            ->withTitleFontSize('24')
            ->withSubtitleFontSize('16')
            ->withLogoDimensions(128, 128)
            ;
    }

    public static function createFacebookCard(): self
    {
        return CardConfiguration::create(1200, 628)
            ->withMargin(32)
            ->withDateFontSize('24')
            ->withNumberFontSize('96')
            ->withTitleFontSize('32')
            ->withSubtitleFontSize('24')
            ->withLogoDimensions(192, 192)
            ;
    }

    public static function createItunesCard(): self
    {
        return CardConfiguration::create(1024, 1024)
            ->withMargin(32)
            ->withDateFontSize('24')
            ->withNumberFontSize('130')
            ->withTitleFontSize('42')
            ->withSubtitleFontSize('24')
            ->withLogoDimensions(256, 256)
            ;
    }

    public static function create1080pCard(): self
    {
        return CardConfiguration::create(1920, 1080)
            ->withMargin(64)
            ->withDateFontSize('32')
            ->withNumberFontSize('130')
            ->withTitleFontSize('54')
            ->withSubtitleFontSize('32')
            ->withLogoDimensions(384, 384)
            ;
    }
}