<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_warranties', function (Blueprint $table) {
            $table->id();
            
            // Order & Product Information
            $table->unsignedBigInteger('order_id')->nullable()->comment('ID đơn hàng nếu mua từ website');
            $table->string('order_product_uuid')->nullable()->comment('UUID sản phẩm trong đơn hàng');
            $table->unsignedBigInteger('product_id')->comment('ID sản phẩm');
            $table->string('product_name')->comment('Tên sản phẩm');
            $table->string('product_code')->nullable()->comment('Mã sản phẩm');
            $table->string('serial_number')->nullable()->comment('Số serial sản phẩm');
            
            // Customer Information
            $table->unsignedBigInteger('customer_id')->nullable()->comment('ID khách hàng nếu đã đăng ký');
            $table->string('customer_name')->comment('Tên khách hàng');
            $table->string('customer_phone', 20)->comment('Số điện thoại');
            $table->string('customer_email')->nullable()->comment('Email');
            $table->text('customer_address')->comment('Địa chỉ chi tiết');
            $table->string('province_id')->nullable()->comment('Tỉnh/Thành phố');
            $table->string('district_id')->nullable()->comment('Quận/Huyện');
            $table->string('ward_id')->nullable()->comment('Phường/Xã');
            
            // Warranty Information
            $table->date('purchase_date')->comment('Ngày mua hàng');
            $table->date('activation_date')->nullable()->comment('Ngày kích hoạt bảo hành');
            $table->integer('warranty_months')->default(12)->comment('Thời hạn bảo hành (tháng)');
            $table->date('warranty_end_date')->nullable()->comment('Ngày hết hạn bảo hành');
            $table->string('qr_code')->nullable()->unique()->comment('Mã QR');
            
            // Images
            $table->text('product_images')->nullable()->comment('Ảnh sản phẩm khi kích hoạt');
            $table->text('invoice_image')->nullable()->comment('Ảnh hóa đơn');
            
            // Status & Notes
            $table->enum('status', ['pending', 'active', 'expired', 'rejected'])->default('pending')->comment('Trạng thái: pending=chờ duyệt, active=đang bảo hành, expired=hết hạn, rejected=từ chối');
            $table->text('customer_note')->nullable()->comment('Ghi chú của khách hàng');
            $table->text('admin_note')->nullable()->comment('Ghi chú của admin');
            
            // Foreign Keys
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('customer_phone');
            $table->index('qr_code');
            $table->index(['order_id', 'order_product_uuid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_warranties');
    }
};
