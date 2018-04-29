<?php

namespace Kvik\AdminBundle\Controller;

use Kvik\AdminBundle\Entity\Post;
use Kvik\AdminBundle\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PostController extends Controller
{
    /**
     * @param Request $request
     * @param $type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $type)
    {
        $em = $this->getDoctrine()->getManager();
        if( count($request->query) > 0 ){
            $params = [
                'status' => $request->query->get('status'),
                'cat' => $request->query->get('cat'),
                'tag' => $request->query->get('tag'),
                'author' => $request->query->get('author'),
                'page' => $request->query->get('page')
            ];
            $posts = $em->getRepository(Post::class)->getPostsByQuery($params);
        }
        else $posts = $em->getRepository(Post::class)->findBy([
            'postType' => $type
        ]);
        return $this->render('@KvikAdmin/Post/index.html.twig', [
            'type' => $type,
            'posts' => $posts
        ]);
    }

    public function addAction(Request $request, $type)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post, ['type' => $type]);
        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setSlug( $this->container->get('kvik.sanitize')->slugify($post->getSlug(), $post->getTitle(), $post) );
            if( $post->getTerms()->isEmpty() ) $this->container->get('kvik.postManager')->addToUncategorized($post);
            $post->setEditor($this->getUser());
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
            $post->setSlug( $this->container->get('kvik.sanitize')->slugify($post->getSlug(), $post->getTitle(), $post) );
            if( $post->getTerms()->isEmpty() ) $this->container->get('kvik.postManager')->addToUncategorized($post);
            $post->setEditor($this->getUser());
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
            $post->setPostStatus('trash');
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('kvik_admin_post', [
                'type' => $post->getPostType()
            ]);
        }
    }

}
