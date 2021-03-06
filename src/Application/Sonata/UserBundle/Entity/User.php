<?php

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="fos_user_user", options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="Application\Sonata\UserBundle\Entity\Repository\UserRepository")
 */
class User extends BaseUser
{
    public function __construct()
    {
        parent::__construct();

        $this->base = new ArrayCollection();
        $this->campaigns = new ArrayCollection();
        $this->locked = true;
        $this->enabled = false;
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var
     *
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="users")
     * @ORM\JoinTable(name="users_groups")
     */
    protected $groups;

    /**
     * @ORM\OneToMany(targetEntity="\Application\Sonata\UserBundle\Entity\Base", mappedBy="user", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"created_at" = "DESC"})
     *
     * @var ArrayCollection $base
     */
    protected $base;

    /**
     * @ORM\ManyToMany(targetEntity="\Application\Sonata\UserBundle\Entity\Campaign", inversedBy="users", cascade={"persist", "merge"})
     * @ORM\JoinColumn(name="campaigns_id", referencedColumnName="id")
     */
    protected $campaigns;

    /**
     * @var string
     *
     * @ORM\Column(name="company", type="string", length=255)
     */
    protected $company;

    /**
     * @var string
     *
     * @ORM\Column(name="legalSituation", type="string", length=255)
     */
    protected $legalSituation;

    /**
     * @var string
     *
     * @ORM\Column(name="phoneNumber", type="string", length=10)
     * @Assert\Regex(
     *     pattern="/^((\+|00)33\s?|0)[1-9](\s?\d{2}){4}$/",
     *     match=true,
     *     message="Votre numéro de téléphone doit etre valide"
     * )
     */
    protected $phoneNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string")
     * @Assert\Regex(
     *     pattern="@^(http\:\/\/|https\:\/\/)?([a-z0-9][a-z0-9\-]*\.)+[a-z0-9][a-z0-9\-]*$@i",
     *     match=true,
     *     message="L'adresse du site web doit être valide"
     * )
     */
    protected $url;

    /**
     * @Assert\Regex(
     *  pattern="/^(?=.*[!@#$%])[0-9a-zA-Z!@#$%0-9]{8,}$/",
     *  message="Le mot de passe doit contenir au minimum 8 caractères et au moins un caractère spécial."
     * )
     */
    protected $plainPassword;

    public function getId()
    {
        return $this->id;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function setCompany($company)
    {
        $this->company = $company;
    }

    public function setCampaigns($campaign)
    {
        $this->campaigns = new \Doctrine\Common\Collections\ArrayCollection();
        $this->campaigns[] = $campaign;

        return $this;
    }

    /**
     * Get campaigns
     *
     * @return ArrayCollection
     */
    public function getCampaigns()
    {
        return $this->campaigns;
    }

    public function getLegalSituation()
    {
        return $this->legalSituation;
    }

    public function setLegalSituation($legalSituation)
    {
        $this->legalSituation = $legalSituation;
    }

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setBase($bases)
    {
        if (count($bases) > 0) {
            foreach ($bases as $i) {
                $this->addBase($i);
            }
        }

        return $this;
    }

    public function addBase(Base $base)
    {
        $base->setUser($this);
        $this->base->add($base);
    }

    public function getBase()
    {
        return $this->base;
    }

    /**
     * Remove base
     *
     * @param \Application\Sonata\UserBundle\Entity\base $base
     */
    public function removeBase(base $base)
    {
        $this->base->removeElement($base);
    }
}
