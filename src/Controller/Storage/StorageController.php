<?php

namespace App\Controller\Storage;

use App\Controller\RestController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class StorageController
 * @package App\Controller\Storage
 *
 * @Route("/storage")
 */
class StorageController extends RestController
{
    /**
     * @var string
     */
    protected $repository = \App\Entity\Storage\Storage::class;
}
