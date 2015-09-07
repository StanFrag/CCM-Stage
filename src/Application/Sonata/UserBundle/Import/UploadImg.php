<?php

namespace Application\Sonata\UserBundle\Import;

use Application\Sonata\UserBundle\Entity\Campaign;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class UploadImg {

    private $directory;

    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    public function update(Campaign $campaign, $randomize = true)
    {
        $file = $campaign->getImg();

        if (!$file instanceof UploadedFile) {
            return;
        }else {
            $fileName = $this->generateFilename($file, $randomize);

            $fs = new Filesystem();

            try {
                $fs->mkdir($this->directory, 0777);
            } catch (IOExceptionInterface $e) {
                echo "An error occurred while creating your directory at ".$e->getPath();
            }

            $campaign->setPath($fileName);
            $file->move($this->directory, $fileName);
        }
    }

    public function remove(Campaign $campaign)
    {
        $file = $campaign->getPath();

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

    public function upload(Campaign $campaign, $randomize = true)
    {
        $file = $campaign->getImg();

        if (!$file instanceof UploadedFile) {
            return;
        }else{
            $fileName = $this->generateFilename($file, $randomize);

            $fs = new Filesystem();

            try {
                $fs->mkdir($this->directory, 0777);
            } catch (IOExceptionInterface $e) {
                echo "An error occurred while creating your directory at ".$e->getPath();
            }

            $file->move($this->directory, $fileName);

            $campaign->setPath($fileName);
        }
    }

    private function generateFilename($file, $randomize = true)
    {
        if ($randomize) {
            $filename = sprintf('%s.%s'
                , md5(uniqid($file, true)), $file->guessExtension());

        } else {
            $filename = sprintf('%s.%s', $file->getBasename(), $file->guessExtension());
        }

        return $filename;
    }
}