<?php

namespace Application\Sonata\UserBundle\Import;

use Application\Sonata\UserBundle\Entity\Base;
use Application\Sonata\UserBundle\Entity\BaseDetail;
use Doctrine\ORM\EntityManager;

class PopulateBD {

    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Populate bdd from CSV file
     *
     * @param string $filePath
     * @param base $base
     * @return integer
     */
    public function fromCSV($filePath, $base){

        $num = 0;

        // Si le fichier CSV est correctement ouvert en lecture
        if (($handle = fopen($filePath, "r")) !== FALSE) {
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
                        // On crée un nouvelle obj Base Detail
                        $baseDetail = new BaseDetail();

                        // Et on rempli les variables de l'objet
                        $baseDetail->setBase($base);
                        $baseDetail->setMd5($tmpObj);

                        // Puis on persiste l'entité
                        $this->em->persist($baseDetail);

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
        }else{
            // Si le CSV n'a pas pu etre ouvert, on renvoi une reponse null
            return null;
        }

        // Si le traitement s'est deroulé correctement, on envoi le nombre de lignes traitées
        return $num;
    }
}