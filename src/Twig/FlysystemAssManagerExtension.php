<?php

namespace App\Twig;

use App\FlysystemAssetManager\File;
use App\FlysystemAssetManager\FlysystemAssetManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FlysystemAssManagerExtension extends AbstractExtension
{
    /**
     * @var FlysystemAssetManager
     */
    private $flysystemAssetManager;

    /**
     * FlysystemAssManagerExtension constructor.
     * @param FlysystemAssetManager $flysystemAssetManager
     */
    public function __construct(FlysystemAssetManager $flysystemAssetManager)
    {
        $this->flysystemAssetManager = $flysystemAssetManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('flysystem_managed_asset_url', [$this, 'generateUrl']),
        ];
    }

    public function generateUrl(File $file = null)
    {
        if (! $file) {
            return '';
        }

        return $this->flysystemAssetManager->getUrl($file);
    }
}
