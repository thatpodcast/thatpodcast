<?php

namespace App\Form;

use App\FlysystemAssetManager\File;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileTransformer implements DataTransformerInterface
{
    public function transform($file): array
    {
        return [
            'file' => $file,
        ];
    }
    public function reverseTransform($data)
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $data['file'];

        return $uploadedFile;
    }
}
