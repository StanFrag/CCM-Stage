<?php

namespace Application\Sonata\UserBundle\Export;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportCSV {

    private $em;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Export CSV from matching
     *
     * @param integer $matchingId
     * @return integer
     */
    public function fromMatching($matchingId){

        $container = $this->container;
        $response = new StreamedResponse(function() use($container, $matchingId) {

            $em = $container->get('doctrine')->getManager();

            $results = $em->getRepository('ApplicationSonataUserBundle:MatchingDetail')->findByIdMatching($matchingId);
            $handle = fopen('php://output', 'r+');

            foreach ($results as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition','attachment; filename="Matching-'.$matchingId.'.csv"');

        $response->send();

        return $response;
    }
}