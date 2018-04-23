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
            $slug = $this->container->get('kvik.sanitize')->slugify($term->getSlug(), $term->getName());
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
        $term = $em->getRepository(Term::class)->getOneTerm($type, $id);
        $form = $this->createForm(TermType::class, $term, [
            'type' => $type
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $this->container->get('kvik.sanitize')->slugify($term->getSlug(), $term->getName());
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

    public function deleteAction(Request $request, $type, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $term = $em->getRepository(Term::class)->getOneTerm($type, $id);
        if ( $term !== null ){
            $em->remove($term);
            $em->flush();
            return $this->redirectToRoute('kvik_admin_terms', [
                'type' => $type
            ]);
        }
        else{
            $request->getSession()->getFlashBag()->add('notice', 'Cette page n\'existe pas !');
            return $this->redirectToRoute('kvik_admin_terms', [
                'type' => $type
            ]);
        }
    }

}