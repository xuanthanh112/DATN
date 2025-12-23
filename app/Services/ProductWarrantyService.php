<?php

namespace App\Services;

use App\Services\Interfaces\ProductWarrantyServiceInterface;
use App\Repositories\Interfaces\ProductWarrantyRepositoryInterface as ProductWarrantyRepository;
use App\Repositories\Interfaces\OrderRepositoryInterface as OrderRepository;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Class ProductWarrantyService
 * @package App\Services
 */
class ProductWarrantyService extends BaseService implements ProductWarrantyServiceInterface
{
    protected $productWarrantyRepository;
    protected $orderRepository;
    protected $productRepository;

    public function __construct(
        ProductWarrantyRepository $productWarrantyRepository,
        OrderRepository $orderRepository,
        ProductRepository $productRepository
    ){
        $this->productWarrantyRepository = $productWarrantyRepository;
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
    }

    public function paginate($request)
    {
        return $this->productWarrantyRepository->getAllPaginate($request);
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $payload = $request->all();
            
            $warranty = $this->productWarrantyRepository->create($payload);
            
            DB::commit();
            return [
                'flag' => true,
                'warranty' => $warranty
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating warranty: ' . $e->getMessage());
            return [
                'flag' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function activateFromOrder($request)
    {
        DB::beginTransaction();
        try {
            $orderId = $request->input('order_id');
            $productUuid = $request->input('order_product_uuid');
            $customerId = Auth::guard('customer')->id();
            
            // Check if already activated
            if ($this->productWarrantyRepository->checkActivated($orderId, $productUuid)) {
                return [
                    'flag' => false,
                    'message' => 'Sản phẩm này đã được kích hoạt bảo hành'
                ];
            }
            
            // Get order
            $order = $this->orderRepository->getOrderById($orderId);
            
            if (!$order || $order->customer_id != $customerId) {
                return [
                    'flag' => false,
                    'message' => 'Không tìm thấy đơn hàng'
                ];
            }
            
            // Find product in order
            $orderProduct = $order->products->where('pivot.uuid', $productUuid)->first();
            
            if (!$orderProduct) {
                return [
                    'flag' => false,
                    'message' => 'Không tìm thấy sản phẩm trong đơn hàng'
                ];
            }
            
            // Get product warranty months
            $product = $this->productRepository->findById($orderProduct->id);
            $warrantyMonths = $product->warranty ?? 12;
            
            if ($warrantyMonths <= 0) {
                return [
                    'flag' => false,
                    'message' => 'Sản phẩm này không có bảo hành'
                ];
            }
            
            // Build address from order (already has ward_name, district_name, province_name from join)
            $addressParts = array_filter([
                $order->address,
                $order->ward_name,
                $order->district_name,
                $order->province_name,
            ]);
            $fullAddress = implode(', ', $addressParts);
            
            // Calculate dates
            $purchaseDate = $order->created_at->format('Y-m-d');
            $activationDate = now()->format('Y-m-d');
            $warrantyEndDate = now()->addMonths($warrantyMonths)->format('Y-m-d');
            
            // Create warranty
            $payload = [
                'order_id' => $orderId,
                'order_product_uuid' => $productUuid,
                'product_id' => $orderProduct->id,
                'product_name' => $orderProduct->pivot->name ?? $product->name,
                'product_code' => $product->code,
                'customer_id' => $customerId,
                'customer_name' => $order->fullname,
                'customer_phone' => $order->phone,
                'customer_email' => $order->email,
                'customer_address' => $fullAddress,
                'province_id' => $order->province_id,
                'district_id' => $order->district_id,
                'ward_id' => $order->ward_id,
                'purchase_date' => $purchaseDate,
                'activation_date' => $activationDate,
                'warranty_months' => $warrantyMonths,
                'warranty_end_date' => $warrantyEndDate,
                'status' => 'active',
                'customer_note' => $request->input('note'),
            ];
            
            $warranty = $this->productWarrantyRepository->create($payload);
            
            // TODO: Send email notification
            
            DB::commit();
            return [
                'flag' => true,
                'warranty' => $warranty,
                'message' => 'Kích hoạt bảo hành thành công!'
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error activating warranty: ' . $e->getMessage());
            return [
                'flag' => false,
                'message' => 'Có lỗi xảy ra. Vui lòng thử lại sau.'
            ];
        }
    }

    public function getDetail($id)
    {
        return $this->productWarrantyRepository->getDetail($id);
    }

    public function getByCustomer($customerId, $request)
    {
        return $this->productWarrantyRepository->getByCustomer($customerId, $request);
    }

    public function exportExcel($request)
    {
        // TODO: Implement Excel export
        return [];
    }

    public function getStatistics()
    {
        return $this->productWarrantyRepository->getStatistics();
    }

    public function expireWarranties()
    {
        try {
            $expiredCount = DB::table('product_warranties')
                ->where('status', 'active')
                ->whereDate('warranty_end_date', '<', now())
                ->update(['status' => 'expired', 'updated_at' => now()]);
            
            \Log::info("Expired {$expiredCount} warranties");
            
            return [
                'flag' => true,
                'count' => $expiredCount
            ];
        } catch (\Exception $e) {
            \Log::error('Error expiring warranties: ' . $e->getMessage());
            return [
                'flag' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}

