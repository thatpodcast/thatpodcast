<?php

namespace App\Card;

use Imagine\Image\Box;
use Imagine\Image\Fill\Gradient\Vertical;
use Imagine\Image\FontInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Palette\PaletteInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;

class CardBuilder
{
    /**
     * @var PaletteInterface
     */
    private $palette;

    /**
     * @var ImagineInterface
     */
    private $imagine;

    public function __construct(ImagineInterface $imagine)
    {
        $this->imagine = $imagine;
        $this->palette = new RGB();
    }

    public function buildCard(CardConfiguration $cardConfiguration)
    {
        $origin = new Point(0, 0);
        $size = new Box($cardConfiguration->getWidth(), $cardConfiguration->getHeight());
        $grey = $this->palette->color('#aaa');

        $image = $this->imagine->create($size, $grey);

        $backgroundImage = $this->buildBackgroundImage($cardConfiguration);
        list ($textImage, $allTextHeight) = $this->buildTextImage($cardConfiguration);
        $gradientImage = $this->buildGradientImage($cardConfiguration, $allTextHeight);
        $logoImage = $this->buildLogoImage($cardConfiguration);

        $image->paste($backgroundImage, $origin);
        $image->paste($gradientImage, $origin);
        $image->paste($textImage, $origin);
        $image->paste($logoImage, $origin);

        return $image;
    }

    private function buildBackgroundImage(CardConfiguration $cardConfiguration) : ImageInterface
    {
        $transparentWhiteColor = $this->palette->color('#fff', 0);
        $size = new Box($cardConfiguration->getWidth(), $cardConfiguration->getHeight());

        if (! $cardConfiguration->hasBackgroundFileName()) {
            return $this->imagine->create($size, $transparentWhiteColor);
        }

        $backgroundImage = $this->imagine->open($cardConfiguration->getBackgroundFileName());


        $backgroundImageOriginalSize = $backgroundImage->getSize();
        if ($backgroundImageOriginalSize->getHeight() == $backgroundImageOriginalSize->getWidth()) {
            $backgroundImage->resize($size);
        } elseif ($backgroundImageOriginalSize->getHeight() > $backgroundImageOriginalSize->getWidth()) {
            // scale based on width as the smallest side
            $newSize = $backgroundImageOriginalSize->widen($cardConfiguration->getWidth());
            $y = floor($newSize->getHeight() - $cardConfiguration->getHeight() / 2);
            $backgroundImage->resize($newSize)->crop(new Point(0, $y), $size);
        } else {
            // scale based on height as the smallest side
            $newSize = $backgroundImageOriginalSize->heighten($cardConfiguration->getHeight());
            $x = floor(($newSize->getWidth() - $cardConfiguration->getWidth()) / 2);
            $backgroundImage->resize($newSize)->crop(new Point($x, 0), $size);
        }

        //return $backgroundImage->thumbnail($size, ImageInterface::THUMBNAIL_OUTBOUND);

        return $backgroundImage;
    }

    private function buildGradientImage(CardConfiguration $cardConfiguration, $allTextHeight) : ImageInterface
    {
        $size = new Box($cardConfiguration->getWidth(), $cardConfiguration->getHeight());

        $topGradientHeight = $cardConfiguration->getLogoHeight();

        $topGradient = $this->imagine->create(new Box($cardConfiguration->getWidth(), $topGradientHeight));
        $topGradient->fill(new Vertical(
            $topGradientHeight,
            $this->palette->color('#000', 20),
            $this->palette->color('#000', 0)
        ));

        $bottomGradientHeight = floor($allTextHeight / 2);
        $bottomGradientTargetCombinedHeight = floor($allTextHeight * 1.25 );
        $bottomGradient = $this->imagine->create(new Box($cardConfiguration->getWidth(), $bottomGradientHeight));
        $bottomGradient->fill(new Vertical(
            $bottomGradientHeight,
            $this->palette->color('#000', 0),
            $this->palette->color('#000', 40)
        ));

        $bottomGradientRestHeight = $bottomGradientTargetCombinedHeight - $bottomGradientHeight;
        $bottomGradientRest = $this->imagine->create(
            new Box($cardConfiguration->getWidth(), $bottomGradientRestHeight),
            $this->palette->color('#000', 40)
        );

        $transparentWhiteColor = $this->palette->color('#fff', 0);

        $gradientImage = $this->imagine->create($size, $transparentWhiteColor);

        $gradientImage->paste($topGradient, new Point(0, 0));
        $gradientImage->paste($bottomGradient, new Point(0, $cardConfiguration->getHeight() - $bottomGradientTargetCombinedHeight));
        $gradientImage->paste($bottomGradientRest, new Point(0, $cardConfiguration->getHeight() - $bottomGradientRestHeight));

        return $gradientImage;
    }

    private function buildLogoImage(CardConfiguration $cardConfiguration) : ImageInterface
    {
        $transparentWhiteColor = $this->palette->color('#fff', 0);
        $transparentShadowColor = $this->palette->color('#000', 90);
        $size = new Box($cardConfiguration->getWidth(), $cardConfiguration->getHeight());

        if (! $cardConfiguration->hasLogo()) {
            return $this->imagine->create($size, $transparentWhiteColor);
        }

        $logoImage = $this->imagine->open($cardConfiguration->getLogoFileName())
            ->resize(new Box($cardConfiguration->getLogoWidth(), $cardConfiguration->getLogoHeight()));

        $logoShadowWidth = $cardConfiguration->getLogoWidth() + $cardConfiguration->getMargin();
        $logoShadowHeight = $cardConfiguration->getLogoHeight() + $cardConfiguration->getMargin();
        $logoShadowImage = $this->imagine->create(new Box($logoShadowWidth, $logoShadowHeight), $transparentWhiteColor);
        $logoShadowImage->draw()->circle(
            new Point($logoShadowWidth / 2, $logoShadowHeight / 2),
            $cardConfiguration->getLogoWidth() / 2,
            $transparentShadowColor
        );
        $logoShadowImage->effects()->blur(2);

        $image = $this->imagine->create($size, $transparentWhiteColor);
        $image->paste($logoShadowImage, new Point(($cardConfiguration->getMargin() / 2) + 2, ($cardConfiguration->getMargin() / 2) + 2));
        $image->paste($logoImage, new Point($cardConfiguration->getMargin(), $cardConfiguration->getMargin()));

        return $image;
    }

