<?php

namespace Kvik\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('@KvikAdmin/Default/index.html.twig');
    }
}
