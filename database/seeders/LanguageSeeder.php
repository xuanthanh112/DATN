<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Tạo ít nhất 1 ngôn ngữ (Tiếng Việt) để dùng cho ProductSeeder.
     * Cần có User id=1 trước khi chạy.
     */
    public function run(): void
    {
        if (Language::count() > 0) {
            $this->command->info('Đã có dữ liệu ngôn ngữ, bỏ qua LanguageSeeder.');
            return;
        }

        $userId = 1;
        if (!DB::table('users')->where('id', $userId)->exists()) {
            $this->command->warn('Chưa có User id=1. Chạy: php create_admin_user.php');
            return;
        }

        Language::create([
            'name' => 'Tiếng Việt',
            'canonical' => 'vi',
            'image' => 'vn.png',
            'user_id' => $userId,
            'publish' => 2,
        ]);

        $this->command->info('Đã tạo ngôn ngữ: Tiếng Việt (vi).');
    }
}
