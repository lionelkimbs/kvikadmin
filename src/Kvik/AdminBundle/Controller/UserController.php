<?php

namespace Kvik\AdminBundle\Controller;

use Kvik\AdminBundle\Entity\User;
use Kvik\AdminBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller{

    public function indexAction(){
        return $this->render('@KvikAdmin/User/index.html.twig');
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
            $em->persist($user);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Utilisateur créé avec succès !');
            return $this->redirectToRoute('kvik_admin_users');
        }
        return $this->render('@KvikAdmin/User/add.html.twig', [
            'form' => $form->createView()
        ]);
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
