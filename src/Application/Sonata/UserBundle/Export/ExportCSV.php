<?php

namespace Application\Sonata\UserBundle\Export;

use Application\Sonata\UserBundle\Entity\Matching;
use Application\Sonata\UserBundle\Entity\MatchingDetail;
use Doctrine\ORM\EntityManager;

class ExportCSV {

    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Export CSV from matching
     *
     * @param integer $matchingId
     * @return integer
     */
    public function fromMatching($matchingId){

        $answers = $this->em->getRepository('ApplicationSonataUserBundle:MatchingDetail')->findByIdMatching($matchingId);

        $handle = fopen('php://memory', 'r+');
        $header = array();

        foreach ($answers as $answer) {
            fputcsv($handle, $answer->getData());
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return new Response($content, 200, array(
            'Content-Type' => 'application/force-download',
            'Content-Disposition' => 'attachment; filename="export.csv"'
        ));
    }
}