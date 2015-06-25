<?php

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Base
 *
 * @ORM\Entity()
 * @ORM\Entity(repositoryClass="Application\Sonata\UserBundle\Entity\Repository\BaseRepository")
 * @UniqueEntity("title")
 * @ORM\HasLifecycleCallbacks()
 */
class Base
{

    protected $em;

    public function __construct()
    {
        $this->baseDetail = new ArrayCollection();
    }

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
     * @ORM\OneToMany(targetEntity="\Application\Sonata\UserBundle\Entity\BaseDetail", mappedBy="base", cascade={"all"}, orphanRemoval=true)
     *
     * @var ArrayCollection $baseDetail
     */
    protected $baseDetail;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=100, unique=true)
     * @Assert\NotBlank
     */
    protected $title;

    /**
     * @Assert\File(
     *      uploadErrorMessage = "le fichier n'a pas pu etre upload pour une raison inconnu, veuillez contacter l'administrateur du site",
     *      maxSize="1000M",
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
     * @ORM\Column(name="header", type="boolean", nullable=true)
     */
    protected $header;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbLine", type="integer", nullable=true)
     */
    protected $nbLine;

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
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        $this->setCreatedAt();
        $this->setModificatedAt();
        $this->setState(1);

        if (null !== $this->file) {
            // générer un nom unique
            $this->path = sha1(uniqid(mt_rand(), true)). $this->file->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     */
    public function upload()
    {
        if (!$this->file) {
            return;
        }

        // Move empeche l'entité de persisté en base de donnée si une erreur est recu
        $this->file->move($this->getUploadRootDir(), $this->path);

        // Clean up the file property as you won't need it anymore
        $this->file = null;
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

    /**
     * Get id
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get created_at
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set created_at
     * @return Base
     */
    public function setCreatedAt()
    {
        $this->created_at = new \DateTime('now');

        return $this;
    }

    /**
     * Get modificated_at
     * @return datetime
     */
    public function getModificatedAt()
    {
        return $this->modificated_at;
    }

    /**
     * Set modificated_at
     * @return Base
     */
    public function setModificatedAt()
    {
        $this->modificated_at = new \DateTime('now');

        return $this;
    }

    /**
     * Get delimiter
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

    /**
     * Add baseDetail
     *
     * @param BaseDetail $baseDetail
     */
    public function addBaseDetail(BaseDetail $baseDetail)
    {
        $baseDetail->setBase($this);
        $this->baseDetail[] = $baseDetail;
    }

    /**
     * Get baseDetail
     *
     * @return ArrayCollection $baseDetail
     */
    public function getBaseDetail()
    {
        return $this->baseDetail;
    }

    /**
     * Get state
     *
     * @return tinyint
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set state
     *
     * @param tinyint $state
     * @return Base
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get nbLine
     *
     * @return integer
     */
    public function getNbLine()
    {
        return $this->nbLine;
    }

    /**
     * Set nbLine
     *
     * @param integer $nbLine
     * @return Base
     */
    public function setNbLine($nbLine)
    {
        $this->nbLine = $nbLine;

        return $this;
    }

    public function __toString()
    {
        return $this->getTitle();
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

    /**
     * Remove baseDetail
     *
     * @param \Application\Sonata\UserBundle\Entity\BaseDetail $baseDetail
     */
    public function removeBaseDetail(BaseDetail $baseDetail)
    {
        $this->baseDetail->removeElement($baseDetail);
    }
}
