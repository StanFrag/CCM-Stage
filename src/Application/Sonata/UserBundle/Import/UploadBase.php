<?php

namespace Application\Sonata\UserBundle\Import;


use Application\Sonata\UserBundle\Entity\Base;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

        //$file->move($base->getUploadRootDir(), $fileName);
        $file->move($this->directory, $fileName);

        $base->setPath($fileName);
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

        //$file->move($base->getUploadRootDir(), $fileName);
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