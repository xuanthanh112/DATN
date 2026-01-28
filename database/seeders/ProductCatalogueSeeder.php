<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCatalogue;
use App\Models\Language;
use Illuminate\Support\Facades\DB;

class ProductCatalogueSeeder extends Seeder
{
    /**
     * Tạo 1 danh mục sản phẩm mặc định (Nested Set: node gốc).
     * Cần có User id=1 và Language id=1.
     */
    public function run(): void
    {
        if (ProductCatalogue::count() > 0) {
            $this->command->info('Đã có danh mục sản phẩm, bỏ qua ProductCatalogueSeeder.');
            return;
        }

        if (!DB::table('users')->where('id', 1)->exists()) {
            $this->command->warn('Chưa có User id=1. Chạy: php create_admin_user.php');
            return;
        }

        if (!DB::table('languages')->where('id', 1)->exists()) {
            $this->command->warn('Chưa có Language id=1. Chạy: php artisan db:seed --class=LanguageSeeder');
            return;
        }

        $catalogue = ProductCatalogue::create([
            'parent_id' => 0,
            'lft' => 1,
            'rgt' => 2,
            'level' => 1,
            'publish' => 2,
            'follow' => 0,
            'order' => 0,
            'user_id' => 1,
        ]);

        $catalogue->languages()->attach(1, [
            'name' => 'Danh mục gốc',
            'canonical' => 'danh-muc-san-pham',
            'meta_title' => 'Danh mục sản phẩm',
            'meta_keyword' => null,
            'meta_description' => null,
            'description' => null,
            'content' => null,
        ]);

        $this->command->info('Đã tạo danh mục sản phẩm id=1: "Danh mục gốc".');
    }
}
