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

        $stream = fopen($localPath, 'r');

        $rv = $filesystem->writeStream($file->getPath(), $stream);

        fclose($stream);

        return $rv;
    }

    public function writeFromStream(File $file, $stream)
    {
        /** @var Filesystem $filesystem */
        $filesystem = $this->filesystemMapping[$file->getFilesystem()];

        return $filesystem->writeStream($file->getPath(), $stream);
    }

    public function updateFromFile(File $file, $localPath)
    {
        /** @var Filesystem $filesystem */
        $filesystem = $this->filesystemMapping[$file->getFilesystem()];

        return $filesystem->update($file->getPath(), file_get_contents($localPath));
    }

    public function updateFromStream(File $file, $stream)
    {
        /** @var Filesystem $filesystem */
        $filesystem = $this->filesystemMapping[$file->getFilesystem()];

        return $filesystem->updateStream($file->getPath(), $stream);
    }

    public function writeOrUpdateFromFile(File $file, $localPath)
    {
        if ($this->exists($file)) {
            return $this->updateFromFile($file, $localPath);
        }

        return $this->writeFromFile($file, $localPath);
    }

    public function writeOrUpdateFromStream(File $file, $stream)
    {
        if ($this->exists($file)) {
            return $this->updateFromStream($file, $stream);
        }

        return $this->writeFromStream($file, $stream);
    }

    public function getStream(File $file)
    {
        /** @var Filesystem $filesystem */
        $filesystem = $this->filesystemMapping[$file->getFilesystem()];

        return $filesystem->readStream($file->getPath());
    }

    public function doWithTemporaryLocalFile(File $file, $cb = null)
    {
        if (! $cb) {
            return;
        }

        $temporaryLocalFileName = $this->getTemporaryLocalFileName($file);

        /** @var $cb callable */
        $cb($temporaryLocalFileName);

        unlink($temporaryLocalFileName);
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

        return $filesystem->delete($file->getPath());
    }

    public function getUrl(File $file)
    {
        if ($file->isNotManaged()) {
            return $file->toUrl();
        }

        $prefix = $this->urlMapping[$file->getFilesystem()];

        return $prefix . $file->getPath();
    }
}
