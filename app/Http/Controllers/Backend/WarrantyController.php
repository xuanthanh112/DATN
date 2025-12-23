<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\ProductWarrantyServiceInterface as ProductWarrantyService;

class WarrantyController extends Controller
{
    protected $productWarrantyService;

    public function __construct(
        ProductWarrantyService $productWarrantyService
    ){
        $this->productWarrantyService = $productWarrantyService;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'warranty.index');
        
        $warranties = $this->productWarrantyService->paginate($request);
        
        $config = $this->config();
        $config['seo'] = [
            'index' => [
                'title' => 'Quản lý bảo hành',
                'table' => 'Danh sách bảo hành sản phẩm',
            ]
        ];
        
        $template = 'backend.warranty.index';
        
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'warranties'
        ));
    }

    public function detail($id)
    {
        $this->authorize('modules', 'warranty.index');
        
        $warranty = $this->productWarrantyService->getDetail($id);
        
        $config = $this->config();
        $config['seo'] = [
            'detail' => [
                'title' => 'Chi tiết bảo hành #' . $id,
            ]
        ];
        
        $template = 'backend.warranty.detail';
        
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'warranty'
        ));
    }

    public function statistics()
    {
        $this->authorize('modules', 'warranty.index');
        
        $statistics = $this->productWarrantyService->getStatistics();
        
        $config = $this->config();
        $config['seo'] = [
            'statistics' => [
                'title' => 'Thống kê bảo hành',
            ]
        ];
        
        $template = 'backend.warranty.statistics';
        
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'statistics'
        ));
    }

    public function export(Request $request)
    {
        $this->authorize('modules', 'warranty.index');
        
        // TODO: Implement Excel export
        return back()->with('success', 'Tính năng đang phát triển');
    }

    private function config()
    {
        return [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ]
        ];
    }
}

