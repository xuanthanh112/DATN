<?php

/**
 * Script tạo admin user
 * Chạy: php create_admin_user.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\UserCatalogue;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "  TẠO ADMIN USER\n";
echo "========================================\n\n";

// 1. Kiểm tra UserCatalogue có tồn tại không
$adminCatalogue = UserCatalogue::where('id', 1)->first();
if (!$adminCatalogue) {
    echo "⚠️  UserCatalogue ID=1 không tồn tại!\n";
    echo "Đang tạo UserCatalogue 'Admin'...\n";
    
    $adminCatalogue = UserCatalogue::create([
        'name' => 'Admin',
        'description' => 'Quản trị viên hệ thống',
        'publish' => 2,
    ]);
    echo "✅ Đã tạo UserCatalogue ID=1\n\n";
}

// 2. Kiểm tra admin user đã tồn tại chưa
$adminUser = User::where('user_catalogue_id', 1)->first();

if ($adminUser) {
    echo "📧 Admin user đã tồn tại:\n";
    echo "   Email: {$adminUser->email}\n";
    echo "   Name: {$adminUser->name}\n";
    echo "   ID: {$adminUser->id}\n\n";
    
    // Reset password
    $adminUser->password = Hash::make('password');
    $adminUser->publish = 2;
    $adminUser->save();
    
    echo "✅ Đã reset password thành công!\n\n";
    echo "════════════════════════════════════\n";
    echo "   THÔNG TIN ĐĂNG NHẬP\n";
    echo "════════════════════════════════════\n";
    echo "Email:    {$adminUser->email}\n";
    echo "Password: password\n";
    echo "════════════════════════════════════\n";
} else {
    echo "⚠️  Admin user chưa tồn tại!\n";
    echo "Đang tạo admin user mới...\n\n";
    
    // Tạo admin user
    $adminUser = User::create([
        'name' => 'Administrator',
        'email' => 'admin@vphome.com',
        'password' => Hash::make('password'),
        'user_catalogue_id' => 1,
        'publish' => 2,
        'email_verified_at' => now(),
    ]);
    
    echo "✅ Đã tạo admin user thành công!\n\n";
    echo "════════════════════════════════════\n";
    echo "   THÔNG TIN ĐĂNG NHẬP\n";
    echo "════════════════════════════════════\n";
    echo "Email:    {$adminUser->email}\n";
    echo "Password: password\n";
    echo "════════════════════════════════════\n";
}

echo "\n✨ Hoàn tất!\n";


