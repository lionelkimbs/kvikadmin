<?php

namespace Kvik\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Kvik\AdminBundle\Entity\Post;
use Kvik\AdminBundle\Entity\Term;
use Kvik\AdminBundle\Entity\User;
use Kvik\AdminBundle\Repository\PostRepository;
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
    private $post;
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->type = $options['type'];
        $this->post = $builder->getData();
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
                    'Brouillon' => 'draft',
                    'Publié' => 'publish'
                ]
            ])
            ->add('privacy', ChoiceType::class, [
                'choices' => [
                    'Publique' => 1,
                    'Privé' => 0
                ],
                'data' => 1
            ])
            ->add('postPassword', PasswordType::class, [
                'required' => false            ])
            ->add('datePub', DateTimeType::class, [
                'required' => false
            ])
            ->add('metadescription', TextareaType::class, [
                'required' => false
            ])
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choice_label' => function($u){
                    return $u->getFirstname() .' '. $u->getName();
                }
            ])
            ->add('enregistrer', SubmitType::class)
        ;
        if( $options['type'] == 'post' ){
            $builder
                ->add('commentStatus', ChoiceType::class, [
                    'choices' => [
                        'Interdits' => 0,
                        'Autorisés' => 1
                    ],
                    'data' => 0,
                    'expanded' => true
                ])
                ->add('terms', EntityType::class, [
                    'class' => Term::class,
                    'query_builder' => function(EntityRepository $tr){
                        return $tr->createQueryBuilder('t')
                            ->where('t.termType = :type')
                            ->setParameter('type', 1)
                            ->orderBy('t.name', 'ASC')
                            ;
                    },
                    'choice_label' => 'name',
                    'multiple' => true,
                    'expanded' => true
                ])
            ;
        }
        if( $options['type'] == 'page' ){
            $builder
                ->add('parent', EntityType::class, [
                    'class' => Post::class,
                    'query_builder' => function(PostRepository $pr){
                        return $pr->getOtherPosts($this->post, $this->type);
                    },
                    'choice_label' => 'title',
                    'expanded' => true,
                    'label' => false
                ])
            ;
        }
        if( $options['todo'] == 'edit' ){
            $builder
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
            'type' => null
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
