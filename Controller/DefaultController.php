<?php

namespace Millennium\PaginationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MillenniumPaginationBundle:Default:index.html.twig', ['name' => $name]);
    }
}
