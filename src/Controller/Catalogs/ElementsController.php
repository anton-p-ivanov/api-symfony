<?php

namespace App\Controller\Catalogs;

use App\Controller\RestController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ElementsController
 * @package App\Controller\Catalogs
 *
 * @Route("/catalogs/elements")
 */
class ElementsController extends RestController
{
    /**
     * @var string
     */
    protected $repository = \App\Entity\Catalog\Element::class;
}
