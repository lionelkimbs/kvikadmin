<?php

namespace Kvik\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TermController extends Controller{

    public function indexAction($type)
    {
        return $this->render('@KvikAdmin/Term/index.html.twig');
    }

}