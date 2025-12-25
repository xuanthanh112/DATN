<?php
/**
 * Script test phân quyền
 * Chạy: php test_permissions.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\UserCatalogue;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;

echo "=== TEST HỆ THỐNG PHÂN QUYỀN ===\n\n";

// 1. Kiểm tra các nhóm thành viên
echo "1. DANH SÁCH NHÓM THÀNH VIÊN:\n";
$userCatalogues = UserCatalogue::all();
foreach($userCatalogues as $uc) {
    echo "   - ID: {$uc->id} | Tên: {$uc->name} | Mô tả: {$uc->description}\n";
}
echo "\n";

// 2. Kiểm tra các quyền
echo "2. DANH SÁCH QUYỀN:\n";
$permissions = Permission::take(10)->get();
foreach($permissions as $p) {
    echo "   - ID: {$p->id} | Tên: {$p->name} | Canonical: {$p->canonical}\n";
}
echo "   (Hiển thị 10 quyền đầu tiên, tổng: " . Permission::count() . " quyền)\n\n";

// 3. Kiểm tra user và quyền của họ
echo "3. DANH SÁCH USER VÀ QUYỀN:\n";
$users = User::with('user_catalogues.permissions')->take(5)->get();
foreach($users as $user) {
    echo "   User: {$user->name} ({$user->email})\n";
    echo "   - Nhóm: " . ($user->user_catalogues ? $user->user_catalogues->name : 'Chưa có nhóm') . "\n";
    echo "   - Trạng thái: " . ($user->publish == 1 ? 'Hoạt động' : 'Không hoạt động') . "\n";
    if($user->user_catalogues) {
        $permissionCount = $user->user_catalogues->permissions->count();
        echo "   - Số quyền: {$permissionCount}\n";
        if($permissionCount > 0) {
            echo "   - Các quyền:\n";
            foreach($user->user_catalogues->permissions->take(5) as $perm) {
                echo "     • {$perm->canonical}\n";
            }
            if($permissionCount > 5) {
                echo "     ... và " . ($permissionCount - 5) . " quyền khác\n";
            }
        }
    }
    echo "\n";
}

// 4. Test Gate authorization
echo "4. TEST GATE AUTHORIZATION:\n";
$testUser = User::with('user_catalogues.permissions')->where('publish', 1)->first();
if($testUser) {
    echo "   Test với user: {$testUser->name}\n";
    
    // Test một số quyền phổ biến
    $testPermissions = ['product.index', 'product.create', 'order.index', 'warranty.index'];
    foreach($testPermissions as $perm) {
        try {
            $result = Gate::forUser($testUser)->allows('modules', $perm);
            $status = $result ? '✅ CÓ QUYỀN' : '❌ KHÔNG CÓ QUYỀN';
            echo "   - {$perm}: {$status}\n";
        } catch(\Exception $e) {
            echo "   - {$perm}: ❌ LỖI - {$e->getMessage()}\n";
        }
    }
} else {
    echo "   Không tìm thấy user nào để test\n";
}

echo "\n=== KẾT THÚC TEST ===\n";

