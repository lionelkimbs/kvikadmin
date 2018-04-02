<?php

namespace Kvik\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller{

    public function indexAction(){
        return $this->render('@KvikAdmin/Admin/index.html.twig');
    }
}
