<?php

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="base_details")
 * @ORM\Entity()
 * @ORM\Entity(repositoryClass="Application\Sonata\UserBundle\Entity\Repository\BaseDetailRepository")
 */
class BaseDetail
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
     *
     * @ORM\Column(name="md5", type="string", length=32)
     */
    private $md5;

    /**
     * @ORM\ManyToOne(targetEntity="Base", inversedBy="base_detail")
     * @ORM\JoinColumn(name="fk_base", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $base;

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
     * Set md5
     *
     * @param string $md5
     */
    public function setMd5($md5)
    {
        $this->md5 = $md5;
    }

    /**
     * Get md5
     *
     * @return string 
     */
    public function getMd5()
    {
        return $this->md5;
    }

    /**
     * Set Base
     *
     * @param \Application\Sonata\UserBundle\Entity\Base $base
     */
    public function setBase(Base $base)
    {
        $this->base = $base;
    }

    /**
     * Get Base
     *
     * @return \Application\Sonata\UserBundle\Entity\Base
     */
    public function getBase()
    {
        return $this->base;
    }

    public function __toString()
    {
        return $this->getMd5();
    }
}
