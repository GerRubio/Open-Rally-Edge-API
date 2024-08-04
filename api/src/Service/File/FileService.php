<?php

namespace App\Service\File;

use Aws\S3\S3Client;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FileService
{
    public const AVATAR_INPUT_NAME = 'avatar';

    private S3Client $client;
    private string $bucketName;
    private LoggerInterface $logger;

    public function __construct(S3Client $client, string $bucketName, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->bucketName = $bucketName;
        $this->logger = $logger;
    }

    public function uploadFile(UploadedFile $file, string $prefix, string $visibility): string
    {
        $fileName = sprintf('%s/%s.%s', $prefix, sha1(uniqid()), $file->guessExtension());
        $filePath = $file->getPathname();

        try {
            $this->client->putObject([
                'Bucket' => $this->bucketName,
                'Key' => $fileName,
                'SourceFile' => $filePath,
                'ACL' => $visibility === 'public' ? 'public-read' : 'private',
            ]);

            return $fileName;
        } catch (Exception $exception) {
            $this->logger->error('File upload failed: ' . $exception->getMessage());
            throw new BadRequestHttpException('Failed to upload file');
        }
    }

    public function downloadFile(string $path): ?string
    {
        try {
            $result = $this->client->getObject([
                'Bucket' => $this->bucketName,
                'Key' => $path,
            ]);

            return (string) $result['Body'];
        } catch (Exception $exception) {
            $this->logger->error('File download failed: ' . $exception->getMessage());
            throw new BadRequestHttpException('Failed to download file');
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
                $this->client->deleteObject([
                    'Bucket' => $this->bucketName,
                    'Key' => $path,
                ]);
            }
        } catch (Exception $exception) {
            $this->logger->warning(sprintf('File %s not found in the storage', $path));
        }
    }
}