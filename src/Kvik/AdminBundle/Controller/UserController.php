<?php

namespace Kvik\AdminBundle\Controller;

use Kvik\AdminBundle\Entity\User;
use Kvik\AdminBundle\Form\UserType;
use Kvik\AdminBundle\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller{

    private $params;

    public function indexAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $this->params = [
            'pge' => $request->query->get('pge'),
            'active' => $request->query->get('active'),
        ];

        if( isset($this->params['active']) ){
            $active = $em->getRepository(User::class)->find($this->params['active']);
            $active->setEnabled(1);
            $em->persist($active);
            $em->flush();
            return $this->redirectToRoute('kvik_admin_users');
        }

        $form = $this->createFormBuilder()
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'expanded' => true,
                'multiple' => true,
                'query_builder' => function(UserRepository $ur){
                    return $ur->getOtherUsers($this->getUser(), $this->params)
                    ;
                },
                'label' => false
            ])
            ->add('enabled', ChoiceType::class, [
                'placeholder' => '-- Choisir dans la liste --',
                'choices' => [
                    'Activer' => 1,
                    'Désactiver' => 0,
                    'Supprimer' => 'remove'
                ],
                'required' => false
            ])
            ->add('appliquer', SubmitType::class)
            ->add('role', ChoiceType::class, [
                'placeholder' => '-- Changer de rôle --',
                'choices' => [
                    'Inscrit' => 'inscrit',
                    'Editeur' => 'editeur',
                    'Administrateur' => 'admin',
                    'Super-administrateur' => 'super-admin'
                ],
                'required' => false
            ])
            ->add('changer', SubmitType::class)
            ->getForm()
        ;
        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid() ) {
            $data = $form->getData();
            if( $form->get('appliquer')->isClicked() && !is_null($data['enabled']) ){
                foreach($data['user'] as $user){
                    if( $data['enabled'] === 'remove' ){
                        $em->remove($user);
                    }
                    else{
                        $user->setEnabled($data['enabled']);
                        $em->persist($user);
                    }
                }
                $em->flush();
            }
            elseif( $form->get('changer')->isClicked() && !is_null($data['role']) ){
                foreach($data['user'] as $user){
                    $user->setDisplayedRole($data['role']);
                    $this->container->get('kvik.userManager')->giveMeRole($user);
                    $em->persist($user);
                }
                $em->flush();
            }
            return $this->redirectToRoute('kvik_admin_users');
        }

        $users = $em->getRepository(User::class)->findAll();
        return $this->render('@KvikAdmin/User/index.html.twig', [
            'users' => $users,
            'form' => $form->createView(),
            'total' => (int) $em->getRepository(User::class)->getTotalOtherUsers($this->getUser())
        ]);
    }

    public function addAction(Request $request){
        $user = new User();
        $form = $this->createForm(UserType::class, $user, [
            'todo' => 'add'
        ]);
        if( $form->handleRequest($request)->isValid() && $request->isMethod('POST') ){
            $em = $this->getDoctrine()->getManager();
            $this->container->get('kvik.userManager')->giveMeRole($user);
            $user->setDateAdded(new \DateTime());
            $user->setDateUpdated(new \DateTime());
            $em->persist($user);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Utilisateur créé avec succès !');
            return $this->redirectToRoute('kvik_admin_user_edit', [
                'id' => $user->getId()
            ]);
        }
        return $this->render('@KvikAdmin/User/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function editAction($id = null, Request $request){
        $em = $this->getDoctrine()->getManager();
        //* Who's user is this ?
        if( $id == null ) $user = $this->getUser();
        else $user = $em->getRepository(User::class)->find($id);

        //* If user exists
        if ($user !== null){
            $form = $this->createForm(UserType::class, $user, ['todo' => 'edit']);
            if( $form->handleRequest($request)->isValid() && $request->isMethod('POST') ){
                $this->container->get('kvik.userManager')->giveMeRole($user);
                $user->setDateUpdated(new \DateTime());
                if( $user->getPlainPassword() == null  ) $user->setPassword($user->getPassword());
                $em->persist($user);
                $em->flush();
                $request->getSession()->getFlashBag()->add('notice', 'Modifications appliquées avec succès !');
                return $this->redirectToRoute('kvik_admin_user_edit', [
                    'id' => $user->getId()
                ]);
            }
            return $this->render('@KvikAdmin/User/edit.html.twig', [
                'form' => $form->createView(),
                'user' => $user
            ]);
        }
        else{
            $request->getSession()->getFlashBag()->add('notice', 'Cet utilisateur n\'existe pas !');
            return $this->redirectToRoute('kvik_admin_users');
        }
    }

    public function deleteAction($id, Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        if ($user !== null){
            $em->remove($user);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'L\'utilisateur a été supprimé avec succès !');
            return $this->redirectToRoute('kvik_admin_users');
        }
        else{
            $request->getSession()->getFlashBag()->add('notice', 'Cet utilisateur n\'existe pas !');
            return $this->redirectToRoute('kvik_admin_users');
        }
    }

}
