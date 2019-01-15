<?php

namespace App\Listener;

use App\Exceptions\JsonExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Class JsonExceptionListener
 * @package App\Listener
 */
class JsonExceptionListener
{
    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!$event->getException() instanceof JsonExceptionInterface) {
            return;
        }

        $response = new JsonResponse($this->buildResponseData($event->getException()));
        $response->setStatusCode($event->getException()->getCode());

        $event->setResponse($response);
    }

    /**
     * @param \Exception $exception
     *
     * @return mixed
     */
    private function buildResponseData(\Exception $exception)
    {
        return json_decode($exception->getMessage());
    }
}