<?php

namespace Application\Sonata\UserBundle\Import;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class populateSender{

    private $container;

    public function __construct($container, $directory)
    {
        $this->container = $container;
        $this->directory = $directory;
    }

    public function send($filePath, $base)
    {
        // message a publier ensuite
        $msgToPublish = array
        (
            'filePath' => $filePath,
            'base' => $base
        );

        // Publish message
        $msg = serialize($msgToPublish);

        // Count nombre data
        $num = 0;

        if (($handle = fopen($this->directory.'/'.$filePath, "r")) !== FALSE) {

            while(($row = fgetcsv($handle, 0, ';')) !== FALSE) {

                $md5Array = count($row);
                for ($c=0; $c < $md5Array; $c++) {

                    $tmpObj = str_replace([' ', ';'], '', $row[$c]);

                    if(strlen($tmpObj) == 32){
                        $num += 1;
                    }else{
                        return null;
                    }
                }
            }
            fclose($handle);

            $this->container->get('old_sound_rabbit_mq.add_populate_exchange_producer')->publish($msg);

            return $num;
        }else{
            // Si le CSV n'a pas pu etre ouvert, on renvoi une reponse null
            return null;
        }
    }
}