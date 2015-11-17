<?php

namespace Application\Sonata\UserBundle\Match;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class dbSender{

    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function sendDB($dbTarget, $dbArray, $IsArray)
    {
        foreach ($dbArray as $dbDist)
        {
            $msgToPublish = [];

            switch ($IsArray) {
                case 'base':
                    $msgToPublish = array
                    (
                        'campaign' => $dbTarget,
                        'base' => $dbDist
                    );
                    break;
                case 'campaign':
                    $msgToPublish = array
                    (
                        'campaign' => $dbDist,
                        'base' => $dbTarget
                    );
                    break;
            }

            // Publish message
            $msg = json_encode($msgToPublish);
            $this->container->get('old_sound_rabbit_mq.add_match_exchange_producer')->publish($msg);
        }
    }
}