<?php

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MatchingDetail
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class MatchingDetail
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
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\UserBundle\Entity\Matching", inversedBy="matchingDetail")
     * @ORM\JoinColumn(name="id_matching", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $id_matching;


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
     * @return MatchingDetail
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
     * Set id_matching
     *
     * @param \Application\Sonata\UserBundle\Entity\Matching $idMatching
     * @return MatchingDetail
     */
    public function setIdMatching(\Application\Sonata\UserBundle\Entity\Matching $idMatching = null)
    {
        $this->id_matching = $idMatching;

        return $this;
    }

    /**
     * Get id_matching
     *
     * @return \Application\Sonata\UserBundle\Entity\Matching 
     */
    public function getIdMatching()
    {
        return $this->id_matching;
    }
}
