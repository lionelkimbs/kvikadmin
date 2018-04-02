<?php

namespace Kvik\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller
{
    public function indexAction()
    {
        return $this->render('@KvikAdmin/Category/index.html.twig');
    }
}
