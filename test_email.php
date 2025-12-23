<?php

/**
 * Script test gửi email
 * Chạy: php test_email.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Mail;

echo "========================================\n";
echo "  TEST GỬI EMAIL QUA GMAIL\n";
echo "========================================\n\n";

// Kiểm tra cấu hình
echo "📋 Kiểm tra cấu hình:\n";
echo "   Mail Driver: " . config('mail.default') . "\n";
echo "   Mail Host: " . config('mail.mailers.smtp.host') . "\n";
echo "   Mail Port: " . config('mail.mailers.smtp.port') . "\n";
echo "   Mail Encryption: " . config('mail.mailers.smtp.encryption') . "\n";
echo "   Mail Username: " . config('mail.mailers.smtp.username') . "\n";
echo "   Mail From: " . config('mail.from.address') . "\n";
echo "   Mail From Name: " . config('mail.from.name') . "\n\n";

// Nhập email test
echo "Nhập email để test (hoặc Enter để dùng email từ config): ";
$testEmail = trim(fgets(STDIN));

if(empty($testEmail)){
    $testEmail = config('mail.from.address');
}

if(empty($testEmail) || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)){
    echo "❌ Email không hợp lệ!\n";
    exit(1);
}

echo "\n📧 Đang gửi email test đến: {$testEmail}\n";

try {
    Mail::raw('Đây là email test từ hệ thống VPHome. Nếu bạn nhận được email này, cấu hình Gmail đã thành công!', function($message) use ($testEmail) {
        $message->to($testEmail)
                ->subject('Test Email - VPHome System');
    });
    
    echo "✅ Email đã được gửi thành công!\n";
    echo "   Vui lòng kiểm tra hộp thư (kể cả thư mục Spam)\n";
    
} catch (\Exception $e) {
    echo "❌ Lỗi khi gửi email:\n";
    echo "   " . $e->getMessage() . "\n\n";
    echo "💡 Gợi ý:\n";
    echo "   1. Kiểm tra App Password trong .env\n";
    echo "   2. Đảm bảo đã bật 2-Step Verification\n";
    echo "   3. Kiểm tra firewall có chặn port 587 không\n";
    echo "   4. Xem log: storage/logs/laravel.log\n";
}

echo "\n✨ Hoàn tất!\n";

