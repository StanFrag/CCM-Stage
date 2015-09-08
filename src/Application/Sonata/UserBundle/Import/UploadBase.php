<?php

namespace Application\Sonata\UserBundle\Import;

use Application\Sonata\UserBundle\Entity\base;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class UploadBase {

    private $directory;

    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    public function update(base $base, $randomize = true)
    {
        $file = $base->getFile();

        if (!$file instanceof UploadedFile) {
            throw new \InvalidArgumentException(
                'There is no file to upload!'
            );
        }
        $fileName = $this->generateFilename($file, $randomize);

        $fs = new Filesystem();

        try {
            $fs->mkdir($this->directory, 0777);
        } catch (IOExceptionInterface $e) {
            echo "An error occurred while creating your directory at ".$e->getPath();
        }

        $base->setPath($fileName);
        $file->move($this->directory, $fileName);
    }

    public function remove(base $base)
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

    public function upload(base $base, $randomize = true)
    {
        $file = $base->getFile();

        if (!$file instanceof UploadedFile) {
            throw new \InvalidArgumentException(
                'There is no file to upload!'
            );
        }

        $fileName = $this->generateFilename($file, $randomize);

        $fs = new Filesystem();

        try {
            $fs->mkdir($this->directory, 0777);
        } catch (IOExceptionInterface $e) {
            echo "An error occurred while creating your directory at ".$e->getPath();
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