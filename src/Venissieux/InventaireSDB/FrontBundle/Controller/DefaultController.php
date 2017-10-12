<?php

namespace Venissieux\InventaireSDB\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('VenissieuxInventaireSDBFrontBundle:Default:index.html.twig');
    }
}
