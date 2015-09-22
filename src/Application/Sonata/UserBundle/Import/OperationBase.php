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

class OperationBase {

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getPopulateRate(Base $base)
    {
        $em = $this->container->get('doctrine')->getManager();

        $results = $em->getRepository('ApplicationSonataUserBundle:BaseDetail')->countBaseDetailByIdBase($base->getId());

        $uploadRate = $results * 100 / $base->getRowCount();

        return round($uploadRate, 2);
    }
}