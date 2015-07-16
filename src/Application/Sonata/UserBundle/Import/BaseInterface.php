<?php

namespace Application\Sonata\UserBundle\Import;


interface BaseInterface {
    public function getAbsolutePath();
    public function getWebPath();
    public function getUploadRootDir();
    public function getUploadDir();
    public function removeUpload();
}