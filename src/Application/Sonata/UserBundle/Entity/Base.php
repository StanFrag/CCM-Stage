<?php

namespace Application\Sonata\UserBundle\Entity;

use Application\Sonata\UserBundle\Entity\BaseDetail;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 *
 * @ORM\Table(name="bases")
 * @ORM\Entity(repositoryClass="Application\Sonata\UserBundle\Entity\Repository\BaseRepository")
 * @UniqueEntity(
 *      fields = {"title"},
 *      message = "Nom de base déja utilisé, veuillez le modifier pour continuer l'upload de la base"
 * )
 * @ORM\HasLifecycleCallbacks()
 */
class Base
{

    protected $em;

    public function __construct()
    {
        $this->base_details = new ArrayCollection();
    }

    /**
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\UserBundle\Entity\User", inversedBy="base")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="\Application\Sonata\UserBundle\Entity\Matching", mappedBy="base")
     * @var ArrayCollection $match
     */
    protected $match;

    /**
     * @var ArrayCollection $base_detail
     * @ORM\OneToMany(targetEntity="\Application\Sonata\UserBundle\Entity\BaseDetail", mappedBy="base", cascade={"all"}, orphanRemoval=true)
     */
    protected $base_detail;

    /**
     * @ORM\OneToMany(targetEntity="\Application\Sonata\UserBundle\Entity\Campaign", mappedBy="base")
     *
     * @var ArrayCollection $campaign
     */
    protected $campaign;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=100, unique=true)
     * @Assert\NotBlank
     */
    protected $title;

    /**
     * @Assert\File(
     *      uploadErrorMessage = "le fichier n'a pas pu etre uploadé pour une raison inconnu, veuillez contacter l'administrateur du site",
     *      maxSize="1000M",
     * )
     */
    public $file;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $created_at;

    /**
     * @var \DateTime
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updated_at;

    /**
     * @var integer
     *
     * @ORM\Column(name="row_count", type="integer", nullable=true)
     */
    protected $row_count;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @ORM\PrePersist()
     */
    public function preUpload()
    {
        $this->setCreatedAt();
        $this->setUpdatedAt();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->setUpdatedAt();
        $this->removeBaseDetailAll();
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
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set created_at
     */
    public function setCreatedAt()
    {
        $this->created_at = new \DateTime('now');
    }

    /**
     * Get updated_at
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set updated_at
     */
    public function setUpdatedAt()
    {
        $this->updated_at = new \DateTime('now');
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
     * @return base
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
     * Add base_detail
     *
     * @param BaseDetail $base_detail
     */
    public function addBaseDetail(BaseDetail $base_detail)
    {
        $base_detail->setIdBase($this);
        $this->base_detail[] = $base_detail;
    }

    /**
     * Get base_detail
     *
     * @return ArrayCollection $base_detail
     */
    public function getBaseDetail()
    {
        return $this->base_detail;
    }

    /**
     * Get campaign
     *
     * @return ArrayCollection $campaign
     */
    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * Remove base_detail
     *
     * @param \Application\Sonata\UserBundle\Entity\BaseDetail $base_detail
     */
    public function removeBaseDetail(BaseDetail $base_detail)
    {
        $this->base_detail->removeElement($base_detail);
    }

    /**
     * Remove base_details_all
     *
     */
    public function removeBaseDetailAll()
    {
        $this->base_detail->clear();
    }

    /**
     * Get row_count
     *
     * @return integer
     */
    public function getRowCount()
    {
        return $this->row_count;
    }

    /**
     * Set row_count
     *
     * @param integer $row_count
     */
    public function setRowCount($row_count)
    {
        $this->row_count = $row_count;
    }

    /**
     * Get file
     *
     * @return Assert\File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set path
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
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

    public function __toString()
    {
        return $this->getTitle();
    }
}
