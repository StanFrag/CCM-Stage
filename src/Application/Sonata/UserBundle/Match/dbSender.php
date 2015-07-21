<?php

use OldSound\RabbitMqBundle\RabbitMq\Producer;

class dbSender {

    private $dbTarget;
    private $dbArray;

    private $producer;

    public function __construct($dbTarget, array $dbArray, Producer $producer)
    {
        $this->dbTarget = $dbTarget;
        $this->dbArray = $dbArray;

        $this->producer = $producer;
    }

    public function process()
    {
        foreach ($this->dbArray as $dbDist)
        {
            $msgToPublish = array
            (
                'campaign' => $this->dbTarget,
                'base' => $dbDist
            );

            // Publish message
            $msg = json_encode($msgToPublish);
            $this->producer->publish($msg);
        }
    }
}