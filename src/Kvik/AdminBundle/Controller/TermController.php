<?php

namespace Kvik\AdminBundle\Controller;

use Kvik\AdminBundle\Entity\Term;
use Kvik\AdminBundle\Form\TermType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TermController extends Controller{

    public function indexAction(Request $request, $type)
    {
        $term = new Term();
        $form = $this->createForm(TermType::class, $term, ['type' => $type]);

        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid() ){
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

}