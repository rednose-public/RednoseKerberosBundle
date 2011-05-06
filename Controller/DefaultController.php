<?php

namespace Rednose\KerberosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('RednoseKerberosBundle:Default:index.html.twig');
    }
}
