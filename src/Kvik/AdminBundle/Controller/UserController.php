<?php

namespace Kvik\AdminBundle\Controller;

use Kvik\AdminBundle\Entity\User;
use Kvik\AdminBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller{

    public function indexAction(){
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->findAll();
        return $this->render('@KvikAdmin/User/index.html.twig', [
            'users' => $users
        ]);
    }

    public function addAction(Request $request){
        $user = new User();
        $form = $this->createForm(UserType::class, $user, [
            'todo' => 'add'
        ]);
        if( $form->handleRequest($request)->isValid() && $request->isMethod('POST') ){
            $em = $this->getDoctrine()->getManager();
            $this->giveUserRole($user);
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
                $this->giveUserRole($user);
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

    //Assign a single role to the user
    private function giveUserRole(User $user){
        switch ($user->getDisplayedRole()){
            case 'Inscrit':
                $user->addRole('ROLE_USER');
                break;
            case 'Editeur':
                $user->addRole('ROLE_EDITOR');
                break;
            case 'Administrateur':
                $user->addRole('ROLE_ADMIN');
                break;
            case 'Super-administrateur':
                $user->addRole('ROLE_SUPER_ADMIN');
                break;
        }
    }
}
