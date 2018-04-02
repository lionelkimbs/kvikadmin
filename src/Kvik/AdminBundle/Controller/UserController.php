<?php

namespace Kvik\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller{

    public function indexAction(){
        return $this->render('@KvikAdmin/User/index.html.twig');
    }

    public function addAction(){
        return $this->render('@KvikAdmin/User/index.html.twig');
    }
}
