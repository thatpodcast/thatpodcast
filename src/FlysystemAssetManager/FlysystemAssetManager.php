<?php

namespace App\FlysystemAssetManager;

use League\Flysystem\Filesystem;

class FlysystemAssetManager
{
    private $urlMapping = [];
    private $filesystemMapping = [];

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
        /** @var Filesystem $filesystem */
        $filesystem = $this->filesystemMapping[$file->getFilesystem()];

        $filesystem->writeStream($file->getPath(), $stream);
    }

    public function getStream(File $file)
    {
        /** @var Filesystem $filesystem */
        $filesystem = $this->filesystemMapping[$file->getFilesystem()];

        return $filesystem->readStream($file->getPath());
    }

    public function getTemporaryLocalFileName(File $file): string
    {
        $temporaryLocalFileName = tempnam(sys_get_temp_dir(), 'flysystem-asset-manager-');
        $targetStream = fopen($temporaryLocalFileName, 'w');

        $sourceStream = $this->getStream($file);

        if (false === stream_copy_to_stream($sourceStream, $targetStream)) {
            throw new \RuntimeException('Could not copy stream to ' . $temporaryLocalFileName);
        }

        return $temporaryLocalFileName;
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
