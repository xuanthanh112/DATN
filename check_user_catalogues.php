<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\UserCatalogue;

echo "=== KIỂM TRA NHÓM THÀNH VIÊN ===\n\n";

$total = UserCatalogue::count();
echo "Tổng số nhóm: {$total}\n\n";

$all = UserCatalogue::all(['id', 'name', 'publish', 'description']);
echo "Danh sách tất cả nhóm:\n";
foreach($all as $uc) {
    $status = $uc->publish == 1 ? 'Hoạt động' : 'Không hoạt động';
    echo "  - ID: {$uc->id} | Tên: {$uc->name} | Trạng thái: {$status} | Mô tả: {$uc->description}\n";
}

echo "\n";
echo "Nhóm đang hoạt động (publish=1): " . UserCatalogue::where('publish', 1)->count() . "\n";
echo "Nhóm không hoạt động (publish=0): " . UserCatalogue::where('publish', 0)->count() . "\n";
echo "Nhóm đã xóa (soft delete): " . UserCatalogue::onlyTrashed()->count() . "\n";

