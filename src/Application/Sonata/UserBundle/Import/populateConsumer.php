<?php

namespace Application\Sonata\UserBundle\Import;

use Application\Sonata\UserBundle\Entity\BaseDetail;
use Doctrine\ORM\EntityManager;
use OldSound\RabbitMqBundle\RabbitMq\Consumer;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class populateConsumer implements ConsumerInterface{

    protected $em;

    protected $base;
    protected $campaign;

    public function __construct($container, EntityManager $em, Consumer $consumer)
    {
        $this->container = $container;
        $this->em = $em;
        $this->consumer = $consumer;
    }

    public function execute(AMQPMessage $msg)
    {
        // Decode message
        $object = unserialize($msg->body);

        if (isset($object['message']) && $object['message'] === 'shutdown') {
            $this->consumer->forceStopConsumer();
            return false;
        }

        // On verifie que les id recuperée ne sont pas null
        if(null == $object['base'] || null == $object['md5']){
            return false;
        }

        $base = $this->em
            ->getRepository('ApplicationSonataUserBundle:Base')
            ->find($object['base']);

        // On verifie que les id recuperée ne sont pas null
        if(null == $base){
            return false;
        }

        // Population de l'entité matching
        $responsePopulate = $this->populate($object['md5'], $base);

        return $responsePopulate;
    }

    protected function populate($md5, $base){

        // On crée un nouvelle obj Base Detail
        $baseDetail = new BaseDetail();

        // Et on rempli les variables de l'objet
        $baseDetail->setBase($base);
        $baseDetail->setMd5($md5);

        // Puis on persiste l'entité
        $this->em->persist($baseDetail);
        $this->em->flush();

        return true;
    }
}