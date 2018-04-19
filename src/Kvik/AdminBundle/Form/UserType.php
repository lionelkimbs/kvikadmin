<?php

namespace Kvik\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $constraintsOptions = array(
            'message' => 'fos_user.current_password.invalid',
        );

        $builder
            ->add('name', TextType::class)
            ->add('firstname', TextType::class)
            ->add('presentation', TextareaType::class, [
                'required' => false
            ])
            ->add('displayedRole', ChoiceType::class, [
                'choices' => [
                    'Inscrit' => 'Inscrit',
                    'Editeur' => 'Editeur',
                    'Administrateur' => 'Administrateur',
                    'Super-administrateur' => 'Super-administrateur'
                ]
            ])
            ->add('enregistrer', SubmitType::class)
        ;
        if( $options['todo'] == 'edit' ){
            $builder
                ->remove('plainPassword')
                ->add('plainPassword', PasswordType::class, [
                    'required' => false
                ])
            ;
        }
        if( $options['todo'] == 'profile' ){
            $builder
                ->remove('plainPassword')
            ;
        }
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kvik\AdminBundle\Entity\User',
            'todo' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'kvik_adminbundle_user';
    }


}
