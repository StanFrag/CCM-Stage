<?php

namespace Application\Sonata\UserBundle\Controller;

use Application\Sonata\UserBundle\Entity\Base;
use Application\Sonata\UserBundle\Form\Type\BaseType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;


class BaseController extends Controller
{
    public function uploadAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        // Création de l'objet Base
        $base = new Base();

        // Creation du formulaire
        $form = $this->createForm(new BaseType(), $base);
        $form->handleRequest($request);

        // Si le formulaire est submit et la validation correct
        if ($form->isValid()) {
            // Recupération de l'entity manager
            $em = $this->getDoctrine()->getManager();

            // Recuperation du path du fichier soumis
            $filePath = $form->get('file')->getData()->getPathName();

            // On récupère le service pour remplir la base de donnée des basesDetails
            $populate = $this->container->get('public_user.populate');
            $nb_line = $populate->fromCSV($filePath, $base);

            // Si le service n'a pas rempli la base de donnée des Bases Details
            if ($nb_line == 0) {
                $this->setFlash('sonata_user_error', 'upload.flash.error');
            }else{
                // Sinon on ajout en bd le nombre de ligne du fichier et l'User associé
                $base->setNbLine($nb_line);
                $base->setUser($user);

                // Et on envoi les données
                $em->persist($base);
                $em->flush();

                $this->setFlash('sonata_user_success', 'upload.flash.success');
            }
            $this->redirect($this->generateUrl('base_upload'));
        }

        return $this->render('base/base_upload.html.twig', array(

            'form' => $form->createView(),

        ));
    }

    /**
     * @param string $action
     * @param string $value
     */
    protected function setFlash($action, $value)
    {
        $this->container->get('session')->getFlashBag()->set($action, $value);
    }
}
