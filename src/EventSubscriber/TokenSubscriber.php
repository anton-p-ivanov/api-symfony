<?php

namespace App\EventSubscriber;

use App\Controller\IRestController;
use App\Exceptions\OAuth\InvalidAccessTokenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class TokenSubscriber
 * @package App\EventSubscriber
 */
class TokenSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    /**
     * @param FilterControllerEvent $event
     *
     * @throws InvalidAccessTokenException
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof IRestController) {
            $token = $event->getRequest()->headers->get('Access-Token');

            $skipPermissionChecks = $controller[0]->skipPermissionChecks();
            if (in_array($controller[1], $skipPermissionChecks)) {
                return;
            }

            if (!$token) {
                throw new InvalidAccessTokenException('No Access-Token header found in request');
            }
        }
    }
}