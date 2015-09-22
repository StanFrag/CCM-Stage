<?php

namespace Application\Sonata\UserBundle\Import;

use Application\Sonata\UserBundle\Entity\Base;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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

    public function update(Base $base, $oldFile, $randomize = true)
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
            $fs->remove($this->directory.'/'.$oldFile);
            $fs->mkdir($this->directory, 0777);
            $file->move($this->directory, $fileName);
        } catch (IOExceptionInterface $e) {
            throw new IOException($e->getMessage());
        } catch (FileException $e) {
            throw new FileException($e->getMessage());
        } catch(\Exception $e){
            throw new Exception($e->getMessage());
        }

        $base->setPath($fileName);

        return $fileName;
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

        $fs = new Filesystem();

        try {
            $fs->mkdir($this->directory, 0777);
            $file->move($this->directory, $fileName);
        } catch (IOExceptionInterface $e) {
            throw new IOException($e->getMessage());
        } catch (FileException $e) {
            throw new FileException($e->getMessage());
        } catch(\Exception $e){
            throw new Exception($e->getMessage());
        }

        $base->setPath($fileName);

        return $fileName;
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