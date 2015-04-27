<?php

namespace Application\Sonata\UserBundle\Entity;

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
        // your own logic
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
     * @var
     *
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="users")
     * @ORM\JoinTable(name="users_groups")
     */
    protected $groups;

    /**
     * @var string $company
     *
     * @ORM\Column(name="company", type="string", length=255)
     */
    protected $company;

    /**
     * @var string $legalSituation
     *
     * @ORM\Column(name="legalSituation", type="string", length=255)
     */
    protected $legalSituation;

    /**
     * @var string $activityType
     *
     * @ORM\Column(name="activityType", type="string", length=255)
     */
    protected $activityType;

    /**
     * @var string $phoneNumber
     *
     * @ORM\Column(name="phoneNumber", type="integer")
     */
    protected $phoneNumber;

    /**
     * @var string $url
     *
     * @ORM\Column(name="url", type="string")
     */
    protected $url;


    public function getId()
    {
        return $this->id;
    }

    public function getLogin() {
        return $this->login;
    }

    public function setLogin($login) {
        $this->login = $login;
    }

    public function getCompany() {
        return $this->company;
    }

    public function setCompany($company) {
        $this->company = $company;
    }

    public function getLegalSituation() {
        return $this->legalSituation;
    }

    public function setLegalSituation($legalSituation) {
        $this->legalSituation = $legalSituation;
    }

    public function getActivityType() {
        return $this->activityType;
    }

    public function setActivityType($activityType) {
        $this->activityType = $activityType;
    }

    public function getPhoneNumber() {
        return $this->phoneNumber;
    }

    public function setPhoneNumber($phoneNumber) {
        $this->phoneNumber = $phoneNumber;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }
}
