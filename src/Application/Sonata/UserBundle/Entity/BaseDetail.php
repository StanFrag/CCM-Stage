<?php

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BaseDetail
 *
 * @ORM\Table()
 * @ORM\Entity
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
     * @ORM\Column(name="md5", type="string", length=100)
     */
    private $md5;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\UserBundle\Entity\Base", inversedBy="baseDetail")
     * @ORM\JoinColumn(name="base_id", referencedColumnName="id")
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
     * @return BaseDetail
     */
    public function setMd5($md5)
    {
        $this->md5 = $md5;

        return $this;
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
     * Set base
     *
     * @param \Application\Sonata\UserBundle\Entity\Base $base
     */
    public function setBase(Base $base)
    {
        $this->base = $base;
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

    public function __toString()
    {
        return $this->getMd5();
    }
}
