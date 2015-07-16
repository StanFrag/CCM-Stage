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
 * @UniqueEntity(
 *      fields = {"title"},
 *      message = "Nom de base déja utilisé, veuillez le modifier pour continuer l'upload de la base"
 * )
 * @ORM\HasLifecycleCallbacks()
 */
class Base extends AbstractBase
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
     *      uploadErrorMessage = "le fichier n'a pas pu etre uploadé pour une raison inconnu, veuillez contacter l'administrateur du site",
     *      maxSize="1000M",
     * )
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
     * @var integer
     *
     * @ORM\Column(name="nbLine", type="integer", nullable=true)
     */
    protected $nbLine;

    /**
     * @ORM\Column(columnDefinition="tinyint UNSIGNED DEFAULT '1'", name="state")
     */
    protected $state = 2;

    /**
     * @ORM\PrePersist()
     */
    public function preUpload()
    {
        $this->setCreatedAt();
        $this->setModificatedAt();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->setModificatedAt();
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
     * Remove baseDetail
     *
     * @param \Application\Sonata\UserBundle\Entity\BaseDetail $baseDetail
     */
    public function removeBaseDetail(BaseDetail $baseDetail)
    {
        $this->baseDetail->removeElement($baseDetail);
    }

    /**
     * Remove baseDetailAll
     *
     */
    public function removeBaseDetailAll()
    {
        $this->baseDetail->clear();
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

    /**
     * Get file
     *
     * @return Assert\File
     */
    public function getFile()
    {
        return $this->file;
    }

    public static function getStateList()
    {
        return array(
            '1' => "Accepté",
            '2' => "En attente",
            '0' => "Refusé"
        );
    }

    public function __toString()
    {
        return $this->getTitle();
    }
}
