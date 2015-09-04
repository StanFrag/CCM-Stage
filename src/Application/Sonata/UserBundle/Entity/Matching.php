<?php

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Matching
 *
 * @ORM\Entity()
 * @ORM\Entity(repositoryClass="Application\Sonata\UserBundle\Entity\Repository\MatchingRepository")
 */
class Matching
{

    public function __construct()
    {
        $this->matchingDetail = new ArrayCollection();
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\UserBundle\Entity\Base", inversedBy="match")
     * @ORM\JoinColumn(name="base_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $base;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\UserBundle\Entity\Campaign", inversedBy="match")
     * @ORM\JoinColumn(name="campaign_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $campaign;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_maj", type="datetime")
     */
    private $date_maj;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_match", type="integer")
     */
    private $nb_match;

    /**
     * @ORM\OneToMany(targetEntity="\Application\Sonata\UserBundle\Entity\MatchingDetail", mappedBy="id_matching", cascade={"all"}, orphanRemoval=true)
     *
     * @var ArrayCollection $matchingDetail
     */
    protected $matchingDetail;


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
     * Set date_maj
     *
     * @return Matching
     */
    public function setDateMaj()
    {
        $this->date_maj = new \DateTime('now');;

        return $this;
    }

    /**
     * Get date_maj
     *
     * @return \DateTime 
     */
    public function getDateMaj()
    {
        return $this->date_maj;
    }

    /**
     * Set nb_match
     *
     * @param integer $nbMatch
     * @return Matching
     */
    public function setNbMatch($nbMatch)
    {
        $this->nb_match = $nbMatch;

        return $this;
    }

    /**
     * Get nombre_adresses_matchees
     *
     * @return integer 
     */
    public function getNbMatch()
    {
        return $this->nb_match;
    }

    /**
     * Set id_base
     *
     * @param \Application\Sonata\UserBundle\Entity\Base $Base
     * @return Matching
     */
    public function setBase(Base $Base)
    {
        $this->base = $Base;

        return $this;
    }

    /**
     * Get id_base
     *
     * @return \Application\Sonata\UserBundle\Entity\Base 
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * Set id_campaign
     *
     * @param \Application\Sonata\UserBundle\Entity\Campaign $Campaign
     * @return Matching
     */
    public function setCampaign(Campaign $Campaign)
    {
        $this->campaign = $Campaign;

        return $this;
    }

    /**
     * Get id_campaign
     *
     * @return \Application\Sonata\UserBundle\Entity\Campaign 
     */
    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * Add matchingDetail
     *
     * @param \Application\Sonata\UserBundle\Entity\MatchingDetail $matchingDetail
     * @return Matching
     */
    public function addMatchingDetail(MatchingDetail $matchingDetail)
    {
        $matchingDetail->setIdMatching($this);
        $this->matchingDetail[] = $matchingDetail;
    }

    /**
     * Remove matchingDetail
     *
     * @param \Application\Sonata\UserBundle\Entity\MatchingDetail $matchingDetail
     */
    public function removeMatchingDetail(MatchingDetail $matchingDetail)
    {
        $this->matchingDetail->removeElement($matchingDetail);
    }

    /**
     * Remove baseMatchingDetail
     *
     */
    public function removeMatchingDetailAll()
    {
        $this->matchingDetail->clear();
    }

    /**
     * Get matchingDetail
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMatchingDetail()
    {
        return $this->matchingDetail;
    }
}
