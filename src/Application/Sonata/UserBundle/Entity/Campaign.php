<?php

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Campaign
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Application\Sonata\UserBundle\Entity\CampaignRepository")
 * @UniqueEntity(
 *      fields = {"title"},
 *      message = "Nom de Campagne déja utilisé, veuillez le modifier pour continuer l'upload de la base"
 * )
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
     * @ORM\OneToOne(targetEntity="Application\Sonata\UserBundle\Entity\Base", cascade={"persist"})
     */
    protected $base;

    /**
     * @Assert\File(
     *      uploadErrorMessage = "le fichier n'a pas pu etre upload pour une raison inconnu, veuillez contacter l'administrateur du site"
     * )
     */
    public $img;

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
     * @ORM\Column(type="boolean", name="state")
     */
    protected $state = false;


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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Campaign
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set modificated_at
     *
     * @param \DateTime $modificatedAt
     * @return Campaign
     */
    public function setModificatedAt($modificatedAt)
    {
        $this->modificated_at = $modificatedAt;

        return $this;
    }

    /**
     * Get modificated_at
     *
     * @return \DateTime 
     */
    public function getModificatedAt()
    {
        return $this->modificated_at;
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
}
