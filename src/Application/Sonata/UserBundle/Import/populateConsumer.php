<?php

namespace Application\Sonata\UserBundle\Import;

use Application\Sonata\UserBundle\Entity\BaseDetail;
use Doctrine\ORM\EntityManager;
use OldSound\RabbitMqBundle\RabbitMq\Consumer;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class populateConsumer implements ConsumerInterface{

    protected $base;
    private $conn;

    public function __construct($container, EntityManager $em, Consumer $consumer, $directory)
    {
        $this->container = $container;
        $this->em = $em;
        $this->consumer = $consumer;
        $this->directory = $directory;

        $this->consumer->getChannel()->queue_bind('populate-queue', 'broadcasting');
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

            $conn = $this->em->getConnection();
            $conn->beginTransaction();

            $countLine = 0;

            // Pour chaque colonne du CSV
            while(($row = fgetcsv($handle, 0, ';')) !== FALSE) {

                $countLine++;

                // On compte le nombre de ligne presente dans le fichier
                $md5Array = count($row);

                // Pour chaque ligne
                for ($c = 0; $c < $md5Array; $c++) {

                    $tmpObj = str_replace([' ', ';'], '', $row[$c]);
                    $tmpObjLower = strtolower($tmpObj);

                    $this->populate($tmpObjLower, $base, $conn);
                }

                if( ($countLine % 500) == 0 && $c > 0){
                    try{
                        $conn->commit();
                        $conn->beginTransaction();
                    } catch(Exception $e) {
                        $conn->rollback();
                        throw $e;
                    }
                }
            }
            // On close le csv
            fclose($handle);

            try{
                $conn->commit();
                return true;
            } catch(Exception $e) {
                $conn->rollback();
                throw $e;
            }


        }else{
            // Si on arrive pas a ouvrir le fichier, on reitere l'operation
            return false;
        }
    }

    protected function populate($md5, $base, $conn){
        $conn->insert('base_details', ['md5' => $md5, 'fk_base' => $base->getId()]);
    }
}