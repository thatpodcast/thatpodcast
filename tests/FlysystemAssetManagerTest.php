<?php

namespace App\Tests;

use App\FlysystemAssetManager\File;
use App\FlysystemAssetManager\FlysystemAssetManager;
use PHPUnit\Framework\TestCase;

class FlysystemAssetManagerTest extends TestCase
{
    public function testFileConstruction()
    {
        $file = new File(
            'content',
            '/path/with/leading/slash',
            'text/slash',
            1024
        );

        $this->assertEquals(
            'flysystem://content/path/with/leading/slash?content-type=text/slash&content-length=1024',
            $file->toUrl()
        );
    }

    public function testFileConstructionForPathWithoutLeadingSlash()
    {
        $file = new File(
            'content',
            'path/without/leading/slash',
            'text/slash',
            1024
        );

        $this->assertEquals(
            'flysystem://content/path/without/leading/slash?content-type=text/slash&content-length=1024',
            $file->toUrl()
        );
    }

    public function testCreateFileFromUrl()
    {
        $url = 'flysystem://content/episodes/3d6715d1-43bb-4194-adb4-d09ecf6b0c22/pristine-media/c042db5b.mp3?content-type=audio/mpeg&content-length=20853008';

        $file = File::createFromUrl($url);

        $this->assertEquals('content', $file->getFilesystem());
        $this->assertEquals('/episodes/3d6715d1-43bb-4194-adb4-d09ecf6b0c22/pristine-media/c042db5b.mp3', $file->getPath());
        $this->assertEquals('audio/mpeg', $file->getContentType());
        $this->assertEquals(20853008, $file->getContentLength());

        $this->assertEquals($url, $file->toUrl());
    }

    public function testGetUrl()
    {
        $file = File::createFromUrl('flysystem://content/episodes/3d6715d1-43bb-4194-adb4-d09ecf6b0c22/pristine-media/c042db5b.mp3?content-type=audio/mpeg&content-length=20853008');
        $flysystemAssetManager = new FlysystemAssetManager([
            'content' => 'https://uploads.thatpodcast.io',
        ], []);

        $this->assertEquals(
            'https://uploads.thatpodcast.io/episodes/3d6715d1-43bb-4194-adb4-d09ecf6b0c22/pristine-media/c042db5b.mp3',
            $flysystemAssetManager->getUrl($file)
        );
    }
}
