<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

echo "=== Bắt đầu tạo đơn hàng fake ===\n\n";

// Lấy danh sách customer và product (với tên sản phẩm từ ngôn ngữ tiếng Việt - language_id = 1)
$customers = Customer::all();
$products = Product::where('publish', 2)
    ->whereHas('languages', function($query) {
        $query->where('language_id', 1);
    })
    ->with(['languages' => function($query) {
        $query->where('language_id', 1);
    }])
    ->get();

if ($customers->isEmpty()) {
    die("Không có customer nào trong database!\n");
}

if ($products->isEmpty()) {
    die("Không có product nào trong database!\n");
}

echo "Số lượng customers: " . $customers->count() . "\n";
echo "Số lượng products: " . $products->count() . "\n\n";

// Mảng tỉnh thành phổ biến (code thực tế từ database)
$provinces = ['1', '79', '48', '92', '31']; // Hà Nội, TP.HCM, Đà Nẵng, Cần Thơ, Hải Phòng
$districts = ['1', '2', '3', '4', '5'];
$wards = ['00001', '00002', '00003', '00004', '00005'];

// Phương thức thanh toán - QUAN TRỌNG: Dùng 'cancle' không phải 'cancel'
$methods = ['cod', 'banking', 'vnpay'];
$confirms = ['pending', 'confirm', 'cancle'];  // ← Sửa từ 'cancel' thành 'cancle'
$payments = ['unpaid', 'paid'];
$deliveries = ['pending', 'delivering', 'success', 'cancle']; // ← Sửa từ 'cancel' thành 'cancle'

// Tạo đơn hàng từ 11/1 đến 28/1/2026
$startDate = new DateTime('2026-01-11');
$endDate = new DateTime('2026-01-28');

$totalOrders = 0;
$ordersByDate = [];

// Duyệt qua từng ngày
for ($date = clone $startDate; $date <= $endDate; $date->modify('+1 day')) {
    // Mỗi ngày tạo từ 2-5 đơn hàng
    $ordersPerDay = rand(2, 5);
    $ordersByDate[$date->format('Y-m-d')] = 0;
    
    for ($i = 0; $i < $ordersPerDay; $i++) {
        try {
            DB::beginTransaction();
            
            // Random customer
            $customer = $customers->random();
            
            // Tạo code đơn hàng duy nhất
            $orderCode = 'ORD' . $date->format('ymd') . str_pad($i + 1, 3, '0', STR_PAD_LEFT);
            
            // Random giờ trong ngày
            $hour = rand(8, 20);
            $minute = rand(0, 59);
            $second = rand(0, 59);
            $orderDateTime = clone $date;
            $orderDateTime->setTime($hour, $minute, $second);
            
            // Random địa chỉ
            $province = $provinces[array_rand($provinces)];
            $district = $districts[array_rand($districts)];
            $ward = $wards[array_rand($wards)];
            
            // Random số lượng sản phẩm trong đơn (1-4 sản phẩm)
            $numProducts = rand(1, min(4, $products->count()));
            $orderProducts = $products->random($numProducts);
            
            $cartData = [];
            $totalAmount = 0;
            
            foreach ($orderProducts as $product) {
                // Lấy tên sản phẩm từ pivot table
                $productName = $product->languages->first()->pivot->name ?? 'Sản phẩm không có tên';
                
                $qty = rand(1, 3);
                $price = $product->price;
                $priceOriginal = $product->price;
                
                $cartData[] = [
                    'id' => $product->id,
                    'name' => $productName,
                    'qty' => $qty,
                    'price' => $price,
                    'priceOriginal' => $priceOriginal,
                ];
                
                $totalAmount += ($price * $qty);
            }
            
            // Random shipping fee
            $shipping = rand(0, 1) ? rand(15000, 50000) : 0;
            
            // Random trạng thái (bias về đơn thành công)
            $confirm = $confirms[array_rand($confirms)];
            if ($confirm === 'confirm') {
                $payment = rand(0, 100) < 70 ? 'paid' : 'unpaid'; // 70% đã thanh toán
                $delivery = $deliveries[array_rand($deliveries)];
                
                // Nếu đơn confirm, tăng tỷ lệ giao thành công
                if (rand(0, 100) < 60) {
                    $delivery = 'success';
                    $payment = 'paid';
                }
            } else {
                $payment = 'unpaid';
                $delivery = 'pending';
            }
            
            $method = $methods[array_rand($methods)];
            
            // Tạo đơn hàng - Dùng DB::table() thay vì Model để tránh Laravel ghi đè timestamp
            $orderId = DB::table('orders')->insertGetId([
                'code' => $orderCode,
                'fullname' => $customer->name,
                'phone' => $customer->phone ?: '0' . rand(900000000, 999999999),
                'email' => $customer->email,
                'province_id' => $province,
                'district_id' => $district,
                'ward_id' => $ward,
                'address' => $customer->address ?: 'Số ' . rand(1, 999) . ', Đường ' . rand(1, 50),
                'description' => rand(0, 100) < 30 ? 'Giao hàng giờ hành chính' : null,
                'promotion' => null,
                'cart' => json_encode([
                    'cartTotal' => $totalAmount,
                    'cartPromotion' => 0,
                ]),
                'customer_id' => $customer->id,
                'guest_cookie' => null,
                'method' => $method,
                'confirm' => $confirm,
                'payment' => $payment,
                'delivery' => $delivery,
                'shipping' => $shipping,
                'created_at' => $orderDateTime->format('Y-m-d H:i:s'),
                'updated_at' => $orderDateTime->format('Y-m-d H:i:s'),
            ]);
            
            // Tạo order_product
            foreach ($orderProducts as $index => $product) {
                $cartItem = $cartData[$index];
                
                DB::table('order_product')->insert([
                    'order_id' => $orderId,
                    'product_id' => $product->id,
                    'uuid' => Uuid::uuid4()->toString(),
                    'name' => $cartItem['name'], // Lấy từ cartData đã xử lý
                    'qty' => $cartItem['qty'],
                    'price' => $cartItem['price'],
                    'priceOriginal' => $cartItem['priceOriginal'],
                    'option' => json_encode([]),
                ]);
            }
            
            DB::commit();
            $totalOrders++;
            $ordersByDate[$date->format('Y-m-d')]++;
            
            echo "✓ Tạo đơn hàng: {$orderCode} | Ngày: {$orderDateTime->format('Y-m-d H:i:s')} | Khách: {$customer->name} | Tổng: " . number_format($totalAmount) . "đ | Trạng thái: {$confirm}/{$payment}/{$delivery}\n";
            
        } catch (Exception $e) {
            DB::rollBack();
            echo "✗ Lỗi tạo đơn hàng: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n=== Hoàn thành ===\n";
echo "Tổng số đơn hàng đã tạo: {$totalOrders}\n";
echo "\nThống kê theo ngày:\n";
foreach ($ordersByDate as $date => $count) {
    echo "  {$date}: {$count} đơn\n";
}
echo "\n";
echo "⚠️  LƯU Ý: Trạng thái đơn hàng sử dụng 'cancle' (có chữ 'l') theo chuẩn của hệ thống.\n";
