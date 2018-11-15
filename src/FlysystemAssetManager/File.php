<?php

namespace App\FlysystemAssetManager;

class File
{
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
     * File constructor.
     * @param string $filesystem
     * @param string $path
     * @param string $contentType
     * @param int $contentLength
     */
    public function __construct(string $filesystem, string $path, string $contentType, int $contentLength)
    {
        $this->filesystem = $filesystem;
        $this->path = strpos($path, '/') === 0 ? $path : '/'.$path;
        $this->contentType = $contentType;
        $this->contentLength = $contentLength;
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

    /**
     * @param string $url
     * @return static
     */
    static public function createFromUrl(string $url)
    {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if ('flysystem' !== $scheme) {
            throw new \InvalidArgumentException(sprintf(
                'The scheme "%s" is not supported; must be "flysystem"',
                $scheme
            ));
        }

        parse_str( parse_url( $url, PHP_URL_QUERY ), $query );

        return new static(
            parse_url($url, PHP_URL_HOST),
            parse_url($url, PHP_URL_PATH),
            $query['content-type'],
            $query['content-length']
        );
    }

    public function toUrl(): string
    {
        return sprintf(
            'flysystem://%s%s?content-type=%s&content-length=%d',
            $this->filesystem,
            $this->path,
            $this->contentType,
            $this->contentLength
        );
    }
}
