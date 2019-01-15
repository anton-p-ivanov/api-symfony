<?php

namespace App\Controller\Forms;

use App\Controller\RestController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FormsController
 * @package App\Controller\Forms
 *
 * @Route("/forms")
 */
class FormsController extends RestController
{
    /**
     * @var string
     */
    protected $repository = \App\Entity\Form\Form::class;
}
