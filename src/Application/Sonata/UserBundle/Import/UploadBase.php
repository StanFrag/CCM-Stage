<?php

namespace Application\Sonata\UserBundle\Import;


use Application\Sonata\UserBundle\Entity\Base;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\File;

class UploadBase {

    private $directory;

    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    public function update(Base $base, $randomize = true)
    {
        $file = $base->getFile();

        if (!$file instanceof UploadedFile) {
            throw new \InvalidArgumentException(
                'There is no file to upload!'
            );
        }
        $fileName = $this->generateFilename($file, $randomize);

        // If the destination directory does not exist create it
        if(!is_dir($this->directory)) {
            if(!mkdir($this->directory, 0777, true)) {
                // If the destination directory could not be created stop processing
                throw new NotFoundHttpException("Impossible de créer le repertoire d'upload");
            }
        }

        $base->setPath($fileName);
        $file->move($this->directory, $fileName);
    }

    public function remove(Base $base)
    {
        $file = $base->getPath();

        if (!$file) {
            throw new \InvalidArgumentException(
                'There is no file to delete!'
            );
        }

        if (file_exists($this->directory)) {
            if ($file = $this->directory) {
                unlink($file);
            }
        }
    }

    public function upload(Base $base, $randomize = true)
    {
        $file = $base->getFile();

        if (!$file instanceof UploadedFile) {
            throw new \InvalidArgumentException(
                'There is no file to upload!'
            );
        }

        $fileName = $this->generateFilename($file, $randomize);

        // If the destination directory does not exist create it
        if(!is_dir($this->directory)) {
            if(!mkdir($this->directory, 0777, true)) {
                // If the destination directory could not be created stop processing
                throw new NotFoundHttpException("Impossible de créer le repertoire d'upload");
            }
        }

        $file->move($this->directory, $fileName);

        $base->setPath($fileName);
    }

    private function generateFilename($file, $randomize = true)
    {
        if ($randomize) {
            $filename = sprintf('%s.%s'
                , md5(uniqid($file, true)), 'csv');

        } else {
            $filename = sprintf('%s.%s', $file->getBasename(), 'csv');
        }

        return $filename;
    }
}