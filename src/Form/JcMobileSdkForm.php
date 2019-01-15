<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class JcMobileSdkForm
 *
 * @package App\Form
 */
class JcMobileSdkForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $option
     */
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('form_field_contact_email', Type\EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email()
                ]
            ])
            ->add('form_field_contact_name', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('form_field_contact_phone', Type\TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('form_field_company_name', Type\TextType::class)
            ->add('form_field_company_position', Type\TextType::class)
            ->add('form_field_project_description', Type\TextareaType::class)
            ->add('form_field_project_platforms', Type\ChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'choices' => array_combine($this->getSystems(), $this->getSystems())
            ])
            ->add('form_field_project_additional_readers', Type\TextType::class)
            ->add('form_field_project_additional_sc', Type\TextType::class)
            ->add('form_field_project_customer', Type\TextType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => false
        ]);
    }

    /**
     * @return array
     */
    public function getSystems(): array
    {
        return [
            'Microsoft Windows',
            'Linux',
            'Apple Mac OS',
            'Apple iOS',
            'Google Android',
            'Microsoft Windows Phone'
        ];
    }
}