<?php

namespace Kvik\AdminBundle\Controller;

use Kvik\AdminBundle\Entity\Post;
use Kvik\AdminBundle\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PostController extends Controller
{
    public function indexAction($type)
    {
        return $this->render('@KvikAdmin/Post/index.html.twig', [
            'type' => $type
        ]);
    }

    public function addAction(Request $request, $type)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post, ['type' => $type]);
        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setSlug( $this->container->get('kvik.sanitize')->slugify($post->getSlug(), $post->getTitle()) );
            $post->setPostType($type);
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('kvik_admin_post_edit', [
                'id' => $post->getId()
            ]);
        }
        return $this->render('@KvikAdmin/Post/add.html.twig', [
            'type' => $type,
            'form' => $form->createView()
        ]);
    }

    public function editAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->find($id);
        $form = $this->createForm(PostType::class, $post, [
            'todo' => 'edit',
            'type' => $post->getPostType()
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() ){
            $post->setSlug( $this->container->get('kvik.sanitize')->slugify($post->getSlug(), $post->getTitle()) );
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('kvik_admin_post_edit', [
                'id' => $post->getId(),
            ]);
        }
        return $this->render('@KvikAdmin/Post/add.html.twig', [
            'type' => $post->getPostType(),
            'form' => $form->createView(),
            'post' => $post
        ]);
    }

    public function trashAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->find($id);
        if ( $post === null ) return $this->render('@KvikAdmin/Admin/index.html.twig');
        else{
            $post->setPostStatus(0);
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('kvik_admin_post', [
                'type' => $post->getPostType()
            ]);
        }
    }

}
