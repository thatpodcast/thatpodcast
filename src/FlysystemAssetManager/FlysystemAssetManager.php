<?php

namespace App\FlysystemAssetManager;

use League\Flysystem\Filesystem;

class FlysystemAssetManager
{
    private $urlMapping = [];
    private $filesystemMapping = [];

    /**
     * FlysystemAssetManager constructor.
     * @param array $urlMapping
     * @param array $filesystemMapping
     */
    public function __construct(array $urlMapping, array $filesystemMapping)
    {
        $this->urlMapping = $urlMapping;
        $this->filesystemMapping = $filesystemMapping;
    }

    public function writeFromFile(File $file, $localPath)
    {
        /** @var Filesystem $filesystem */
        $filesystem = $this->filesystemMapping[$file->getFilesystem()];

        $filesystem->write($file->getPath(), file_get_contents($localPath));
    }

    public function writeFromStream(File $file, $stream)
    {
        //
    }

    public function getStream(File $file)
    {
        /** @var Filesystem $filesystem */
        $filesystem = $this->filesystemMapping[$file->getFilesystem()];

        return $filesystem->readStream($file->getPath());
    }

    public function exists(File $file)
    {
        /** @var Filesystem $filesystem */
        $filesystem = $this->filesystemMapping[$file->getFilesystem()];

        try {
            $filesystem->assertPresent($file->getPath());

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete(File $file)
    {
        /** @var Filesystem $filesystem */
        $filesystem = $this->filesystemMapping[$file->getFilesystem()];

        $filesystem->delete($file->getPath());
    }

    public function getUrl(File $file)
    {
        $prefix = $this->urlMapping[$file->getFilesystem()];

        return $prefix . $file->getPath();
    }
}
