<?php

namespace App\Service;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{

    /**
     * @var SluggerInterface
     */
    private $slugger;
    /**
     * @var FilesystemOperator
     */
    private $filesystem;

    public function __construct(FilesystemOperator $uploadsCertificatesFilesystem, SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
        $this->filesystem = $uploadsCertificatesFilesystem;
    }

    /**
     * @throws FilesystemException
     * @throws \Exception
     */
    public function uploadFile(File $file, ?string $oldFileName = null): string
    {
        $fileName = $this->slugger
            ->slug(pathinfo($file instanceof UploadedFile ? $file->getClientOriginalName() : $file->getFilename(), PATHINFO_FILENAME))
            ->append('-' . uniqid())
            ->append('.' . $file->guessExtension())
            ->toString();

        $stream = fopen($file->getPathname(), 'r');

        try {
            $this->filesystem->writeStream($fileName, $stream);
        } catch (\Exception $e) {
            throw new \Exception("Не удалось записать файл: $fileName");
        }

        if (is_resource($stream)) {
            fclose($stream);
        }

        if ($oldFileName && $this->filesystem->fileExists($oldFileName)) {
            try {
                $this->filesystem->delete($oldFileName);
            }
            catch (\Exception $e) {
                throw new \Exception("Ошибка удаления файла: $oldFileName");
            }
        }
        return $fileName;
    }

}
