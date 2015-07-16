<?php

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Application\Sonata\UserBundle\Import\BaseInterface;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks()
 */
abstract class AbstractBase implements BaseInterface {

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    public function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__ . '/../../../../www/' . $this->getUploadDir();
        //return '/var/nas/static/r-target.com/'.$this->getUploadDir();
    }

    public function getUploadDir()
    {
        return 'upload';
    }

    /**
     * @ORM\PostRemove
     */
    public function removeUpload()
    {
        if (file_exists($this->getAbsolutePath())) {
            if ($file = $this->getAbsolutePath()) {
                unlink($file);
            }
        }
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Base
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

}