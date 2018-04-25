<?php

namespace Kvik\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Kvik\AdminBundle\Entity\Term;
use Kvik\AdminBundle\Entity\User;
use Kvik\AdminBundle\Repository\TermRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    private $type;
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if( $options['type'] == 'post' ) $this->type = 1;
        elseif( $options['type'] == 'page' ) $this->type = 2;
        $builder
            ->add('title', TextType::class)
            ->add('body', TextareaType::class, [
                'required' => false
            ])
            ->add('excerpt', TextareaType::class, [
                'required' => false
            ])
            ->add('postStatus', ChoiceType::class, [
                'choices' => [
                    'Brouillon' => 1,
                    'Publié' => 2
                ]
            ])
            ->add('privacy', ChoiceType::class, [
                'choices' => [
                    'Publique' => 1,
                    'Privé' => 0
                ],
                'data' => 1
            ])
            ->add('commentStatus', ChoiceType::class, [
                'choices' => [
                    'Interdits' => 0,
                    'Autorisés' => 1
                ],
                'data' => 0,
                'expanded' => true
            ])
            ->add('postPassword', PasswordType::class, [
                'required' => false            ])
            ->add('datePub', DateTimeType::class, [
                'required' => false
            ])
            ->add('metadescription', TextareaType::class, [
                'required' => false
            ])
            ->add('terms', EntityType::class, [
                'class' => Term::class,
                'query_builder' => function(EntityRepository $tr){
                    return $tr->createQueryBuilder('t')
                        ->where('t.termType = :type')
                        ->setParameter('type', $this->type)
                        ->orderBy('t.name', 'ASC')
                    ;
                },
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('enregistrer', SubmitType::class)
        ;

        if( $options['todo'] == 'edit' ){
            $builder
                ->add('author', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => function($u){
                        return $u->getFirstname() .' '. $u->getName();
                    }
                ])
                ->add('dateEdit', DateTimeType::class, [
                    'data' => new \DateTime()
                ])
                ->add('slug', TextType::class)
            ;
        }
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kvik\AdminBundle\Entity\Post',
            'todo' => null,
            'type' => null,
            'user' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'kvik_adminbundle_post';
    }


}
