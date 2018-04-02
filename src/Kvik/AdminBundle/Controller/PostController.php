<?php

namespace Kvik\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PostController extends Controller
{
    public function indexAction($type)
    {
        return $this->render('@KvikAdmin/Post/index.html.twig', [
            'type' => $type
        ]);
    }

    public function addAction($type)
    {
        return $this->render('@KvikAdmin/Post/add.html.twig', [
            'type' => $type
        ]);
    }
}
