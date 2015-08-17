<?php

namespace Application\Sonata\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MatchingController extends Controller
{
    public function showAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        // On recupere l'ensemble des bases actives
        $em = $this->getDoctrine();
        $campaigns = $em->getRepository('ApplicationSonataUserBundle:Campaign')->findCampaignWithMatching();

        if(!$campaigns){
            throw new NotFoundHttpException("Campagne non trouvé, veuillez contacter le support.");
        }

        $matchArray = [];

        foreach($campaigns as $tmp) {
            $match = $em->getRepository('ApplicationSonataUserBundle:Matching')->findAllFromCampaign($user, $tmp);
            array_push($matchArray, ['campaign' => $tmp, 'match' => $match]);
        }

        return $this->render('match/match_list.html.twig', array(
            'listMatch' => $matchArray,
        ));
    }
}
