<?php

namespace Application\Sonata\UserBundle\Controller;

use Application\Sonata\UserBundle\Form\Type\BaseType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Application\Sonata\UserBundle\Entity\Base;

class BaseController extends Controller
{
    public function uploadAction(Request $request)

    {
        // On crée un objet Base
        $base = new Base();

        $form = $this->createForm(new BaseType(), $base);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $user = $this->get('security.token_storage')->getToken()->getUser();
            $base->setUser($user);

            // Get file
            $file = $form->get('file');
            $file->getData();

            $this->populateBaseDetail($file, $base);

            $em->persist($base);
            $em->flush();

            $this->redirect($this->generateUrl('base_upload'));
        }

        return $this->render('base/base_upload.html.twig', array(

            'form' => $form->createView(),

        ));
    }

    public function populateBaseDetail(){

    }
}
