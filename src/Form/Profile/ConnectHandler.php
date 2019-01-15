<?php

namespace App\Form\Profile;

use App\Security\Encoder\PasswordEncoder;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

/**
 * Class ConnectHandler
 *
 * @package App\Form\Profile
 */
class ConnectHandler extends RegisterHandler
{
    /**
     * @param Connect $model
     * @param FormInterface $form
     *
     * @return bool
     */
    public function connect(Connect $model, FormInterface $form): bool
    {
        $this->user = $this->manager->getRepository('App:User\User')->findOneBy(['email' => $model->getEmail()]);

        // Validating user
        if (!$this->user) {
            $message = $this->translator->trans('response.error.user_not_found');
            $form->get('email')->addError(new FormError($message));

            return false;
        }

        $encoder = new PasswordEncoder();

        // Validating user password
        if (!$encoder->isPasswordValid($this->user->getPassword(), $model->getPassword(), $this->user->getSalt())) {
            $message = $this->translator->trans('response.error.invalid_password');
            $form->get('password')->addError(new FormError($message));

            return false;
        }

        return $this->registerInternal($model, $form);
    }
}