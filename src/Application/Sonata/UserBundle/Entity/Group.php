<?php

namespace Application\Sonata\UserBundle\Entity;

use Sonata\UserBundle\Entity\BaseGroup as BaseGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="fos_user_group", options={"collate"="utf8_general_ci"})
 * @ORM\Entity(repositoryClass="Application\Sonata\UserBundle\Entity\Repository\GroupRepository")
 */
class Group extends BaseGroup
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="groups")
     */
    protected $users;
}
