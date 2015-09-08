<?php

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 *
 * @ORM\Table(name="campaigns")
 * @ORM\Entity(repositoryClass="Application\Sonata\UserBundle\Entity\Repository\CampaignRepository")
 * @UniqueEntity(
 *      fields = {"title"},
 *      message = "Nom de Campagne déja utilisé, veuillez le modifier pour continuer l'upload de la base"
 * )
 * @ORM\HasLifecycleCallbacks()
 */
class Campaign
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=100, unique=true)
     * @Assert\NotBlank
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(name="description", type="string")
     */
    protected $description;

    /**
     * @var string
     * @ORM\Column(name="theme", type="string")
     */
    protected $theme;

    /**
     * @var string
     * @ORM\Column(name="remuneration_type", type="string")
     */
    protected $remuneration_type;

    /**
     * @var string
     * @ORM\Column(name="remuneration_amount", type="string")
     */
    protected $remuneration_amount;

    /**
     * @var string
     * @ORM\Column(name="object_sentence", type="string")
     */
    protected $object_sentence;

    /**
     * @var string
     * @ORM\Column(name="sender", type="string")
     */
    protected $sender;

    /**
     * @var \DateTime
     * @ORM\Column(name="begin_date", type="datetime")
     */
    protected $begin_date;

    /**
     * @var \DateTime
     * @ORM\Column(name="end_date", type="datetime")
     */
    protected $end_date;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\UserBundle\Entity\Base", inversedBy="campaign")
     * @ORM\JoinColumn(name="base_id", referencedColumnName="id")
     */
    protected $base;

    /**
     * @ORM\OneToMany(targetEntity="\Application\Sonata\UserBundle\Entity\Matching", mappedBy="campaign")
     *
     * @var ArrayCollection $match
     */
    protected $match;

    /**
     * @Assert\File(
     *      uploadErrorMessage = "le fichier n'a pas pu etre upload pour une raison inconnu, veuillez contacter l'administrateur du site"
     * )
     */
    public $img;

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
     * @var boolean
     * @ORM\Column(type="boolean", name="state")
     */
    protected $state = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    private $temp;


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
     * Set title
     *
     * @param string $title
     * @return string
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return string
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $created_at
     * @return \DateTime
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updated_at
     * @return \DateTime
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set state
     *
     * @param boolean $state
     * @return boolean
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return boolean 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Get match
     *
     * @return ArrayCollection
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * Set base
     *
     * @param \Application\Sonata\UserBundle\Entity\Base $base
     * @return Base
     */
    public function setBase(Base $base = null)
    {
        $this->base = $base;

        return $this;
    }

    /**
     * Get base
     *
     * @return \Application\Sonata\UserBundle\Entity\Base
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * Sets img.
     *
     * @param UploadedFile $img
     */
    public function setImg(UploadedFile $img = null)
    {
        $this->img = $img;

        // check if we have an old image path
        if (isset($this->path)) {
            $this->temp = $this->path;
            $this->path = null;
        } else {
            $this->path = 'initial';
        }
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return string
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
     * @ORM\PrePersist()
     */
    public function prePersist() {
        $this->setCreatedAt(new \DateTime("now"));
        $this->setUpdatedAt(new \DateTime("now"));
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate() {
        $this->setUpdatedAt(new \DateTime("now"));
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->match = new ArrayCollection();
    }

    /**
     * Set theme
     *
     * @param string $theme
     * @return string
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get theme
     *
     * @return string 
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Set remuneration_type
     *
     * @param string $remuneration_type
     * @return string
     */
    public function setRemunerationType($remuneration_type)
    {
        $this->remuneration_type = $remuneration_type;

        return $this;
    }

    /**
     * Get remuneration_type
     *
     * @return string 
     */
    public function getRemunerationType()
    {
        return $this->remuneration_type;
    }

    /**
     * Set remuneration_amount
     *
     * @param string $remuneration_amount
     * @return string
     */
    public function setRemunerationAmount($remuneration_amount)
    {
        $this->remuneration_amount = $remuneration_amount;

        return $this;
    }

    /**
     * Get remuneration_amount
     *
     * @return string 
     */
    public function getRemunerationAmount()
    {
        return $this->remuneration_amount;
    }

    /**
     * Set object_sentence
     *
     * @param string $object_sentence
     * @return string
     */
    public function setObjectSentence($object_sentence)
    {
        $this->object_sentence = $object_sentence;

        return $this;
    }

    /**
     * Get object_sentence
     *
     * @return string 
     */
    public function getObjectSentence()
    {
        return $this->object_sentence;
    }

    /**
     * Set sender
     *
     * @param string $sender
     * @return string
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return string 
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set begin_date
     *
     * @param \DateTime $begin_date
     * @return \DateTime
     */
    public function setBeginDate($begin_date)
    {
        $this->begin_date = $begin_date;

        return $this;
    }

    /**
     * Get begin_date
     *
     * @return \DateTime 
     */
    public function getBeginDate()
    {
        return $this->begin_date;
    }

    /**
     * Set end_date
     *
     * @param \DateTime $end_date
     * @return \DateTime
     */
    public function setEndDate($end_date)
    {
        $this->end_date = $end_date;

        return $this;
    }

    /**
     * Get end_date
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * Add match
     *
     * @param \Application\Sonata\UserBundle\Entity\Matching $match
     * @return Matching
     */
    public function addMatch(Matching $match)
    {
        $this->match[] = $match;
        return $this;
    }

    /**
     * Remove match
     *
     * @param \Application\Sonata\UserBundle\Entity\Matching $match
     */
    public function removeMatch(Matching $match)
    {
        $this->match->removeElement($match);
    }
}
