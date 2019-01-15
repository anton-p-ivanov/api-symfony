<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UsersController
 * @package App\Controller
 *
 * @Route("/users")
 */
class UsersController extends RestController
{
    /**
     * @var string 
     */
    protected $repository = \App\Entity\User\User::class;

    /**
     * @Route("/getByUsername", methods={"GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getByUsername(Request $request): Response
    {
        $username = $request->get('username');
        $status = Response::HTTP_BAD_REQUEST;
        $response = [
            'error' => 'Invalid request',
            'description' => 'Required parameter `username` is not set.'
        ];

        if ($username !== null) {
            $status = Response::HTTP_OK;
            $response = $this->getDoctrine()->getRepository($this->repository)->findOneBy(['email' => $username]);
            if (!$response) {
                $status = Response::HTTP_NOT_FOUND;
                $response = [
                    'error' => 'User not found',
                    'description' => sprintf('User identified by email `%s` not found.', $username)
                ];
            }
        }

        return JsonResponse::create($response, $status);
    }
}
