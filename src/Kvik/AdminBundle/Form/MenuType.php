<?php

namespace Kvik\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
        ;
        if( $options['todo'] == 'edit' ){
            $builder
                ->add('links', CollectionType::class, [
                    'entry_type' => LinkType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => false,
                    'prototype' => true,
                    'by_reference' => false
                ])
                ->add('sortable', HiddenType::class, [
                    'label' => false,
                    'by_reference' => false,
                    'mapped' => false
                ])
                ->add('enregistrer', SubmitType::class)
            ;
        }
        else{
            $builder
                ->add('valider', SubmitType::class)
            ;
        }
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kvik\AdminBundle\Entity\Menu',
            'todo' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'kvik_adminbundle_menu';
    }


}
