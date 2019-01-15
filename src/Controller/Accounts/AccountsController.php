<?php

namespace App\Controller\Accounts;

use App\Controller\RestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class UsersController
 * @package App\Controller\Accounts
 *
 * @Route("/accounts/accounts")
 */
class AccountsController extends RestController
{
    /**
     * @var string
     */
    protected $repository = \App\Entity\Account\Account::class;

    /**
     * List methods which can be executed WITH OUT permission checks.
     * @return array
     */
    public function skipPermissionChecks(): array
    {
        return ['getByCode'];
    }

    /**
     * @Route("/getByCode", methods={"GET"})
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     *
     * @return Response
     */
    public function getByCode(Request $request, TranslatorInterface $translator): Response
    {
        $code = $request->get('code');
        $status = Response::HTTP_BAD_REQUEST;
        $response = [
            'error' => 'invalid_request',
            'description' => $translator->trans('response.error.parameter_not_set', ['%name%' => 'code'])
        ];

        if ($code) {
            $accountCode = $this->getDoctrine()->getRepository('App:Account\Code')->findOneBy(['code' => $code]);
            if ($accountCode && $accountCode->isValid()) {
                return new JsonResponse($accountCode->getAccount(), Response::HTTP_OK);
            }

            $status = Response::HTTP_NOT_FOUND;
            $response = [
                'error' => 'entity_not_found',
                'description' => $translator->trans('response.error.account_not_found')
            ];
        }

        return new JsonResponse($response, $status);
    }
}
