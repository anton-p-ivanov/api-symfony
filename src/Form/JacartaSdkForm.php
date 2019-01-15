<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class JacartaSdkForm
 *
 * @package App\Form
 */
class JacartaSdkForm extends AbstractType
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
            ->add('form_field_project_tasks', Type\ChoiceType::class, [
                'multiple' => true,
                'choices' => array_combine($this->getTasks(), $this->getTasks()),
            ])
            ->add('form_field_project_skzi', Type\ChoiceType::class, [
                'choices' => array_combine($this->getSkzi(), $this->getSkzi()),
            ])
            ->add('form_field_project_size', Type\ChoiceType::class, [
                'choices' => array_combine($this->getSizes(), $this->getSizes()),
            ])
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
    protected function getTasks(): array
    {
        return [
            'Электронная подпись',
            'Аутентификация на Web-порталах',
            'Аутентификация в приложениях',
            'Аутентификация в информационных системах',
            'Хранение идентификаторов/объектов',
            'Другие задачи'
        ];
    }

    /**
     * @return array
     */
    protected function getSizes(): array
    {
        return [
            'Неизвестно',
            'От 1 до 50',
            'От 51 до 500',
            'От 501 и больше'
        ];
    }

    /**
     * @return array
     */
    protected function getSkzi(): array
    {
        return [
            'Зарубежная (RSA, AES и т.п.)',
            'Российская (ГОСТ)'
        ];
    }
}