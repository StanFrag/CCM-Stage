<?php

namespace AppBundle\Controller;

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

        /*
        $response = new StreamedResponse();

        $response->setCallback(function ($matchId) {
            $container = $this->container;

            $em = $container->get('doctrine')->getManager();

            $conn = $em->getConnection();

            $sth = $conn->prepare('SELECT md5 FROM matching_details WHERE id_matching = ?');
            $sth->execute(array($matchId));

            $results = $sth->fetchAll();

            $handle = fopen('php://output', 'r') or die("Couldn't get handle");

            foreach ($results as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        });

        $contentDisposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'Export_matching_'.$matchId.'.csv');
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', $contentDisposition);
        return $response;
        */

        return new StreamedResponse(
            function () use ($matchId) {
                $container = $this->container;
                $em = $container->get('doctrine')->getManager();
                $conn = $em->getConnection();

                $sth = $conn->prepare('SELECT md5 FROM matching_details WHERE id_matching = ?');
                $sth->execute(array($matchId));
                $results = $sth->fetchAll();

                $handle = fopen('php://output', 'r') or die("Couldn't get handle");

                foreach ($results as $row) {
                    fputcsv($handle, $row);
                }

                fclose($handle);
            }, 200, array(
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="Export_matching_'.$matchId.'.csv"')
        );

        /*
        // On récupère le service qui va envoyer le match
        $response = new StreamedResponse(function() use($matchId) {
            //$downloadMatching = $this->container->get('public_user.exportCsv')->fromMatching($matchId);

            $container = $this->container;

            $em = $container->get('doctrine')->getManager();

            $conn = $em->getConnection();

            $sth = $conn->prepare('SELECT md5 FROM matching_details WHERE id_matching = ?');
            $sth->execute(array($matchId));

            $results = $sth->fetchAll();

            $handle = fopen('php://output', 'r') or die("Couldn't get handle");

            foreach ($results as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
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
        */
    }
}