<?php

namespace App\Controller\Forms;

use App\Controller\RestController;
use App\Entity\Form as Form;
use App\Form\BaseHandler;
use App\Response\InvalidDataResponse;
use App\Traits\RestFormsTrait;
use Symfony\Component\HttpFoundation as Http;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ResultsController
 * @package App\Controller\Forms
 *
 * @Route("/forms/{form<\w{8}(\-\w{4}){3}\-\w{12}>}/results")
 */
class ResultsController extends RestController
{
    use RestFormsTrait;

    /**
     * @var string
     */
    protected $repository = Form\Result::class;

    /** @noinspection PhpSignatureMismatchDuringInheritanceInspection */

    /**
     * Insert a new entity.
     *
     * @Route("/", methods={"POST"})
     *
     * @param Http\Request $request
     * @param BaseHandler $handler
     *
     * @return Http\JsonResponse
     */
    public function create(Http\Request $request, BaseHandler $handler): Http\JsonResponse
    {
        $webForm = $this->getDoctrine()->getRepository(Form\Form::class)->find($request->get('form'));

        $code = strtolower($webForm->getCode());
        $code = implode(array_map(function ($value) { return ucfirst($value); }, explode("_", $code)));
        $className = "App\\Form\\$code";

        if (!class_exists($className)) {
            return Http\JsonResponse::create([
                'error' => 'invalid_request',
                'error_description' => 'Invalid attribute `_className` value.'
            ], Http\JsonResponse::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm($className);
        $this->processForm($request, $form);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($handler->process($form->getData(), $webForm)) {
                return Http\JsonResponse::create($handler->getResult(), Http\JsonResponse::HTTP_CREATED);
            }
        }

        return new InvalidDataResponse($this->getFormErrors($form));
    }
}
