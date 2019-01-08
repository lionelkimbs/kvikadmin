<?php

namespace Kvik\AdminBundle\Controller;

use Kvik\AdminBundle\Entity\Link;
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
        if ($request->isMethod("POST") && $form->isSubmitted() && $form->isValid()) {
            if( empty($menus) ) $menu->setSelected(true);
            $em->persist($menu);
            $em->flush();
            return $this->redirectToRoute('kvik_admin_menus');
        }
        
        $last_menu = $em->getRepository(Menu::class)->findOneBy([], ['title' => 'desc']);
        $menu_id = $request->query->get('menu_id');
        if( $last_menu !== null || !empty($menu_id) ) $menu_edit = $em->getRepository(Menu::class)->find(!empty($menu_id) ? $menu_id : $last_menu->getId() );
        else $menu_edit = null;
        
        //Si on a un menu à modifier
        if( $menu_edit !== null ){
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
                $origins = $em->getRepository(Link::class)->findBy(['menu' => $menu_edit]);

                //  Cas1 : Tous les menus on été supprimé
                if( empty($formEditor['sortable']->getData()) ){
                    if( !empty($origins) ) {
                        foreach ($origins as $link) $em->remove($link);
                    }
                    $em->persist($menu_edit);
                    $em->flush();
                    return $this->redirectToRoute('kvik_admin_menus', ['menu_id' => $menu_edit->getId()]);
                }
                
                //  Cas2 : Tous les autres cas
                else{
                    //  Si le menu possède déjà des liens on s'assure de ne conserver que les liens qui sont toujours là
                    if(!empty($origins)){
                        foreach($origins as $link){
                            if(false === $menu_edit->getLinks()->contains($link) ) $em->remove($link);
                        }
                    }
                    
                    //  Si on reçoit bel et bien des liens triés
                    if(!empty($formEditor['sortable']->getData())){
                        if(strpos(';', $formEditor['sortable']->getData()) !== false){
                            $array = explode(';', $formEditor['sortable']->getData());
                            $list = explode(',', preg_replace('(\[|]|\"|link-)', '', $array[0]));
                            $sublist = explode(';', str_replace('},{', '};{', preg_replace('(\[|]|link-)', '', $array[1])));
                            for($i=0; $i<=count($sublist)-1; $i++){
                                $sublist[$i] = json_decode($sublist[$i]);
                            }
                        } 
                        else{
                            $list = explode(',', preg_replace('(\[|]|\"|link-)', '', $formEditor['sortable']->getData()));
                            $sublist = '';
                        }
                        
                        foreach($menu_edit->getLinks() as $link){
                            if(!empty($sublist)){
                                foreach ($sublist as $value){
                                    if( $link->getPosition() == $value->element ){
                                        foreach($menu_edit->getLinks() as $lien){
                                            if( $lien->getPosition() == $value->parent ) $link->setParent($lien);
                                        }
                                    }
                                }
                            }
                            $link->setPosition( array_search($link->getPosition(), $list) );
                        }
                        $em->persist($menu_edit);
                        $em->flush();
                    }
                    
                    return $this->redirectToRoute('kvik_admin_menus', [
                        'menu_id' => $menu_edit->getId()
                    ]);
                }
            }
            return $this->render('KvikAdminBundle:Menu:index.html.twig', [
                'form'          => $form->createView(),
                'formenu'       => $formenu->createView(),
                'formEditor'    => $formEditor->createView(),
                'menus'         => $menus,
                'menuedit'         => $menu_edit
            ]);
        }
        
        else{
            return $this->render('KvikAdminBundle:Menu:index.html.twig', [
                'form'          => $form->createView(),
                'menus'         => $menus
            ]);
        }
    }
}
