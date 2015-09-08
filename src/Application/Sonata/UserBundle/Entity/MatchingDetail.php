<?php

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="matching_details")
 * @ORM\Entity()
 * @ORM\Entity(repositoryClass="Application\Sonata\UserBundle\Entity\Repository\MatchingDetailRepository")
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
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\UserBundle\Entity\Matching", inversedBy="matching_detail")
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
     * @param \Application\Sonata\UserBundle\Entity\Matching $id_matching
     * @return MatchingDetail
     */
    public function setIdMatching(Matching $id_matching = null)
    {
        $this->id_matching = $id_matching;

        return $this;
    }

    /**
     * Get id_matching
     *
     * @return Matching
     */
    public function getIdMatching()
    {
        return $this->id_matching;
    }

    public function __toString()
    {
        return $this->getMd5();
    }

}
