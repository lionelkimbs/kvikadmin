<?php

namespace Kvik\AdminBundle\Controller;

use Kvik\AdminBundle\Entity\Term;
use Kvik\AdminBundle\Form\TermType;
use Kvik\AdminBundle\Repository\TermRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class TermController extends Controller
{
    private $type;
    private $params;

    public function indexAction(Request $request, $type)
    {
        $params = [
            'pge' => $request->query->get('pge')
        ];
        $this->params = $params;
        $this->type = ($type == 'categories') ? 1 : 2;
        $em = $this->getDoctrine()->getManager();

        //*: Index form
        $formulaire = $this->createFormBuilder()
            ->add('term', EntityType::class, [
                'class' => Term::class,
                'choice_label' => 'id',
                'expanded' => true,
                'multiple' => true,
                'query_builder' => function(TermRepository $tr){
                    return $tr->getTheTerms($this->type, $this->params);
                },
                'label' => false
            ])
            ->add('action', ChoiceType::class, [
                'choices' => ['Supprimer' => 'remove'],
                'placeholder' => '-- Action groupée --'
            ])
            ->add('appliquer', SubmitType::class)
            ->getForm()
        ;
        $formulaire->handleRequest($request);
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $data = $formulaire->getData();
            if( !empty($data['term']) ){
                $loop = 0;
                foreach($data['term'] as $term) {
                    if ($term->getSlug() == 'uncategorized') {
                        $request->getSession()->getFlashBag()->add('notice', 'La catégorie par défaut ne peut être supprimée dans une action groupée.');
                    } else {
                        if( $term->getTermType() == 1 ){
                            $this->container->get('kvik.postManager')->addPostsToUncategorized($term->getPosts());
                            $loop++;
                        }
                        $em->remove($term);
                    }
                }
                if( $loop > 0 ) $request->getSession()->getFlashBag()->add('notice', 'Retrouvez tous les articles des catégories suppriémes dans la catégorie "Uncategorized".');
                $em->flush();
            }
            return $this->redirectToRoute('kvik_admin_terms', [
                'type' => $type
            ]);
        }

        //*: Form to add a Term
        $term = new Term();
        $form = $this->createForm(TermType::class, $term, ['type' => $type]);
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
            'formulaire' => $formulaire->createView(),
            'type' => $type,
            'total' => (int) $em->getRepository(Term::class)->getTotalTerms($type)
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
                else{
                    $this->container->get('kvik.postManager')->addPostsToUncategorized($term->getPosts());
                    $request->getSession()->getFlashBag()->add('notice', 'Retrouvez les articles de cette catégorie dans la catégorie par défaut "Uncategorized".');
                }
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