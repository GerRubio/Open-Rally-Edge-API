<?php

namespace App\Service\File;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FileService
{
    public const AVATAR_INPUT_NAME = 'avatar';

    private FilesystemOperator $defaultStorage;
    private LoggerInterface $logger;

    public function __construct(FilesystemOperator $defaultStorage, LoggerInterface $logger)
    {
        $this->defaultStorage = $defaultStorage;
        $this->logger = $logger;
    }

    /**
     * @throws FilesystemException
     */
    public function uploadFile(UploadedFile $file, string $prefix, string $visibility): string
    {
        $fileName = sprintf('%s/%s.%s', $prefix, sha1(uniqid()), $file->guessExtension());
        $stream = fopen($file->getPathname(), 'r+');

        $this->defaultStorage->writeStream(
            $fileName,
            $stream,
            ['visibility' => $visibility]
        );

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $fileName;
    }

    public function downloadFile(string $path): ?string
    {
        try {
            return $this->defaultStorage->read($path);
        } catch (FilesystemException) {
            throw new \App\Exception\File\FileNotFoundException();
        }
    }

    public function validateFile(Request $request, string $inputName): UploadedFile
    {
        if (null === $file = $request->files->get($inputName)) {
            throw new BadRequestHttpException(sprintf('Cannot get file with input name %s', $inputName));
        }

        return $file;
    }

    public function deleteFile(?string $path): void
    {
        try {
            if (null !== $path) {
                $this->defaultStorage->delete($path);
            }
        } catch (\Exception) {
            $this->logger->warning(sprintf('File %s not found in the storage', $path));
        } catch (FilesystemException) {
            throw new \App\Exception\File\FileNotFoundException();
        }
    }
}