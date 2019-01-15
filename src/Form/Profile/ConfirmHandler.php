<?php

namespace App\Form\Profile;

use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ConfirmHandler
 *
 * @package App\Form\Profile
 */
class ConfirmHandler
{
    /**
     * @var null|User
     */
    private $user;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * ResetHandler constructor.
     *
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, TranslatorInterface $translator)
    {
        $this->manager = $manager;
        $this->encoder = $encoder;
        $this->translator = $translator;
    }

    /**
     * @param Confirm $model
     * @param FormInterface $form
     *
     * @return bool
     */
    public function confirm(Confirm $model, FormInterface $form): bool
    {
        $queryBuilder = $this->manager->createQueryBuilder();

        $this->user = $queryBuilder->select(['u'])
            ->from('App:User\User', 'u')
            ->innerJoin('u.checkwords', 'c')
            ->where('c.checkword = :code AND u.isActive = :isActive')
            ->setParameters([
                'code' => $model->getCode(),
                'isActive' => true
            ])
            ->getQuery()
            ->getOneOrNullResult();

        // Validating an user
        if (!$this->user) {
            $message = $this->translator->trans('response.error.user_not_active');
            $form->get('password')->addError(new FormError($message));

            return false;
        }

        // Validating user password
        if (!$this->encoder->isPasswordValid($this->user, $model->getPassword())) {
            $message = $this->translator->trans('response.error.invalid_password');
            $form->get('password')->addError(new FormError($message));

            return false;
        }

        $this->user->setIsConfirmed(true);

        $this->manager->persist($this->user);
        $this->manager->flush();

        return $this->user->getIsConfirmed();
    }

    /**
     * @return null|User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
}