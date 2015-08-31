<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CRUDController extends Controller
{
    public function downloadMatchAction()
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('Unable to find the object with id : %s', $id));
        }

        // On récupère le service qui va envoyer le match
        $downloadMatching = $this->container->get('public_user.exportCsv');
        $downloadMatching->fromMatching($object->getId());

        $this->addFlash('sonata_flash_success', 'DownLoad du matching effectué avec succès');

        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}