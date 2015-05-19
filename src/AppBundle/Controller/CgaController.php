<?php

// src/AppBundle/Controller/CgaController.php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CgaController extends Controller
{
    public function showAction()
    {
        return $this->render('cga/index.html.twig');
    }
}
