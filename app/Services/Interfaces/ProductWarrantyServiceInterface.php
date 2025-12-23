<?php 

namespace App\Services\Interfaces;

/**
 * Interface ProductWarrantyServiceInterface
 * @package App\Services\Interfaces
 */
interface ProductWarrantyServiceInterface
{
    public function paginate($request);
    
    public function create($request);
    
    public function activateFromOrder($request);
    
    public function getDetail($id);
    
    public function getByCustomer($customerId, $request);
    
    public function exportExcel($request);
    
    public function getStatistics();
    
    public function expireWarranties();
}

