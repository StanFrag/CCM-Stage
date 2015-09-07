<?php

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Campaign
 *
 * @ORM\Table()
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
     * @ORM\Column(name="remunerationType", type="string")
     */
    protected $remunerationType;

    /**
     * @var string
     * @ORM\Column(name="remunerationAmount", type="string")
     */
    protected $remunerationAmount;

    /**
     * @var string
     * @ORM\Column(name="objectSentence", type="string")
     */
    protected $objectSentence;

    /**
     * @var string
     * @ORM\Column(name="sender", type="string")
     */
    protected $sender;

    /**
     * @var datetime
     * @ORM\Column(name="beginDate", type="datetime")
     */
    protected $beginDate;

    /**
     * @var datetime
     * @ORM\Column(name="endDate", type="datetime")
     */
    protected $endDate;

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
     * @var datetime
     * @ORM\Column(name="createdAt", type="datetime")
     */
    protected $createdAt;

    /**
     * @var datetime
     * @ORM\Column(name="modificatedAt", type="datetime")
     */
    protected $modificatedAt;

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
     * @return Campaign
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
     * Set description
     *
     * @param string $description
     * @return Campaign
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Campaign
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set modificatedAt
     *
     * @param \DateTime $modificatedAt
     * @return Campaign
     */
    public function setModificatedAt($modificatedAt)
    {
        $this->modificatedAt = $modificatedAt;

        return $this;
    }

    /**
     * Get modificated_at
     *
     * @return \DateTime 
     */
    public function getModificatedAt()
    {
        return $this->modificatedAt;
    }

    /**
     * Set state
     *
     * @param boolean $state
     * @return Campaign
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
     * @return Campaign
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
     * @ORM\PrePersist()
     */
    public function prePersist() {
        $this->setModificatedAt(new \DateTime("now"));
        $this->setCreatedAt(new \DateTime("now"));
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate() {
        $this->setModificatedAt(new \DateTime("now"));
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
        $this->match = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set theme
     *
     * @param string $theme
     * @return Campaign
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
     * Set remunerationType
     *
     * @param string $remunerationType
     * @return Campaign
     */
    public function setRemunerationType($remunerationType)
    {
        $this->remunerationType = $remunerationType;

        return $this;
    }

    /**
     * Get remunerationType
     *
     * @return string 
     */
    public function getRemunerationType()
    {
        return $this->remunerationType;
    }

    /**
     * Set remunerationAmount
     *
     * @param string $remunerationAmount
     * @return Campaign
     */
    public function setRemunerationAmount($remunerationAmount)
    {
        $this->remunerationAmount = $remunerationAmount;

        return $this;
    }

    /**
     * Get remunerationAmount
     *
     * @return string 
     */
    public function getRemunerationAmount()
    {
        return $this->remunerationAmount;
    }

    /**
     * Set objectSentence
     *
     * @param string $objectSentence
     * @return Campaign
     */
    public function setObjectSentence($objectSentence)
    {
        $this->objectSentence = $objectSentence;

        return $this;
    }

    /**
     * Get objectSentence
     *
     * @return string 
     */
    public function getObjectSentence()
    {
        return $this->objectSentence;
    }

    /**
     * Set sender
     *
     * @param string $sender
     * @return Campaign
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
     * Set beginDate
     *
     * @param \DateTime $beginDate
     * @return Campaign
     */
    public function setBeginDate($beginDate)
    {
        $this->beginDate = $beginDate;

        return $this;
    }

    /**
     * Get beginDate
     *
     * @return \DateTime 
     */
    public function getBeginDate()
    {
        return $this->beginDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Campaign
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Add match
     *
     * @param \Application\Sonata\UserBundle\Entity\Matching $match
     * @return Campaign
     */
    public function addMatch(\Application\Sonata\UserBundle\Entity\Matching $match)
    {
        $this->match[] = $match;

        return $this;
    }

    /**
     * Remove match
     *
     * @param \Application\Sonata\UserBundle\Entity\Matching $match
     */
    public function removeMatch(\Application\Sonata\UserBundle\Entity\Matching $match)
    {
        $this->match->removeElement($match);
    }
}
