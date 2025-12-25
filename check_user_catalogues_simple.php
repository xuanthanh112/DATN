<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\UserCatalogue;

$total = UserCatalogue::withTrashed()->count();
$active = UserCatalogue::count();
$deleted = UserCatalogue::onlyTrashed()->count();

echo "Tổng số nhóm (bao gồm đã xóa): {$total}\n";
echo "Nhóm đang hoạt động: {$active}\n";
echo "Nhóm đã xóa (soft delete): {$deleted}\n\n";

echo "Danh sách tất cả nhóm:\n";
$all = UserCatalogue::withTrashed()->get(['id', 'name', 'publish', 'description', 'deleted_at']);
foreach($all as $uc) {
    $status = $uc->publish == 1 ? 'Không xuất bản' : ($uc->publish == 2 ? 'Xuất bản' : 'Không xác định');
    $deleted = $uc->deleted_at ? ' (ĐÃ XÓA)' : '';
    echo "  - ID: {$uc->id} | Tên: {$uc->name} | Publish: {$uc->publish} ({$status}){$deleted}\n";
}

