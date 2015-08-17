<?php

namespace Application\Sonata\UserBundle\Match;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class dbSender{

    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function sendDB($dbTarget, $dbArray, $OneIsArray)
    {
        foreach ($dbArray as $dbDist)
        {
            $msgToPublish = [];

            if($OneIsArray == 'base'){
                $msgToPublish = array
                (
                    'campaign' => $dbTarget,
                    'base' => $dbDist
                );
            }else if($OneIsArray == 'campaign'){
                $msgToPublish = array
                (
                    'campaign' => $dbDist,
                    'base' => $dbTarget
                );
            }

            // Publish message
            $msg = serialize($msgToPublish);
            $this->container->get('old_sound_rabbit_mq.add_match_exchange_producer')->publish($msg);
        }
    }
}