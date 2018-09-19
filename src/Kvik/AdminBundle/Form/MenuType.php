<?php

namespace Kvik\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            ->add('enregistrer', SubmitType::class)
        ;
        if( $options['todo'] == 'edit' ){
            $builder
                ->add('links', CollectionType::class, [
                    'entry_type' => LinkType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => false
                ])
            ;
        }
        else{
            $builder
                ->add('title');
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