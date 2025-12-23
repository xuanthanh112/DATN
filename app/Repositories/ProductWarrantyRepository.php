<?php

namespace App\Repositories;

use App\Models\ProductWarranty;
use App\Repositories\Interfaces\ProductWarrantyRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * Class ProductWarrantyRepository
 * @package App\Repositories
 */
class ProductWarrantyRepository extends BaseRepository implements ProductWarrantyRepositoryInterface
{
    protected $model;

    public function __construct(
        ProductWarranty $model
    ){
        $this->model = $model;
    }

    public function getAllPaginate($request)
    {
        $perpage = $request->integer('perpage', 20);
        
        $warranties = $this->model->query()
            ->with(['customer', 'product', 'order'])
            ->when($request->filled('keyword'), function($query) use ($request){
                $keyword = $request->input('keyword');
                $query->where(function($q) use ($keyword){
                    $q->where('customer_name', 'LIKE', '%'.$keyword.'%')
                      ->orWhere('customer_phone', 'LIKE', '%'.$keyword.'%')
                      ->orWhere('customer_email', 'LIKE', '%'.$keyword.'%')
                      ->orWhere('product_name', 'LIKE', '%'.$keyword.'%')
                      ->orWhere('id', $keyword);
                });
            })
            ->when($request->filled('status'), function($query) use ($request){
                $query->where('status', $request->input('status'));
            })
            ->when($request->filled('start_date') && $request->filled('end_date'), function($query) use ($request){
                $query->whereBetween('activation_date', [
                    $request->input('start_date'),
                    $request->input('end_date')
                ]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perpage)
            ->withQueryString()
            ->withPath(env('APP_URL').'/admin/warranty/index');

        return $warranties;
    }

    public function getDetail($id)
    {
        return $this->model->with(['customer', 'product', 'order', 'provinces'])->findOrFail($id);
    }

    public function create(array $payload = [])
    {
        return $this->model->create($payload);
    }

    public function update(int $id = 0, array $payload = [])
    {
        $warranty = $this->getDetail($id);
        $warranty->update($payload);
        return $warranty;
    }

    public function delete(int $id = 0)
    {
        return $this->getDetail($id)->delete();
    }

    public function findByOrderProduct($orderId, $productUuid)
    {
        return $this->model
            ->where('order_id', $orderId)
            ->where('order_product_uuid', $productUuid)
            ->first();
    }

    public function getByCustomer($customerId, $request = null)
    {
        $query = $this->model->query()
            ->with(['product', 'order'])
            ->where('customer_id', $customerId);

        if ($request && $request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request && $request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function($q) use ($keyword){
                $q->where('product_name', 'LIKE', '%'.$keyword.'%')
                  ->orWhere('id', $keyword);
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function checkActivated($orderId, $productUuid)
    {
        return $this->model
            ->where('order_id', $orderId)
            ->where('order_product_uuid', $productUuid)
            ->exists();
    }

    public function getExpiring($days = 30)
    {
        return $this->model
            ->expiringWithin($days)
            ->with(['customer', 'product'])
            ->get();
    }

    public function getStatistics()
    {
        $total = $this->model->count();
        $active = $this->model->where('status', 'active')->count();
        $expired = $this->model->where('status', 'expired')->count();
        $thisMonth = $this->model->whereMonth('activation_date', now()->month)
                                  ->whereYear('activation_date', now()->year)
                                  ->count();
        $expiringSoon = $this->model->expiringWithin(30)->count();

        // Top products
        $topProducts = $this->model
            ->select('product_name', DB::raw('count(*) as total'))
            ->groupBy('product_name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // Monthly chart data
        $monthlyData = $this->model
            ->select(
                DB::raw('YEAR(activation_date) as year'),
                DB::raw('MONTH(activation_date) as month'),
                DB::raw('count(*) as total')
            )
            ->where('activation_date', '>=', now()->subMonths(11))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        return [
            'total' => $total,
            'active' => $active,
            'expired' => $expired,
            'this_month' => $thisMonth,
            'expiring_soon' => $expiringSoon,
            'top_products' => $topProducts,
            'monthly_data' => $monthlyData,
        ];
    }

    public function updateStatus($id, $status)
    {
        $warranty = $this->findById($id);
        $warranty->update(['status' => $status]);
        return $warranty;
    }
}

