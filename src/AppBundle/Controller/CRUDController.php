<?php

namespace AppBundle\Controller;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CRUDController extends Controller
{
    public function downloadMatchAction()
    {
        $object = $this->admin->getSubject();
        $matchId = $object->getId();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('Unable to find the object with id : %s', $matchId));
        }

        // On récupère le service qui va envoyer le match
        $response = new StreamedResponse(function() use($matchId) {
            //$downloadMatching = $this->container->get('public_user.exportCsv')->fromMatching($matchId);

            $container  = $this->container;
            $em         = $container->get('doctrine')->getManager();
            $conn       = $em->getConnection();

            $lim = 10000;

            // On recupere les 10 000 premiers resultat
            $query = "SELECT SQL_CALC_FOUND_ROWS md5 FROM matching_details WHERE id_matching = :id LIMIT :lim";
            $sth = $conn->prepare($query);
            $sth->bindValue(':id', $matchId);
            $sth->bindValue(':lim', (int) $lim, \PDO::PARAM_INT);
            $sth->execute();
            $results = $sth->fetchAll();

            // On ouvre le fichier sur lequel on va ecrire
            $handle = fopen('php://output', 'r') or die("Couldn't get handle");

            // On ajout au fichier les données recuperé precedement
            foreach ($results as $row) {
                fputcsv($handle, $row);
            }

            // On recupere le nombre de ligne total
            $sth = $conn->prepare('SELECT FOUND_ROWS()');
            $sth->execute(array($matchId));
            $resultLine = $sth->fetchAll();

            $nb_line = intval($resultLine[0]["FOUND_ROWS()"]);

            // Si le nombrte de ligne max est superieurs aux nombres d elignes recuperé precedement
            if($nb_line >= $lim){

                // on retire du nombre de ligne le nombre d'elements recuperé precedement
                $lineDone = $lim;
                $offset = $lim;

                $this->getData($conn, $matchId, $handle, $nb_line, $lineDone, $lim, $offset);
            }else{
                fclose($handle);
            }
        });

        $response->setStatusCode(200);
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Expires', '0');
        $response->headers->set('Content-Disposition','attachment; filename="Export_matching_'.$matchId.'.csv"');
        $response->headers->set('Cache-Control', 'must-revalidate; post-check=0; pre-check=0');
        $response->headers->set('Cache-Control', 'private', false);
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Transfer-Encoding', 'binary');

        return $response;
    }

    function getData($conn, $matchId, $handle, $nb_line, $lineDone, $lim, $offset){

        $query = 'SELECT md5 FROM matching_details WHERE id_matching = :id LIMIT :lim OFFSET :offset';

        $sth = $conn->prepare($query);
        $sth->bindValue(':id', $matchId);
        $sth->bindValue(':lim', (int) $lim, \PDO::PARAM_INT);
        $sth->bindValue(':offset', (int) $offset, \PDO::PARAM_INT);
        $sth->execute();
        $results = $sth->fetchAll();

        foreach ($results as $row) {
            fputcsv($handle, $row);
        }

        // On incremente le nombre de ligne traité
        $lineDone += $lim;
        $offset += $lim;

        if($lineDone >= $nb_line){
            fclose($handle);
            return;
        }else{
            $this->getData($conn, $matchId, $handle, $nb_line, $lineDone, $lim, $offset);
        }
    }
}