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

    public function __construct($container, EntityManager $em, Consumer $consumer, $directory)
    {
        $this->container = $container;
        $this->em = $em;
        $this->consumer = $consumer;
        $this->directory = $directory;
    }

    public function execute(AMQPMessage $msg)
    {
        // Decode message
        $object = unserialize($msg->body);

        // Rabbitmq consumer non lancé, renvoi en liste necessaire.
        if (isset($object['message']) && $object['message'] === 'shutdown') {
            $this->consumer->forceStopConsumer();
            return false;
        }

        // On verifie que les id recuperée ne sont pas null
        if(null == $object['base'] || null == $object['filePath']){
            return false;
        }

        $base = $this->em
            ->getRepository('ApplicationSonataUserBundle:Base')
            ->find($object['base']);

        // Si la base est null c'est qu'elle n'existe plus donc le traitement n'a pas lieu d'etre
        if(null == $base){
            return true;
        }

        // Si le fichier CSV est correctement ouvert en lecture
        if (($handle = fopen($this->directory.'/'.$object['filePath'], "r")) !== FALSE) {
            // Pour chaque colonne du CSV
            while(($row = fgetcsv($handle, 0, ';')) !== FALSE) {

                // On compte le nombre de ligne presente dans le fichier
                $md5Array = count($row);

                // Pour chaque ligne
                for ($c=0; $c < $md5Array; $c++) {

                    $tmpObj = str_replace([' ', ';'], '', $row[$c]);

                    $this->populate($tmpObj, $base);
                }
            }
            fclose($handle);

            // Si le traitement s'est deroulé correctement, on le specifie au consumer
            return true;
        }else{
            // Si on arrive pas a ouvrir le fichier, on reitere l'operation
            return false;
        }
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
    }
}