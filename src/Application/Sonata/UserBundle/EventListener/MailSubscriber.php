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
            ApplicationEvents::AFTER_REGISTER => 'sentMailToAdmin'
        );
    }

    public function sentMailToAdmin(MailEvent $event)
    {
        $userName = $event->getUserName();

        $body = $this->renderTemplate($userName);

        $message = \Swift_Message::newInstance()
            ->setSubject('Compte de l\'utilisateur ' .$userName. ' crée, en attente d\'etre dévèrouillé')
            ->setFrom('admin@r-target.com')
            ->setTo('admin@r-target.com')
            ->setBody($body)
        ;

        $this->mailer->send($message);
    }

    public function renderTemplate($name)
    {
        return $this->twig->render(
            'mail/mail.html.twig',
            array(
                'name' => $name
            )
        );
    }

}