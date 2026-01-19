<?php

namespace App\Repositories\Interfaces;

/**
 * Interface AttributeServiceInterface
 * @package App\Services\Interfaces
 */
interface OrderRepositoryInterface
{
    public function getTopProducts($limit = 10);
}
