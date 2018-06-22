<?php

namespace Kvik\AdminBundle\Services;

use Knp\Menu\FactoryInterface;

class MenuBuilder{
    private $factory;

    /**
     * MenuBuilder constructor.
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }
    
    public function createMainMenu(array $options){
        $menu = $this->factory->createItem('root');
        $menu->addChild('Home', [
            'route' => 'homepage'
        ]);
        return $menu;
    }
}
