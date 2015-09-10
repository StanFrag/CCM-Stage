<?php

namespace Application\Sonata\UserBundle\Controller;

use Application\Sonata\UserBundle\Entity\Base;
use Application\Sonata\UserBundle\Form\Type\BaseType;
use Application\Sonata\UserBundle\Form\Type\RenameBaseType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class BaseController extends Controller
{
    public function showAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('ApplicationSonataUserBundle:Base')
        ;

        $listBases = $repository->findBy(
            array('user' => $user),
            array('updated_at' => 'DESC')
        );

        return $this->render('base/base_list.html.twig', array(
            'listBases' => $listBases,
        ));
    }

    public function renameAction(Base $base, Request $request)
    {
        if (!$base) {
            throw $this->createNotFoundException('No Base found');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        if ($base->getUser() !== $user) {
            throw new AccessDeniedException();
        }

        // Creation du formulaire
        $form = $this->createForm(new RenameBaseType(), $base);
        $form->handleRequest($request);

        // Si le formulaire est submit et la validation correct
        if ($form->isValid()) {

            // Recupération de l'entity manager
            $em = $this->getDoctrine()->getManager();

            // Recuperation du titre de la base modifié
            $name = $form["title"]->getData();

            $base->setTitle($name);

            // Et on envoi les données
            $em->persist($base);
            $em->flush();

            $this->setFlash('sonata_user_success', 'upload.flash.success');

            return $this->redirect($this->generateUrl('base_list'));
        }

        return $this->render('base/base_rename.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function updateAction(Base $base, Request $request)
    {
        if (!$base) {
            throw $this->createNotFoundException('No Base found');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        if ($base->getUser() !== $user) {
            throw new AccessDeniedException();
        }

        // Creation du formulaire
        $form = $this->createForm(new BaseType(), $base);
        $form->handleRequest($request);

        // Si le formulaire est submit et la validation correct
        if ($form->isValid()) {

            // Recupération de l'entity manager
            $em = $this->getDoctrine()->getManager();

            // Recuperation du path du fichier soumis
            $filePath = $form->get('file')->getData()->getPathName();

            // On supprime les anciennes base details
            $base->removeBaseDetailAll();

            // On récupère le service pour remplir la base de donnée des basesDetails
            $populate = $this->container->get('public_user.populate');
            $response = $populate->fromCSV($filePath, $base);

            // Si le service n'a pas rempli la base de donnée des Bases Details
            if (null !== $response) {
                // Sinon on ajout en bd le nombre de ligne du fichier et l'User associé
                $base->setRowCount($response);
                $base->setUser($user);

                // Et on envoi les données
                $em->persist($base);
                $em->flush();

                $this->removePreviousMatching($base);

                $this->sendMatching($base);

                $this->setFlash('sonata_user_success', 'upload.flash.success');
                return $this->redirect($this->generateUrl('base_list'));
            }else{
                $this->setFlash('sonata_user_error', 'upload.flash.error');
                $this->redirect($this->generateUrl('base_upload'));
            }
        }

        return $this->render('base/base_upload.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function detailsAction(Base $base)
    {
        if (!$base) {
            throw $this->createNotFoundException('No Base found');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        if ($base->getUser() !== $user) {
            throw new AccessDeniedException();
        }

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('ApplicationSonataUserBundle:BaseDetail')
        ;

        $listBases = $repository->findBy(
            array('base' => $base)
        );

        return $this->render('base/base_details_list.html.twig', array(
            'listBasesDetails' => $listBases,
        ));
    }

    public function removeAction(Base $base)
    {

        if (!$base) {
            throw $this->createNotFoundException('No Base found');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        if ($base->getUser() !== $user) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($base);
        $em->flush();

        // Remove de la base
        $this->get('public_user.upload_base')->remove($base);

        $this->setFlash('sonata_user_success', 'remove.base.success');

        return $this->redirect($this->generateUrl('base_list'));
    }

    public function uploadAction(Request $request)
    {
        // Création de l'objet Base
        $base = new Base();

        // Creation du formulaire
        $form = $this->createForm(new BaseType(), $base);
        $form->handleRequest($request);

        // Si le formulaire est submit et la validation correct
        if ($form->isValid()) {
            // Recupération du user
            $user = $this->get('security.token_storage')->getToken()->getUser();

            // Recupération de l'entity manager
            $em = $this->getDoctrine()->getManager();

            // Recuperation du path du fichier soumis
            $filePath = $form->get('file')->getData()->getPathName();

            // On récupère le service pour remplir la base de donnée des basesDetails
            $populate = $this->container->get('public_user.populate');
            $response = $populate->fromCSV($filePath, $base);

            // Si le service n'a pas rempli la base de donnée des Bases Details
            if (null !== $response) {
                // Upload de la base
                $this->get('public_user.upload_base')->upload($base);

                // Sinon on ajout en bd le nombre de ligne du fichier et l'User associé
                $base->setRowCount($response);
                $base->setUser($user);

                // Et on envoi les données
                $em->persist($base);
                $em->flush();

                $this->sendMatching($base);

                $this->setFlash('sonata_user_success', 'upload.flash.success');
                return $this->redirect($this->generateUrl('base_list'));
            }else{
                $this->setFlash('sonata_user_error', 'upload.flash.error');
                $this->redirect($this->generateUrl('base_upload'));
            }
        }

        return $this->render('base/base_upload.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    protected function sendMatching(Base $base){
        // Apres la persistance/update d'une campagne

        $idArray = array();

        // On recupere l'ensemble des bases actives
        $em = $this->getDoctrine();
        $campaigns = $em->getRepository('ApplicationSonataUserBundle:Campaign')->findActiveCampaign();

        if(null == $campaigns){
            return;
        }else{
            // Pour chaque base on recupere son id
            foreach($campaigns as $campaign){
                $id = $campaign['id'];
                array_push($idArray, $id);
            }

            // On récupère le service qui va envoyer le match
            $sendMatching = $this->container->get('match_exchange_sender');
            $sendMatching->sendDB($base->getId(), $idArray, 'campaign');
        }
    }

    public function removePreviousMatching(Base $base)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $matchs = $em->getRepository('ApplicationSonataUserBundle:Matching')->findByBase($base);

        if (null == $matchs) {
            return;
        }else{
            foreach($matchs as $match){
                $em->remove($match);
            }

            $em->flush();
        }

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
