<?php

namespace Kvik\AdminBundle\Controller;

use Kvik\AdminBundle\Entity\Term;
use Kvik\AdminBundle\Form\TermType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TermController extends Controller
{

    public function indexAction(Request $request, $type)
    {
        $term = new Term();
        $form = $this->createForm(TermType::class, $term, ['type' => $type]);

        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $this->container->get('kvik.sanitize')->slugify($term->getSlug(), $term->getName(), $term);
            $term->setSlug($slug);
            $em->persist($term);
            $em->flush();
            return $this->redirectToRoute('kvik_admin_terms', [
                'type' => $type
            ]);
        }

        return $this->render('@KvikAdmin/Term/index.html.twig', [
            'form' => $form->createView(),
            'terms' => $em->getRepository(Term::class)->getTerms($type)
        ]);
    }

    public function editAction(Request $request, $type, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $term = $em->getRepository(Term::class)->find($id);
        $form = $this->createForm(TermType::class, $term, [
            'type' => $type,
            'todo' => 'edit'
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $this->container->get('kvik.sanitize')->slugify($term->getSlug(), $term->getName(), $term);
            $term->setSlug($slug);
            $em->persist($term);
            $em->flush();
            return $this->redirectToRoute('kvik_admin_term_edit', [
                'type' => $type,
                'id' => $id
            ]);
        }
        return $this->render('@KvikAdmin/Term/edit.html.twig', [
            'form' => $form->createView(),
            'term' => $term
        ]);
    }

    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $term = $em->getRepository(Term::class)->find($id);

        if ( $term !== null ){
            if($term->getTermType() == 1 ){
                $type = 'categories';
                //*: If $cat is Uncategorized
                if( $term->getSlug() == 'uncategorized' && !$term->getPosts()->isEmpty() ){
                    $request->getSession()->getFlashBag()->add('notice', 'Vous ne pouvez pas supprimer cette catégorie tant qu\'elle contient des articles.');
                    return $this->redirectToRoute('kvik_admin_terms', [
                        'type' => $type
                    ]);
                }
                else $this->container->get('kvik.postManager')->addPostsToUncategorized($term->getPosts());
            }
            else $type = 'tags';

            $em->remove($term);
            $em->flush();
            return $this->redirectToRoute('kvik_admin_terms', [
                'type' => $type
            ]);
        }
        else{
            $request->getSession()->getFlashBag()->add('notice', 'Cette page n\'existe pas !');
            return $this->redirectToRoute('kvik_admin_index');
        }
    }

}