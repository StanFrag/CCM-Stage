<?php

namespace Application\Sonata\UserBundle\Controller;

use Application\Sonata\UserBundle\Entity\BaseDetail;
use Application\Sonata\UserBundle\Entity\Base;
use Application\Sonata\UserBundle\Form\Type\BaseType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class BaseController extends Controller
{
    public function uploadAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        // On crée un objet Base
        $base = new Base();

        $form = $this->createForm(new BaseType(), $base);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $base->setUser($user);

            // Get file
            $filePath = $form->get('file')->getData()->getPathName();

            $this->populateBaseDetail($filePath, $base, $base->getDelimiter(), $em);

            $em->persist($base);
            $em->flush();

            $this->setFlash('sonata_user_success', 'upload.flash.success');

            $this->redirect($this->generateUrl('base_upload'));
        }

        return $this->render('base/base_upload.html.twig', array(

            'form' => $form->createView(),

        ));
    }

    public function populateBaseDetail($filePath, $base, $delimiter, $em){
        $num = 0;

        if (($handle = fopen($filePath, "r")) !== FALSE) {
            while(($row = fgetcsv($handle, 100, $delimiter)) !== FALSE) {

                $md5Array = count($row); // process the row.

                for ($c=0; $c < $md5Array; $c++) {
                    $num += 1;

                    $baseDetail = new BaseDetail();
                    $baseDetail->setBase($base);
                    $baseDetail->setMd5($row[$c]);

                    $em->persist($baseDetail);
                }
            }
            $base->setNbLine($num);
            fclose($handle);
        }

        return $num;
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
