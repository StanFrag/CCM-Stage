<?php

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use Doctrine\ORM\EntityManager;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use \Application\Sonata\UserBundle\Entity\Matching;

class dbConsumer implements ConsumerInterface{

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function execute(AMQPMessage $msg)
    {
        // Decode message
        $object = json_decode($msg);

        if(null == $object->campaign || null == $object->base){
            return;
        }

        $base = $this->em
            ->getRepository('ApplicationSonataUserBundle:Base')
            ->find($object->base);

        //Get base detail
        $baseDetails = $this->em
            ->getRepository('ApplicationSonataUserBundle:BaseDetail')
            ->findBy(
                array('base' => $base)
            );

        if (!$baseDetails) {
            throw new NotFoundHttpException(
                'Aucun detail de base trouvé pour cet id : '. $object->base
            );
        }

        //Get campagne details
        $campaign = $this->em
            ->getRepository('ApplicationSonataUserBundle:Campaign')
            ->find($object->campaign);

        $baseFromCampaign = $campaign->getBase();

        $repositoryBaseFromCampaign = $this->em
            ->getRepository('ApplicationSonataUserBundle:Base')
            ->find($baseFromCampaign->getId());

        // On recupere les details de la base ciblé
        $baseDetailsFromCampaign = $repositoryBaseDetail->findBy(
            array('base' => $repositoryBaseFromCampaign)
        );

        if (!$baseDetailsFromCampaign) {
            throw new NotFoundHttpException(
                'Aucun detail de base trouvé pour cet id : '. $object->campaign
            );
        }

        //Match
        $dataMatch = $this->match($baseDetails, $baseDetailsFromCampaign);

        //Populate BD
        $this->populate($dataMatch);
    }

    protected function match(Array $firstDB, Array $secondDB){

        if(null == $firstDB || null == $firstDB){
            throw new NotFoundHttpException(
                'Problème durant le matching des deux bases.'
            );
        }

        $result =  array_intersect($firstDB, $secondDB);

        return $result;
    }

    protected function populate(Array $data){
        $match = new Matching();

        $match->setIdBase($response);
        $match->setIdCampaign($user);
    }
}