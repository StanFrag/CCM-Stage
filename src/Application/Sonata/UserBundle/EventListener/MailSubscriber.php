<?php

namespace Application\Sonata\UserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Application\Sonata\UserBundle\ApplicationEvents;
use Application\Sonata\UserBundle\Event\MailEvent;

class MailSubscriber implements EventSubscriberInterface
{

    protected $mailer;
    protected $twig;

    public function __construct(\Twig_Environment $twig, \Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public static function getSubscribedEvents()
    {
        // Liste des évènements écoutés et méthodes à appeler
        return array(
            ApplicationEvents::AFTER_REGISTER => 'sentMailAfterRegister',
            ApplicationEvents::AFTER_POSTULATE => 'sentMailAfterPostulate'
        );
    }

    public function sentMailAfterPostulate(MailEvent $event)
    {
        $userName = $event->getUserName();
        $base = $event->getBase();
        $campaign = $event->getCampaign();
        $matchId = $event->getMatchingId();

        $body = $this->renderPostulateTemplate($userName, $base, $campaign, $matchId);

        $message = \Swift_Message::newInstance()
            ->setSubject('L\'utilisateur ' .$userName. ' souhaite postuler à une base')
            ->setFrom('admin@r-target.com')
            ->setTo('admin@r-target.com')
            ->setBody($body)
        ;

        $this->mailer->send($message);

        return true;
    }

    public function sentMailAfterRegister(MailEvent $event)
    {
        $userName = $event->getUserName();

        $body = $this->renderRegisterTemplate($userName);

        $message = \Swift_Message::newInstance()
            ->setSubject('Compte de l\'utilisateur ' .$userName. ' crée, en attente d\'etre dévèrouillé')
            ->setFrom('admin@r-target.com')
            ->setTo('admin@r-target.com')
            ->setBody($body)
        ;

        $this->mailer->send($message);

        return true;
    }


    public function renderPostulateTemplate($name, $base, $campaign, $matchId)
    {
        return $this->twig->render(
            'mail/postulate_mail.html.twig',
            array(
                'name' => $name,
                'base' => $base,
                'campaign' => $campaign,
                'matchId' => $matchId
            )
        );
    }

    public function renderRegisterTemplate($name)
    {
        return $this->twig->render(
            'mail/mail.html.twig',
            array(
                'name' => $name
            )
        );
    }

}