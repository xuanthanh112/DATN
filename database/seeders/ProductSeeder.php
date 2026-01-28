<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Language;
use App\Models\ProductCatalogue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Load dữ liệu sản phẩm mẫu.
     * Cần có: User id=1, Language id=1, ProductCatalogue id=1.
     */
    public function run(): void
    {
        if (!DB::table('users')->where('id', 1)->exists()) {
            $this->command->warn('Chưa có User id=1. Chạy: php create_admin_user.php');
            return;
        }

        if (!DB::table('languages')->where('id', 1)->exists()) {
            $this->command->warn('Chưa có Language id=1. Chạy: php artisan db:seed --class=LanguageSeeder');
            return;
        }

        if (!DB::table('product_catalogues')->where('id', 1)->exists()) {
            $this->command->warn('Chưa có ProductCatalogue id=1. Chạy: php artisan db:seed --class=ProductCatalogueSeeder');
            return;
        }

        $samples = [
            [
                'name' => 'Gối Massage Hồng Ngoại 20 Bi Okato Japan Chính Hãng',
                'price' => 998000,
                'code' => 'SP001',
                'made_in' => 'Japan',
            ],
            [
                'name' => 'Máy Massage Cổ Vai Gáy 6D Nhật Bản',
                'price' => 749000,
                'code' => 'SP002',
                'made_in' => 'Japan',
            ],
            [
                'name' => 'Bơm Lốp Kiêm Kích Bình Soulor Đa Năng Chính Hãng',
                'price' => 53200000,
                'code' => 'SP003',
                'made_in' => 'Việt Nam',
            ],
            [
                'name' => 'Bộ Kích Nổ Ô Tô 70Mai PS01-11100mAh Chính Hãng',
                'price' => 4200000,
                'code' => 'SP004',
                'made_in' => 'Trung Quốc',
            ],
            [
                'name' => 'Máy Hút Chân Không Đa Năng',
                'price' => 890000,
                'code' => 'SP005',
                'made_in' => 'Việt Nam',
            ],
        ];

        foreach ($samples as $item) {
            $canonical = Str::slug($item['name']);
            $product = Product::create([
                'product_catalogue_id' => 1,
                'user_id' => 1,
                'price' => $item['price'],
                'code' => $item['code'],
                'made_in' => $item['made_in'],
                'publish' => 2,
                'follow' => 1,
                'order' => 0,
                'variant' => 0,
            ]);

            $product->languages()->attach(1, [
                'name' => $item['name'],
                'canonical' => $canonical,
                'meta_title' => $item['name'],
                'meta_keyword' => null,
                'meta_description' => Str::limit($item['name'] . ' - Sản phẩm chính hãng.', 160),
                'description' => $item['name'],
                'content' => '<p>' . $item['name'] . '</p>',
            ]);

            $product->product_catalogues()->attach(1);
        }

        $this->command->info('Đã tạo ' . count($samples) . ' sản phẩm mẫu.');
    }
}
