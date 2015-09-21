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
        $num = 0;

        // Si le fichier CSV est correctement ouvert en lecture
        if (($handle = fopen($this->directory.'/'.$filePath, "r")) !== FALSE) {
            // Pour chaque colonne du CSV. Dans notre cas il ne sera possible que d'upload des fichiers d'une colonne
            while(($row = fgetcsv($handle, 0, ';')) !== FALSE) {

                // On compte le nombre de ligne presente dans le fichier
                $md5Array = count($row);

                // Pour chaque ligne
                for ($c=0; $c < $md5Array; $c++) {

                    $tmpObj = str_replace([' ', ';'], '', $row[$c]);

                    // Si la ligne prise en compte n'est pas egal à 32 caractere c'est qu'il ne s'agit pas d'un MD5
                    // donc en annule le traitement
                    // Le retrait des caracteres espace et point virgule permet d'empecher une intrusion sur le nombre de caracter
                    if(strlen($tmpObj) == 32){

                        $msgToPublish = array
                        (
                            'md5' => $tmpObj,
                            'base' =>$base
                        );

                        // Publish message
                        $msg = serialize($msgToPublish);
                        $this->container->get('old_sound_rabbit_mq.add_populate_exchange_producer')->publish($msg);

                        // Et on incremente le nombre de ligne traité
                        $num += 1;
                    }else{
                        // Si la ligne possede un nombre non egal à 32 caractere c'est que le fichier ne contient pas seulement des MD5
                        // On renvoi donc une erreur par le biais d'un nombre de MD5 traité null
                        return null;
                    }
                }
            }
            // On ferme la lecture du fichier CSV
            fclose($handle);

            // Si le traitement s'est deroulé correctement, on envoi le nombre de lignes traitées
            return $num;
        }else{
            // Si le CSV n'a pas pu etre ouvert, on renvoi une reponse null
            return null;
        }
    }
}