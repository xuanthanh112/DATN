<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Tìm tất cả user có user_catalogue_id = 1
$admins = User::where('user_catalogue_id', 1)->get();

echo "=== DANH SÁCH ADMIN USER ===\n\n";

if ($admins->count() > 0) {
    foreach ($admins as $admin) {
        echo "ID: {$admin->id}\n";
        echo "Email: {$admin->email}\n";
        echo "Name: {$admin->name}\n";
        echo "Publish: {$admin->publish}\n";
        echo "---\n";
    }
    
    // Reset password cho admin đầu tiên
    $firstAdmin = $admins->first();
    $firstAdmin->password = Hash::make('password');
    $firstAdmin->publish = 2;
    $firstAdmin->save();
    
    echo "\n✅ Đã reset password!\n";
    echo "\n=== THÔNG TIN ĐĂNG NHẬP ===\n";
    echo "Email: {$firstAdmin->email}\n";
    echo "Password: password\n";
} else {
    echo "❌ Không tìm thấy admin user nào!\n";
    echo "\nĐang tạo admin user mới...\n";
    
    $admin = User::create([
        'name' => 'Admin',
        'email' => 'admin@vphome.com',
        'password' => Hash::make('password'),
        'user_catalogue_id' => 1,
        'publish' => 2,
        'email_verified_at' => now(),
    ]);
    
    echo "✅ Đã tạo admin user!\n";
    echo "\n=== THÔNG TIN ĐĂNG NHẬP ===\n";
    echo "Email: {$admin->email}\n";
    echo "Password: password\n";
}

