<?php

namespace AppBundle\Controller;

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
            $downloadMatching = $this->container->get('public_user.exportCsv')->fromMatching($matchId);
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition','attachment; filename="Export_matching_'.$matchId.'.csv"');

        return $response;
    }
}