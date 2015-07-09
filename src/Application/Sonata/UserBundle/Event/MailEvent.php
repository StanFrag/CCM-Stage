<?php
namespace Application\Sonata\UserBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class MailEvent extends Event
{
    /** @var string */
    protected $userName = null;

    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    public function getUserName()
    {
        return $this->userName;
    }
}