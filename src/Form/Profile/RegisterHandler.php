<?php

namespace App\Form\Profile;

use App\Entity\Account\Code;
use App\Entity\User\Account;
use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class RegisterHandler
 *
 * @package App\Form\Profile
 */
class RegisterHandler
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var EntityManagerInterface
     */
    protected $manager;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * ResetHandler constructor.
     *
     * @param EntityManagerInterface $manager
     * @param ValidatorInterface $validator
     * @param TranslatorInterface $translator
     */
    public function __construct(
        EntityManagerInterface $manager,
        ValidatorInterface $validator,
        TranslatorInterface $translator
    ) {
        $this->manager = $manager;
        $this->validator = $validator;
        $this->translator = $translator;
    }

    /**
     * @param Register $model
     * @param FormInterface $form
     *
     * @return bool
     */
    public function register(Register $model, FormInterface $form): bool
    {
        $this->user = new User();
        return $this->registerInternal($model, $form);
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param Register|Connect $model
     * @param FormInterface $form
     *
     * @return bool
     */
    protected function registerInternal($model, FormInterface $form): bool
    {
        $account = null;

        $this->setUserProperties($model);
        $this->setUserSites($model);
        $this->setUserRoles($model);

        // Link user to company
        if ($code = $model->getCode()) {
            $code = $this->manager->getRepository('App:Account\Code')->findOneBy(['code' => sha1($code)]);
            if ($code && $code->isValid()) {
                $account = $this->setUserAccount($code, $model);
                $this->manager->persist($account);
            }
            else {
                $message = $this->translator->trans('response.error.account_not_found');
                $form->get('code')->addError(new FormError($message));
                return false;
            }
        }

        // Validate user
        $violations = $this->validator->validate($this->user);
        if ($violations->count() > 0) {
            foreach ($violations as $violation) {
                $message = $this->translator->trans($violation->getMessage());
                $form->get($violation->getPropertyPath())->addError(new FormError($message));
            }

            return false;
        }

        $this->manager->persist($this->user);
        $this->manager->flush();

        return $this->user->getUuid() !== null;
    }

    /**
     * @param Connect|Register $model
     */
    private function setUserSites($model)
    {
        $sites = $this->manager->getRepository('App:Site')->findBy(['uuid' => $model->getSites()]);
        foreach ($sites as $site) {
            if (!$this->user->getSites()->contains($site)) {
                $this->user->getSites()->add($site);
            }
        }
    }

    /**
     * @param Connect|Register $model
     */
    private function setUserRoles($model)
    {
        $roles = $this->manager->getRepository('App:Role')->findBy(['code' => $model->getRoles()]);
        foreach ($roles as $role) {
            if (!$this->user->getRoles()->contains($role)) {
                $this->user->getRoles()->add($role);
            }
        }
    }

    /**
     * @param Code $code
     * @param Connect|Register $model
     *
     * @return Account
     */
    private function setUserAccount(Code $code, $model): Account
    {
        $account = new Account();
        $attributes = [
            'user' => $this->user,
            'account' => $code->getAccount(),
            'position' => $model->getPosition()
        ];
        foreach ($attributes as $name => $value) {
            $account->{'set' . ucfirst($name)}($value);
        }

        return $account;
    }

    /**
     * @param Connect|Register $model
     */
    private function setUserProperties($model)
    {
        $attributes = ['email', 'fname', 'lname', 'password'];
        foreach ($attributes as $attribute) {
            $this->user->{'set' . ucfirst($attribute)}($model->{'get' . ucfirst($attribute)}());
        }

        // Set user full name
        $fullNameParts = array_filter([
            $this->user->getLname(),
            $this->user->getFname(),
            $this->user->getSname()
        ], 'strlen');

        $this->user->setFullName(implode(' ', $fullNameParts));
    }
}