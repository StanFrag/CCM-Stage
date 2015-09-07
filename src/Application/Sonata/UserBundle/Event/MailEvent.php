<?php
namespace Application\Sonata\UserBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class MailEvent extends Event
{
    /** @var string */
    protected $userName = null;
    protected $base = null;
    protected $campaign = null;
    protected $matchingId = null;

    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function setBase($base)
    {
        $this->base = $base;
    }

    public function getBase()
    {
        return $this->base;
    }

    public function setCampaign($campaign)
    {
        $this->campaign = $campaign;
    }

    public function getCampaign()
    {
        return $this->campaign;
    }

    public function setMatchingId($matchingId)
    {
        $this->matchingId = $matchingId;
    }

    public function getMatchingId()
    {
        return $this->matchingId;
    }
}