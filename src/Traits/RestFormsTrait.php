<?php

namespace App\Traits;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trait RestFormsTrait
 * @package App\Traits
 */
trait RestFormsTrait
{
    /**
     * @param Request $request
     * @param FormInterface $form
     */
    protected function processForm(Request $request, FormInterface $form)
    {
        $clientData = json_decode($request->getContent(), true);
        if (!is_array($clientData)) {
            $clientData = [];
        }

        $normalizedClientData = [];
        foreach ($clientData as $key => $value) {
            if ($form->has($key) && !empty($value)) {
                $normalizedClientData[$key] = $value;
            }
        }

        $form->submit($normalizedClientData);
    }

    /**
     * @param FormInterface $form
     *
     * @return array
     */
    protected function getFormErrors(FormInterface $form): array
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getFormErrors($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        return $errors;
    }
}