<?php

namespace Application\Sonata\UserBundle\Match;

use Application\Sonata\UserBundle\Entity\MatchingDetail;
use Doctrine\ORM\EntityManager;
use OldSound\RabbitMqBundle\RabbitMq\Consumer;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use \Application\Sonata\UserBundle\Entity\Matching;

class dbConsumer implements ConsumerInterface{

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
            return true;
        }

        // On verifie que les id recuperée ne sont pas null
        if(null == $object['campaign'] || null == $object['base']){
            return true;
        }

        // On recupere la base liée a l'id recuperée
        $this->base = $this->em
            ->getRepository('ApplicationSonataUserBundle:Base')
            ->find($object['base']);

        //Get base detail from base
        $baseDetailsTmp = $this->em
            ->getRepository('ApplicationSonataUserBundle:BaseDetail')
            ->findBy(
                array('base' => $this->base)
            );

        $baseDetails = [];

        foreach($baseDetailsTmp as $tmpData){
            $tmp = $tmpData->getMd5();
            $baseDetails[] = $tmp;
        }

        // Si la base est vide on renvoi une erreur
        if (!$baseDetails) {
            return true;
        }

        // On recupere la campagne liée à l'id recuperée
        $this->campaign = $this->em
            ->getRepository('ApplicationSonataUserBundle:Campaign')
            ->find($object['campaign']);

        // On recupere la base liée a cette campagne
        $campaignBase = $this->campaign->getBase();

        // Si la base est null on renvoi une erreur
        if (!$campaignBase) {
            return true;
        }

        // On recupere les details de la base ciblé
        $baseDetailsFromCampaignTmp = $this->em
            ->getRepository('ApplicationSonataUserBundle:BaseDetail')
            ->findBy(
                array('base' => $campaignBase)
            );

        $baseDetailsFromCampaign = [];

        foreach($baseDetailsFromCampaignTmp as $tmpData){
            $tmp = $tmpData->getMd5();
            $baseDetailsFromCampaign[] = $tmp;
        }

        // Si le base ne contient pas de données on renvoi une erreur
        if (!$baseDetailsFromCampaign) {
            return true;
        }

        // Lancement du matching entre les deux bases
        $dataMatch = $this->match($baseDetails, $baseDetailsFromCampaign);

        if (!$dataMatch) {
            return true;
        }

        // Population de l'entité matching
        $responsePopulate = $this->populate($dataMatch);

        return $responsePopulate;
    }

    protected function match(Array $first_db, Array $second_db){

        // Si une des deux bases est null on renvoi une erreur
        if(null == $first_db || null == $second_db){
            return false;
        }

        // Fonction permettant de traité la difference entre les deux array
        $result =  array_intersect($first_db, $second_db);

        // Fonction qui supprime les doublons
        $finalResult = array_unique($result, SORT_REGULAR);

        // Return du resultat du matching
        return $finalResult;
    }

    protected function populate(Array $data){

        $match = new Matching();

        $response = $this->populateBaseDetails($data, $match);

        $nbMatch = count($data);

        if($response){

            $match->setBase($this->base);
            $match->setCampaign($this->campaign);
            $match->setUpdatedAt();
            $match->setMatchCount($nbMatch);

            $this->em->persist($match);
            $this->em->flush();

            return true;
        }else{
            return false;
        }
    }

    protected  function populateBaseDetails(Array $dataMatch, Matching $match){

        foreach($dataMatch as $md5){

            $matchDetail = new MatchingDetail();

            // Et on rempli les variables de l'objet
            $matchDetail->setIdMatching($match);
            $matchDetail->setMd5($md5);

            // Puis on persiste l'entité
            $this->em->persist($matchDetail);
        }

        return true;
    }
}