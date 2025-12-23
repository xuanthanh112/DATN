<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Interfaces\OrderServiceInterface  as OrderService;
use App\Repositories\Interfaces\OrderRepositoryInterface as OrderRepository;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;


class OrderController extends Controller
{
    protected $orderService;
    protected $orderRepository;

    public function __construct(
        OrderService $orderService,
        OrderRepository $orderRepository,
        ProvinceRepository $provinceRepository,
    ){
        $this->orderService = $orderService;
        $this->orderRepository = $orderRepository;
        $this->provinceRepository = $provinceRepository;
    }

    public function index(Request $request){

        $this->authorize('modules', 'order.index');
        $orders = $this->orderService->paginate($request);
        $config = [
            'js' => [
                'backend/library/order.js',
                'backend/js/plugins/switchery/switchery.js',
                'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js',
                'backend/js/plugins/daterangepicker/daterangepicker.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'backend/css/plugins/daterangepicker/daterangepicker-bs3.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Order'
        ];
        $config['seo'] = __('messages.order');
        $template = 'backend.order.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'orders'
        ));
    }

    public function detail(Request $request, $id){
        $order = $this->orderRepository->getOrderById($id, ['products']);
        $order = $this->orderService->getOrderItemImage($order);

        $provinces = $this->provinceRepository->all();
        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/library/order.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ],
        ];
        
        $config['seo'] = __('messages.order');
        $template = 'backend.order.detail';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'order',
            'provinces',
        ));
    }

    public function delete($id){
        $this->authorize('modules', 'order.destroy');
        $config['seo'] = __('messages.order');
        $order = $this->orderRepository->findById($id);
        $template = 'backend.order.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'order',
            'config',
        ));
    }

    public function destroy($id){
        $this->authorize('modules', 'order.destroy');
        if($this->orderService->destroy($id)){
            return redirect()->route('order.index')->with('success','Xóa đơn hàng thành công');
        }
        return redirect()->route('order.index')->with('error','Xóa đơn hàng không thành công. Hãy thử lại');
    }

    public function destroyMultiple(Request $request){
        $this->authorize('modules', 'order.destroy');
        
        $idsJson = $request->input('id', '[]');
        $ids = json_decode($idsJson, true) ?? [];
        
        if(empty($ids)){
            return redirect()->route('order.index')->with('error','Vui lòng chọn ít nhất một đơn hàng để xóa');
        }
        
        if($this->orderService->destroyMultiple($ids)){
            return redirect()->route('order.index')->with('success','Xóa '.count($ids).' đơn hàng thành công');
        }
        return redirect()->route('order.index')->with('error','Xóa đơn hàng không thành công. Hãy thử lại');
    }

    public function trashed(Request $request){
        $this->authorize('modules', 'order.index');
        $orders = $this->orderService->paginateTrashed($request);
        $config = [
            'js' => [
                'backend/library/order.js',
            ],
            'model' => 'Order'
        ];
        $config['seo'] = [
            'index' => [
                'title' => 'Đơn hàng đã xóa',
                'table' => 'Danh sách đơn hàng đã xóa',
            ]
        ];
        $template = 'backend.order.trashed';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'orders'
        ));
    }

    public function restore($id){
        $this->authorize('modules', 'order.destroy');
        
        // Kiểm tra xem đơn hàng có tồn tại trong trashed không
        $trashedOrder = $this->orderRepository->findTrashedById($id);
        if(!$trashedOrder){
            return redirect()->route('order.trashed')->with('error','Không tìm thấy đơn hàng đã xóa với ID: ' . $id);
        }
        
        $orderCode = $trashedOrder->code;
        
        if($this->orderService->restore($id)){
            // Kiểm tra lại xem đơn hàng đã được restore chưa
            try {
                // Đợi một chút để đảm bảo database đã commit
                usleep(100000); // 0.1 giây
                
                $restoredOrder = $this->orderRepository->findById($id);
                if($restoredOrder && is_null($restoredOrder->deleted_at)){
                    // Clear cache nếu có
                    \Cache::forget('order_' . $id);
                    
                    // Redirect về trang index và clear tất cả filter để hiển thị đơn hàng vừa restore
                    // Không dùng keyword vì có thể gây conflict với filter khác
                    return redirect()->route('order.index')->with('success','Khôi phục đơn hàng ' . $orderCode . ' thành công. Đơn hàng đã được khôi phục. Vui lòng xóa filter ngày (nếu có) hoặc reload trang để xem.');
                } else {
                    return redirect()->route('order.trashed')->with('error','Khôi phục đơn hàng không thành công. Đơn hàng vẫn ở trạng thái đã xóa.');
                }
            } catch (\Exception $e) {
                \Log::error('Error checking restored order: ' . $e->getMessage());
                return redirect()->route('order.index')->with('success','Khôi phục đơn hàng ' . $orderCode . ' thành công. Vui lòng reload trang để xem.');
            }
        }
        return redirect()->route('order.trashed')->with('error','Khôi phục đơn hàng không thành công. Hãy thử lại');
    }

    public function restoreMultiple(Request $request){
        $this->authorize('modules', 'order.destroy');
        
        $idsJson = $request->input('id', '[]');
        $ids = json_decode($idsJson, true) ?? [];
        
        if(empty($ids)){
            return redirect()->route('order.trashed')->with('error','Vui lòng chọn ít nhất một đơn hàng để khôi phục');
        }
        
        if($this->orderService->restoreMultiple($ids)){
            return redirect()->route('order.index')->with('success','Khôi phục '.count($ids).' đơn hàng thành công');
        }
        return redirect()->route('order.trashed')->with('error','Khôi phục đơn hàng không thành công. Hãy thử lại');
    }

}
