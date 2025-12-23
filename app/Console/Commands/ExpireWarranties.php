<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Interfaces\ProductWarrantyServiceInterface as ProductWarrantyService;

class ExpireWarranties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'warranty:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động chuyển trạng thái bảo hành hết hạn';

    protected $productWarrantyService;

    public function __construct(ProductWarrantyService $productWarrantyService)
    {
        parent::__construct();
        $this->productWarrantyService = $productWarrantyService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Đang kiểm tra bảo hành hết hạn...');
        
        $result = $this->productWarrantyService->expireWarranties();
        
        if ($result['flag']) {
            $count = $result['count'];
            $this->info("Đã cập nhật {$count} bảo hành hết hạn.");
        } else {
            $this->error('Có lỗi xảy ra: ' . $result['message']);
        }
        
        return Command::SUCCESS;
    }
}
