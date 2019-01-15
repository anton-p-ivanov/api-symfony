<?php

namespace App\Form\Profile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RegisterForm
 *
 * @package App\Form\Profile
 */
class RegisterForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $option
     */
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('code', TextType::class)
            ->add('email', TextType::class)
            ->add('password', PasswordType::class)
            ->add('fname', TextType::class)
            ->add('lname', TextType::class)
            ->add('position', TextType::class)
            ->add('roles', TextType::class);
//
//        $builder->get('roles')->addModelTransformer(new CallbackTransformer(
//            function ($data) { var_dump($data); return $data; },
//            function ($data) { var_dump($data); return $data; }
//        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Register::class,
            'translation_domain' => false
        ]);
    }
}