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
        // On decode le message recu
        $object = unserialize($msg->body);

        // Verification que le consumer est activé
        // si non on renvoi en file
        if (isset($object['message']) && $object['message'] === 'shutdown') {
            $this->consumer->forceStopConsumer();
            return false;
        }

        if (empty($object['base']) || empty($object['campaign'])) {
            return true;
        }

        // On recupere la base liée a l'id recuperée
        $this->base = $this->em
            ->getRepository('ApplicationSonataUserBundle:Base')
            ->find($object['base']);

        // Si l'id ciblé ne recupere pas de base cela signifie que cette base n'existe plus, on annule alors
        if(empty($this->base)){
            return true;
        }

        // On recupere les baseDetails de la base ciblé
        $baseDetailsTmp = $this->em
            ->getRepository('ApplicationSonataUserBundle:BaseDetail')
            ->findBy(
                array('base' => $this->base)
            );

        if(empty($baseDetailsTmp)){
            return false;
        }

        $baseDetails = [];

        // Pour chaque basedetail, on recupere son md5
        foreach($baseDetailsTmp as $tmpData){
            $tmp = $tmpData->getMd5();
            $baseDetails[] = $tmp;
        }

        // Si le nombre de baseDetails recuperé n'est pas egal au nombre de ligne de la base
        // cela signifie que les données de baseDetails n'ont pas encore été completment importé
        // On renvoi le traitement en liste d'attente
        if($this->base->getRowCount() != count($baseDetails)){
            return false;
        }

        // On recupere la campagne liée à l'id recuperée
        $this->campaign = $this->em
            ->getRepository('ApplicationSonataUserBundle:Campaign')
            ->find($object['campaign']);

        // Si l'id ciblé ne recupere pas de campagne cela signifie que cette base n'existe plus, on annule alors
        if(empty($this->campaign)){
            return true;
        }

        // On recupere la base liée a cette campagne
        $campaignBase = $this->campaign->getBase();

        // Si l'id ciblé ne recupere pas de base de campagne, cela signifi qu'aucune base n'est assigné a la campagne
        // le traitement n'a donc pas lieu d'etre
        if(empty($campaignBase)){
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

        if($campaignBase->getRowCount() != count($baseDetailsFromCampaign)){
            return false;
        }

        // Lancement du matching entre les deux bases
        $dataMatch = $this->match($baseDetails, $baseDetailsFromCampaign);

        // Population de l'entité matching
        $responsePopulate = $this->populate($dataMatch);

        return $responsePopulate;
    }

    protected function match(Array $first_db, Array $second_db){

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