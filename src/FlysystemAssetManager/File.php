<?php

namespace App\FlysystemAssetManager;

class File
{
    /**
     * @var string
     */
    private $scheme;

    /**
     * @var string
     */
    private $filesystem;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var int
     */
    private $contentLength;

    /**
     * @var string
     */
    private $rawUrl;

    private function __construct()
    {
    }

    /**
     * @return string
     */
    public function getFilesystem(): string
    {
        return $this->filesystem;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @return int
     */
    public function getContentLength(): int
    {
        return $this->contentLength;
    }

    public function isManaged()
    {
        return 'flysystem' === $this->scheme;
    }

    public function isNotManaged()
    {
        return 'flysystem' !== $this->scheme;
    }

    static public function create(string $filesystem, string $path, string $contentType = null, int $contentLength = null): File
    {
        $file = new static();
        $file->scheme = 'flysystem';
        $file->filesystem = $filesystem;
        $file->path = strpos($path, '/') === 0 ? $path : '/'.$path;
        $file->contentType = $contentType;
        $file->contentLength = $contentLength;

        return $file;
    }

    static public function createFromUrl(string $url): File
    {
        $scheme = parse_url($url, PHP_URL_SCHEME);

        if ('flysystem' !== $scheme) {
            $file = new static();
            $file->scheme = $scheme;
            $file->rawUrl = $url;

            return $file;
        }

        parse_str( parse_url( $url, PHP_URL_QUERY ), $query );

        return static::create(
            parse_url($url, PHP_URL_HOST),
            parse_url($url, PHP_URL_PATH),
            $query['content-type'],
            $query['content-length']
        );
    }

    public function toUrl(): string
    {
        if ($this->isNotManaged()) {
            return $this->rawUrl;
        }

        return sprintf(
            'flysystem://%s%s?content-type=%s&content-length=%d',
            $this->filesystem,
            $this->path,
            $this->contentType,
            $this->contentLength
        );
    }
}
