<?php

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="matchings")
 * @ORM\Entity()
 * @ORM\Entity(repositoryClass="Application\Sonata\UserBundle\Entity\Repository\MatchingRepository")
 */
class Matching
{

    public function __construct()
    {
        $this->matching_detail = new ArrayCollection();
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
    private $updated_at;

    /**
     * @var integer
     *
     * @ORM\Column(name="match_count", type="integer")
     */
    private $match_count;

    /**
     * @ORM\OneToMany(targetEntity="\Application\Sonata\UserBundle\Entity\MatchingDetail", mappedBy="id_matching", cascade={"all"}, orphanRemoval=true)
     *
     * @var ArrayCollection $matching_detail
     */
    protected $matching_detail;

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
     * Set updated_at
     *
     * @return \DateTime
     */
    public function setUpdatedAt()
    {
        $this->updated_at = new \DateTime('now');;
        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set match_count
     *
     * @param integer $match_count
     * @return integer
     */
    public function setMatchCount($match_count)
    {
        $this->match_count = $match_count;

        return $this;
    }

    /**
     * Get match_count
     *
     * @return integer 
     */
    public function getMatchCount()
    {
        return $this->match_count;
    }

    /**
     * Set id_base
     *
     * @param \Application\Sonata\UserBundle\Entity\Base $base
     * @return Base
     */
    public function setBase(Base $base)
    {
        $this->base = $base;

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
     * @param \Application\Sonata\UserBundle\Entity\Campaign $campaign
     * @return Campaign
     */
    public function setCampaign(Campaign $campaign)
    {
        $this->campaign = $campaign;

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
     * Add matching_detail
     *
     * @param \Application\Sonata\UserBundle\Entity\MatchingDetail $matching_detail
     * @return Matching
     */
    public function addMatchingDetail(MatchingDetail $matching_detail)
    {
        $matching_detail->setIdMatching($this);
        $this->matching_detail[] = $matching_detail;
    }

    /**
     * Remove matching_detail
     *
     * @param \Application\Sonata\UserBundle\Entity\MatchingDetail $matching_detail
     */
    public function removeMatchingDetail(MatchingDetail $matching_detail)
    {
        $this->matching_detail->removeElement($matching_detail);
    }

    /**
     * Remove baseMatchingDetail
     */
    public function removeMatchingDetailAll()
    {
        $this->matching_detail->clear();
    }

    /**
     * Get matching_detail
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMatchingDetail()
    {
        return $this->matching_detail;
    }

    /**
     * Set state
     *
     * @param boolean $state
     * @return boolean
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
}
