<?php

namespace Application\Sonata\UserBundle\Match;

use Application\Sonata\UserBundle\Entity\MatchingDetail;
use Doctrine\ORM\EntityManager;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use \Application\Sonata\UserBundle\Entity\Matching;

class dbConsumer implements ConsumerInterface{

    protected $em;

    protected $base;
    protected $campaign;

    public function __construct($container, EntityManager $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    public function execute(AMQPMessage $msg)
    {
        // Decode message
        $object = unserialize($msg->body);

        // On verifie que les id recuperée ne sont pas null
        if(null == $object['campaign'] || null == $object['base']){
            return false;
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
            array_push($baseDetails, $tmpData->getMd5());
        }

        // Si la base est vide on renvoi une erreur
        if (!$baseDetails) {
            return false;
        }

        // On recupere la campagne liée à l'id recuperée
        $this->campaign = $this->em
            ->getRepository('ApplicationSonataUserBundle:Campaign')
            ->find($object['campaign']);

        // On recupere la base liée a cette campagne
        $campaignBase = $this->campaign->getBase();

        // Si la base est null on renvoi une erreur
        if (!$campaignBase) {
            return false;
        }

        // On recupere les details de la base ciblé
        $baseDetailsFromCampaignTmp = $this->em
            ->getRepository('ApplicationSonataUserBundle:BaseDetail')
            ->findBy(
                array('base' => $campaignBase)
            );

        $baseDetailsFromCampaign = [];

        foreach($baseDetailsFromCampaignTmp as $tmpData){
            array_push($baseDetailsFromCampaign, $tmpData->getMd5());
        }

        // Si le base ne contient pas de données on renvoi une erreur
        if (!$baseDetailsFromCampaign) {
            return false;
        }

        // Lancement du matching entre les deux bases
        $dataMatch = $this->match($baseDetails, $baseDetailsFromCampaign);

        if (!$dataMatch) {
            return false;
        }

        // Population de l'entité matching
        $responsePopulate = $this->populate($dataMatch);

        return $responsePopulate;
    }

    protected function match(Array $firstDB, Array $secondDB){

        // Si une des deux bases est null on renvoi une erreur
        if(null == $firstDB || null == $firstDB){
            return false;
        }

        // Fonction permettant de traité la difference entre les deux array
        $result =  array_intersect($firstDB, $secondDB);

        $finalResult = [];

        foreach ($result as $data) {

            $verif = false;

            foreach ($finalResult as $current) {
                if($current == $data){
                    $verif = true;
                }
            }

            if(!$verif){
                array_push($finalResult, $data);
            }
        }

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
            $match->setDateMaj();
            $match->setNbMatch($nbMatch);

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