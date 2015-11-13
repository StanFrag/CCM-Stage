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

        return $handle;
    }
}