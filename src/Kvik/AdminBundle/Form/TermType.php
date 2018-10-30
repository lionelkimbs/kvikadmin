<?php

namespace Kvik\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Kvik\AdminBundle\Entity\Term;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TermType extends AbstractType
{
    private $obejct;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->obejct = $builder->getData();

        if( $options['type'] == 'newTag' ){
            $builder
                ->add('name', TextType::class)
                ->add('termType', HiddenType::class, [
                    'data' => 2
                ])
            ;
        }
        else{
            $builder
                ->add('name', TextType::class)
                ->add('slug', TextType::class, [
                    'required' => false
                ])
                ->add('resume', TextareaType::class, [
                    'required' => false
                ])
                ->add('enregistrer', SubmitType::class)
            ;
            //* Only for categories
            if ($options['type'] == 'categories' ){
                if ($options['todo'] == 'edit' ){
                    $builder
                        ->add('parent', EntityType::class, [
                            'class' => Term::class,
                            'query_builder' => function(EntityRepository $er){
                                return $er->createQueryBuilder('t')
                                    ->where('t.id != :id')
                                    ->setParameter('id', $this->obejct->getId())
                                    ->orderBy('t.name', 'ASC')
                                    ;
                            },
                            'choice_label' => 'name',
                            'required' => false,
                            'placeholder' => 'Aucun'
                        ])
                    ;
                }
                else {
                    $builder
                        ->add('parent', EntityType::class, [
                            'class' => Term::class,
                            'query_builder' => function(EntityRepository $er){
                                return $er->createQueryBuilder('t')
                                    ->where('t.termType = 1')
                                    ->orderBy('t.name', 'ASC')
                                    ;
                            },
                            'choice_label' => 'name',
                            'required' => false,
                            'placeholder' => 'Aucun'
                        ])
                    ;
                }
                $builder
                    ->add('termType', HiddenType::class, [
                        'data' => 1
                    ])
                ;
            }
            //* Only for tags
            if ($options['type'] == 'tags' ){
                $builder->add('termType', HiddenType::class, [
                    'data' => 2
                ]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kvik\AdminBundle\Entity\Term',
            'type' => null,
            'todo' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'kvik_adminbundle_term';
    }


}
