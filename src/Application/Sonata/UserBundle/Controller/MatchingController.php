<?php

namespace Application\Sonata\UserBundle\Controller;

use Application\Sonata\UserBundle\Event\MailEvent;
use Application\Sonata\UserBundle\ApplicationEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MatchingController extends Controller
{
    public function showAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        // On recupere l'ensemble des bases actives
        $em = $this->getDoctrine();
        $campaigns = $em->getRepository('ApplicationSonataUserBundle:Campaign')->findCampaignWithMatching();

        $matchArray = [];

        if($campaigns){
            foreach($campaigns as $tmp) {
                $match = $em->getRepository('ApplicationSonataUserBundle:Matching')->findAllFromCampaign($user, $tmp);
                array_push($matchArray, ['campaign' => $tmp, 'match' => $match]);
            }
        }

        return $this->render('match/match_list.html.twig', array(
            'listMatch' => $matchArray,
        ));
    }

    public function postulateAction($base, $campaign, $matchId)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        // nouveau mail event
        $event = new MailEvent();

        $event->setUserName("$user");
        $event->setBase("$base");
        $event->setCampaign("$campaign");
        $event->setMatchingId("$matchId");

        $dispatcher = $this->container->get('event_dispatcher');

        $dispatcher->dispatch(
            ApplicationEvents::AFTER_POSTULATE, $event
        );

        $this->setFlash('sonata_user_success', 'postulate_sent');

        return $this->redirect($this->generateUrl('match_list'));
    }

    public function downloadAction($matchId)
    {
        if (!$matchId) {
            throw new NotFoundHttpException(sprintf('Unable to find the object with id : %s', $matchId));
        }

        $downloadMatching = null;

        // On récupère le service qui va envoyer le match
        $response = new StreamedResponse(function() use($matchId, $downloadMatching) {
            $downloadMatching = $this->container->get('public_user.exportCsv')->fromMatching($matchId);
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition','attachment; filename="Export_matching_'.$matchId.'.csv"');
        $response->headers->set('Content-length', filesize($downloadMatching));
        $response->headers->set('Content-Type', 'text/csv'); // CSV
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Transfer-Encoding', 'binary');

        return $response;
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
