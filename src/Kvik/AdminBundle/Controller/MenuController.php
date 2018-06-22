<?php

namespace Kvik\AdminBundle\Controller;

use Kvik\AdminBundle\Entity\Menu;
use Kvik\AdminBundle\Form\MenuType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class MenuController extends Controller{
    
    public function indexAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $menus = $em->getRepository(Menu::class)->findAll();
        //---- Formulaire pour ajouter un menu ----//
        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if( empty($menus) ) $menu->setSelected(true);
            $em->persist($menu);
            $em->flush();
            return $this->redirectToRoute('kvik_admin_menus');
        }

        //---- Formulaire pour sélectionner le menu à éditer ----//
        $formenu = $this->createFormBuilder()
            ->add('menu', EntityType::class, [
                'class' => Menu::class,
                'choice_label' => 'title',
                'label' => false
            ])
            ->add('valider', SubmitType::class)
            ->getForm()
        ;
        
        //---- Formulaire qui édite le menu ----//
        $menu_edited = !empty($request->query->get('menu')) ? $em->getRepository(Menu::class)->find($request->query->get('menu')) : $em->getRepository(Menu::class)->findOneBy([
            'selected' => true
        ]);
        $formEditor = $this->createForm(MenuType::class, $menu, [
            'todo' => 'edit'
        ]);
        $formEditor->handleRequest($request);
        if( $formEditor->isSubmitted() && $formEditor->isValid() ){
            $em->persist($menu);
        }
        

        return $this->render('KvikAdminBundle:Menu:index.html.twig', [
            'form'          => $form->createView(),
            'formenu'       => $formenu->createView(),
            'formEditor'    => $formEditor->createView(),
            'menus'         => $menus
        ]);
    }
}
