<?php

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Base
 *
 * @ORM\Entity()
 * @ORM\Entity(repositoryClass="Application\Sonata\UserBundle\Entity\Repository\BaseRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Base
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\UserBundle\Entity\User", inversedBy="base")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=100)
     * @Assert\NotBlank
     */
    protected $title;

    /**
     * @Assert\File(
     *      mimeTypes = {"text/csv"},
     *      mimeTypesMessage = "ce format de fichier n'est pas reconnu, veuillez uploader un fichier CSV.",
     *      uploadErrorMessage = "le fichier n'a pas pu etre upload pour une raison inconnu, veuillez contacter l'administrateur du site"
     * )
     * @Assert\NotBlank
     */
    public $file;

    /**
     * @var datetime
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $created_at;

    /**
     * @var datetime
     * @ORM\Column(name="modificated_at", type="datetime")
     */
    protected $modificated_at;

    /**
     * @var boolean
     * @ORM\Column(name="header", type="boolean")
     */
    protected $header;

    /**
     * @var string
     *
     * @ORM\Column(name="nb_line", type="integer", nullable=true)
     */
    protected $nb_line;

    /**
     * @var string
     *
     * @ORM\Column(name="delimiter", type="string", length=1)
     */
    protected $delimiter;

    /**
     * @ORM\Column(columnDefinition="tinyint UNSIGNED DEFAULT '1'", name="state")
     */
    protected $state;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function prePersist()
    {
        //using Doctrine DateTime here
        $this->created_at = new \DateTime('now');
        $this->modificated_at = new \DateTime('now');
        $this->state = 1;

        if (null !== $this->file) {
            $this->path = sha1(uniqid(mt_rand(), true)).'.csv';
        }
    }

    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     */
    public function upload()
    {
        // la propriété « file » peut être vide si le champ n'est pas requis
        if (null === $this->file) {
            return;
        }

        // s'il y a une erreur lors du déplacement du fichier, une exception
        // va automatiquement être lancée par la méthode move(). Cela va empêcher
        // proprement l'entité d'être persistée dans la base de données si
        // erreur il y a
        $this->file->move($this->getUploadRootDir(), $this->path);

        unset($this->file);
    }

    /**
     * @ORM\PostRemove
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get delimiter
     *
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * Set delimiter
     *
     * @param string $delimiter
     * @return Base
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * Get header
     *
     * @return boolean
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Set header
     *
     * @param boolean $header
     * @return Base
     */
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Set user
     *
     * @param \Application\Sonata\UserBundle\Entity\User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return \Application\Sonata\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Base
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    protected function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document dans la vue.
        return 'uploads/documents';
    }
}
