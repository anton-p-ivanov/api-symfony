<?php

namespace App\Controller;

use App\Form\BaseHandler;
use App\Form\BaseHandlerInterface;
use App\Tools\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller
 */
class RestController extends Controller implements IRestController
{
    /**
     * @var null|string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $repository;

    /**
     * List methods which can be executed WITH OUT permission checks.
     * @return array
     */
    public function skipPermissionChecks(): array
    {
        return [];
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function checkAccess(Request $request): bool
    {
        return true;
    }

    /**
     * Get a list of entities.
     *
     * @Route("/", methods={"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function list(Request $request): JsonResponse
    {
        $conditions = $request->headers->get('Search-Conditions', "[]");
        $page = (int) $request->headers->get('X-Pagination-Page', 1);
        $size = (int) $request->headers->get('X-Pagination-Size', 10);

        $conditions = json_decode($conditions, true);
        if (array_key_exists('limit', $conditions)) {
            $size = (int) $conditions['limit'];
        }

        $repository = $this->getDoctrine()->getRepository($this->repository);

        if (method_exists($repository, 'search')) {
            $query = $repository->search($conditions);
            $paginator = new Paginator($query, $page, $size);
        }
        else {
            throw new \Exception(sprintf('Repository `%s` does not have required method `search`.', $this->repository));
        }

        $headers = [
            'X-Pagination-Page' => $page,
            'X-Pagination-Size' => $size,
            'X-Pagination-Total' => $paginator->getResulsTotal()
        ];

        return JsonResponse::create($paginator->getIterator(), JsonResponse::HTTP_OK, $headers);
    }

    /**
     * Insert a new entity.
     *
     * @Route("/", methods={"POST"})
     *
     * @param Request $request
     * @param BaseHandler $handler
     *
     * @return JsonResponse
     */
//    public function create(Request $request, BaseHandler $handler): JsonResponse
//    {
//        if (!$this->checkAccess($request)) {
//            return JsonResponse::create([], JsonResponse::HTTP_FORBIDDEN);
//        }
//
//        return JsonResponse::create([], JsonResponse::HTTP_CREATED);
//    }

    /**
     * Get specific entity.
     *
     * @Route("/{uuid<\w{8}(\-\w{4}){3}\-\w{12}>}", methods={"GET"})
     *
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function show(string $uuid): JsonResponse
    {
        $status = JsonResponse::HTTP_OK;
        $response = $this->getDoctrine()->getRepository($this->repository)->find($uuid);

        if ($response === null) {
            $status = JsonResponse::HTTP_NOT_FOUND;
            $response = [
                'error' => 'Entity not found',
                'description' => sprintf(
                    'Entity of class `%s` with identifier `%s` not found.',
                    $this->repository,
                    $uuid
                ),
            ];
        }

        return JsonResponse::create($response, $status);
    }

    /**
     * Update specific entity.
     *
     * @Route("/{uuid<\w{8}(\-\w{4}){3}\-\w{12}>}", methods={"PUT"})
     *
     * @param string $uuid
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function update(string $uuid, Request $request): JsonResponse
    {
        if (!$this->checkAccess($request)) {
            return JsonResponse::create([], JsonResponse::HTTP_FORBIDDEN);
        }

        $status = JsonResponse::HTTP_OK;
        $response = $this->getDoctrine()->getRepository($this->repository)->find($uuid);

        if ($response === null) {
            $status = JsonResponse::HTTP_NOT_FOUND;
            $response = [
                'error' => 'Entity not found',
                'description' => sprintf(
                    'Entity of class `%s` with identifier `%s` not found.',
                    $this->repository,
                    $uuid
                ),
            ];
        }

        return JsonResponse::create($response, $status);
    }

    /**
     * Delete specific entity.
     *
     * @Route("/{uuid<\w{8}(\-\w{4}){3}\-\w{12}>}", methods={"DELETE"})
     *
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function delete(string $uuid): JsonResponse
    {
        $status = JsonResponse::HTTP_NO_CONTENT;
        $response = null;

        $entity = $this->getDoctrine()->getRepository($this->repository)->find($uuid);
        if ($entity === null) {
            $status = JsonResponse::HTTP_NOT_FOUND;
            $response = [
                'error' => 'Entity not found',
                'description' => sprintf(
                    'Entity of class `%s` with identifier `%s` not found.',
                    $this->repository,
                    $uuid
                ),
            ];
        }

        return JsonResponse::create($response, $status);
    }
}
