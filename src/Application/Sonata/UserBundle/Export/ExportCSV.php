<?php

namespace Application\Sonata\UserBundle\Export;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportCSV {

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

            $conn = $em->getConnection();

            $sth = $conn->prepare('SELECT md5 FROM matching_details WHERE id_matching = ?');
            $sth->execute(array($matchingId));

            $results = $sth->fetchAll();
            //$results = $em->getRepository('ApplicationSonataUserBundle:MatchingDetail')->findByIdMatching($matchingId);

            $handle = fopen('php://output', 'r');

            foreach ($results as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition','attachment; filename="Export_matching_'.$matchingId.'.csv"');
        //$response->send();

        return $response;
    }
}