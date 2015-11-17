<?php

namespace Application\Sonata\UserBundle\Match;

use Application\Sonata\UserBundle\Entity\MatchingDetail;
use Doctrine\ORM\EntityManager;
use OldSound\RabbitMqBundle\RabbitMq\Consumer;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Config\Definition\Exception\Exception;
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

        $this->consumer->getChannel()->queue_bind('match-queue', 'broadcasting');
    }

    public function execute(AMQPMessage $msg)
    {
        // On decode le message recu
        $object = unserialize($msg->body);

        // Verification que le consumer est activé
        if (isset($object['message']) && $object['message'] === 'shutdown') {
            $this->consumer->forceStopConsumer();
            return false;
        }

        $conn = $this->em->getConnection();

        // On recupere la base liée a l'id recuperée
        $this->base = $this->em
            ->getRepository('ApplicationSonataUserBundle:Base')
            ->find($object['base']);

        // Si l'id ciblé ne recupere pas de base cela signifie que cette base n'existe plus, on annule alors
        if(!$this->base){
            return true;
        }

        $sth = $conn->prepare('SELECT md5 FROM base_details WHERE fk_base = ?');
        $sth->execute(array($this->base->getId()));
        $result = $sth->fetchAll();

        $baseDetails = array_map(function($array){return $array['md5']; }, $result);

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
        if(!$this->campaign){
            return true;
        }

        // On recupere la base liée a cette campagne
        $campaignBase = $this->campaign->getBase();

        // Si l'id ciblé ne recupere pas de base de campagne, cela signifi qu'aucune base n'est assigné a la campagne
        // le traitement n'a donc pas lieu d'etre
        if(!$campaignBase){
            return true;
        }

        $sth = $conn->prepare('SELECT md5 FROM base_details WHERE fk_base = ?');
        $sth->execute(array($campaignBase->getId()));
        $result = $sth->fetchAll();

        $baseDetailsFromCampaign = array_map(function($array){return $array['md5']; }, $result);

        if($campaignBase->getRowCount() != count($baseDetailsFromCampaign)){
            return false;
        }

        // Lancement du matching entre les deux bases
        $dataMatch = $this->match($baseDetails, $baseDetailsFromCampaign);

        // Population de l'entité matching
        $responsePopulate = $this->populate($dataMatch, $conn);

        return $responsePopulate;
    }

    protected function match(Array $first_db, Array $second_db){

        // Fonction permettant de traité la difference entre les deux array
        $result =  array_intersect($first_db, $second_db);

        // Fonction qui supprime les doublons
        $finalResult = array_unique($result);

        // Return du resultat du matching
        return $finalResult;
    }

    protected function populate(Array $data, $conn){

        $conn->beginTransaction();

        $sth = $conn->prepare('INSERT INTO matchings (base_id, campaign_id) VALUES (?, ?)');
        $sth->execute(array($this->base->getId(),$this->campaign->getId()));

        $tmp = $conn->lastInsertId();

        $response = $this->populateBaseDetails($data, $tmp, $conn);

        $nbMatch = count($data);

        if($response){

            $sth = $conn->prepare('UPDATE matchings SET date_maj = ?, match_count = ? WHERE id = ?');
            $sth->execute(array(date("Y-m-d H:i:s"),$nbMatch, $tmp));

            try{
                $conn->commit();
                return true;
            } catch(Exception $e) {
                $conn->rollback();
                throw $e;
            }

        }else{
            return false;
        }
    }

    protected  function populateBaseDetails(Array $dataMatch, $idMatch, $conn){

        $countLine = 0;

        foreach($dataMatch as $md5){
            $countLine++;

            $sth = $conn->prepare('INSERT INTO matching_details (md5, id_matching) VALUES (?, ?)');
            $sth->execute(array($md5,$idMatch));

            if( ($countLine % 500) == 0){
                try{
                    $conn->commit();
                    $conn->beginTransaction();
                } catch(Exception $e) {
                    $conn->rollback();
                    throw $e;
                }
            }
        }



        return true;
    }
}
