<?php

namespace Kvik\AdminBundle\Controller;

use Doctrine\ORM\EntityRepository;
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
        $menu_edit = $em->getRepository(Menu::class)->find(!empty($request->query->get('menu_id')) ? $request->query->get('menu_id') : $em->getRepository(Menu::class)->findOneBy([], ['title' => 'desc'])->getId());

        //---- Formulaire pour ajouter un menu ----//
        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);
        if ($request->isMethod("POST") && $form->isSubmitted() && $form->isValid()) {
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
                'label' => false,
                'data' => $menu_edit
            ])
            ->add('valider', SubmitType::class)
            ->getForm()
        ;
        $formenu->handleRequest($request);
        if ($formenu->isSubmitted() && $formenu->isValid()) {
            return $this->redirectToRoute('kvik_admin_menus', [
                'menu_id' => $formenu->getData()['menu']->getId()
            ]);
        }
        
        //---- Formulaire qui édite le menu ----//
        $formEditor = $this->createForm(MenuType::class, $menu_edit, [
            'todo' => 'edit'
        ]);
        $formEditor->handleRequest($request);
        if( $formEditor->isSubmitted() && $formEditor->isValid() ){
            if( !empty($formEditor['sortable']->getData()) ){
                $tris = explode('&', str_replace('link[]=', '', $formEditor['sortable']->getData()) );
                foreach($menu_edit->getLinks() as $link){
                    $newposition = array_search($link->getPosition(), $tris);
                    $link->setPosition( $newposition );
                }
            }
            $em->persist($menu_edit);
            $em->flush();
            return $this->redirectToRoute('kvik_admin_menus', [
                'menu_id' => $menu_edit->getId()
            ]);

        }

        return $this->render('KvikAdminBundle:Menu:index.html.twig', [
            'form'          => $form->createView(),
            'formenu'       => $formenu->createView(),
            'formEditor'    => $formEditor->createView(),
            'menus'         => $menus
        ]);
    }
}
