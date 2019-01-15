<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface IRestController
 * @package App\Controller
 */
interface IRestController
{
    /**
     * @return array
     */
    public function skipPermissionChecks(): array;

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function checkAccess(Request $request): bool;
}