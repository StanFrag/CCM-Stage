<?php

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Matching
 *
 * @ORM\Table()
 * @ORM\Entity
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
     * @ORM\OneToOne(targetEntity="Application\Sonata\UserBundle\Entity\Base", cascade={"persist"})
     */
    protected $id_base;

    /**
     * @ORM\OneToOne(targetEntity="Application\Sonata\UserBundle\Entity\Campaign", cascade={"persist"})
     */
    protected $id_campaign;

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
     * @param \DateTime $dateMaj
     * @return Matching
     */
    public function setDateMaj($dateMaj)
    {
        $this->date_maj = $dateMaj;

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
     * @param \Application\Sonata\UserBundle\Entity\Base $idBase
     * @return Matching
     */
    public function setIdBase(Base $idBase = null)
    {
        $this->id_base = $idBase;

        return $this;
    }

    /**
     * Get id_base
     *
     * @return \Application\Sonata\UserBundle\Entity\Base 
     */
    public function getIdBase()
    {
        return $this->id_base;
    }

    /**
     * Set id_campaign
     *
     * @param \Application\Sonata\UserBundle\Entity\Campaign $idCampaign
     * @return Matching
     */
    public function setIdCampaign(Campaign $idCampaign = null)
    {
        $this->id_campaign = $idCampaign;

        return $this;
    }

    /**
     * Get id_campaign
     *
     * @return \Application\Sonata\UserBundle\Entity\Campaign 
     */
    public function getIdCampaign()
    {
        return $this->id_campaign;
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
