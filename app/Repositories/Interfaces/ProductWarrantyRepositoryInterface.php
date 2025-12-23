<?php 

namespace App\Repositories\Interfaces;

/**
 * Interface ProductWarrantyRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface ProductWarrantyRepositoryInterface
{
    public function getAllPaginate($request);
    
    public function create(array $payload = []);
    
    public function getDetail($id);
    
    public function update(int $id = 0, array $payload = []);
    
    public function delete(int $id = 0);
    
    public function findByOrderProduct($orderId, $productUuid);
    
    public function getByCustomer($customerId, $request = null);
    
    public function checkActivated($orderId, $productUuid);
    
    public function getExpiring($days = 30);
    
    public function getStatistics();
    
    public function updateStatus($id, $status);
}