    private function buildTextImage(CardConfiguration $cardConfiguration) : array
    {
        $maxWidth = $cardConfiguration->getWidth() - ($cardConfiguration->getMargin() * 2);

        $calculateLines = function($text, FontInterface $font, FontInterface $fontBack) use ($maxWidth) {
            $lines = [];
            $words = explode(' ', $text);
            $currentLine = '';
            $fontHeight = null;
            while ($nextWord = array_shift($words)) {
                $newDimensons = $font->box($currentLine . ' ' . $nextWord);
                if ($newDimensons->getWidth() > $maxWidth) {
                    $lines[] = $currentLine;
                    $currentLine = $nextWord;
                } else {
                    $currentLine = $currentLine ? $currentLine .= ' ' . $nextWord : $nextWord;
                }

                if ($fontHeight === null) {
                    $fontHeight = $newDimensons->getHeight();
                }
            }

            $lines[] = $currentLine;

            return [
                'lines' => $lines,
                'font' => $font,
                'fontBack' => $fontBack,
                'fontHeight' => $fontHeight,
            ];
        };

        $allLines = [];

        $fontColor = $this->palette->color('#fff');
        $fontShadowColor = $this->palette->color('#000', 90);

        if ($cardConfiguration->hasDate()) {
            $allLines[] = $calculateLines(
                $cardConfiguration->getDate(),
                $this->imagine->font(
                    $cardConfiguration->getDateFontFileName(),
                    $cardConfiguration->getDateFontSize(),
                    $fontColor
                ),
                $this->imagine->font(
                    $cardConfiguration->getDateFontFileName(),
                    $cardConfiguration->getDateFontSize(),
                    $fontShadowColor
                )
            );
        }

        if ($cardConfiguration->hasNumber()) {
            $allLines[] = $calculateLines(
                sprintf("Episode %s", $cardConfiguration->getNumber()),
                $this->imagine->font(
                    $cardConfiguration->getNumberFontFileName(),
                    $cardConfiguration->getNumberFontSize(),
                    $fontColor
                ),
                $this->imagine->font(
                    $cardConfiguration->getNumberFontFileName(),
                    $cardConfiguration->getNumberFontSize(),
                    $fontShadowColor
                )
            );
        }

        if ($cardConfiguration->hasTitle()) {
            $allLines[] = $calculateLines(
                $cardConfiguration->getTitle(),
                $this->imagine->font(
                    $cardConfiguration->getTitleFontFileName(),
                    $cardConfiguration->getTitleFontSize(),
                    $fontColor
                ),
                $this->imagine->font(
                    $cardConfiguration->getTitleFontFileName(),
                    $cardConfiguration->getTitleFontSize(),
                    $fontShadowColor
                )
            );
        }

        if ($cardConfiguration->hasSubtitle()) {
            $allLines[] = ['spacing' => $cardConfiguration->getMargin()];
            $allLines[] = $calculateLines(
                $cardConfiguration->getSubtitle(),
                $this->imagine->font(
                    $cardConfiguration->getSubtitleFontFileName(),
                    $cardConfiguration->getSubtitleFontSize(),
                    $fontColor
                ),
                $this->imagine->font(
                    $cardConfiguration->getSubtitleFontFileName(),
                    $cardConfiguration->getSubtitleFontSize(),
                    $fontShadowColor
                )
            );
        }

        $allTextHeight = collect($allLines)
            ->reduce(function ($carry, $item) {
                if (array_key_exists('spacing', $item)) {
                    return $carry + $item['spacing'];
                }
                return $carry + ( $item['fontHeight'] * count($item['lines']) );
            });

        $bottomText = $cardConfiguration->getHeight() - $cardConfiguration->getMargin() - $allTextHeight;

        $transparentWhiteColor = $this->palette->color('#fff', 0);
        $size = new Box($cardConfiguration->getWidth(), $cardConfiguration->getHeight());

        $textImage = $this->imagine->create($size, $transparentWhiteColor);
        $foregroundTextImage = $this->imagine->create($size, $transparentWhiteColor);
        $backgroundTextImage = $this->imagine->create($size, $transparentWhiteColor);

        foreach ($allLines as $lines) {
            if (array_key_exists('spacing', $lines)) {
                $bottomText += $lines['spacing'];

                continue;
            }

            $font = $lines['font'];
            $fontBack = $lines['fontBack'];
            $fontHeight = $lines['fontHeight'];

            foreach ($lines['lines'] as $line) {
                $backgroundTextImage->draw()->text($line, $fontBack, new Point($cardConfiguration->getMargin() + 1, $bottomText + 1));
                $foregroundTextImage->draw()->text($line, $font, new Point($cardConfiguration->getMargin(), $bottomText));
                $bottomText += $fontHeight;
            }
        }

        $backgroundTextImage->effects()->blur(2);
        $textImage->paste($backgroundTextImage, new Point(0, 0));

        $textImage->paste($foregroundTextImage, new Point(0, 0));

        return [$textImage, $allTextHeight];
    }
}