@extends('frontend.homepage.layout')
@section('content')
    <div class="profile-container pt20 pb20">
        <div class="uk-container uk-container-center">
            <div class="uk-grid uk-grid-medium">
                <div class="uk-width-large-1-4">
                    @include('frontend.auth.customer.components.sidebar')
                </div>
                <div class="uk-width-large-3-4">
<div class="customer-content">
    <div class="content-header">
        <h2><i class="fa fa-file-text"></i> Chi tiết đơn hàng #{{ $order->code }}</h2>
        <p>Thông tin chi tiết về đơn hàng của bạn</p>
    </div>
    
    <!-- Order Info -->
    <div class="order-detail-card" style="background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; border: 1px solid #e5e5e5;">
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-medium-1-2">
                <div class="info-row" style="margin-bottom: 12px;">
                    <span style="color: #666; display: inline-block; width: 140px;"><i class="fa fa-tag"></i> Mã đơn hàng:</span>
                    <strong>{{ $order->code }}</strong>
                </div>
                <div class="info-row" style="margin-bottom: 12px;">
                    <span style="color: #666; display: inline-block; width: 140px;"><i class="fa fa-calendar"></i> Ngày đặt hàng:</span>
                    <strong>{{ date('d/m/Y H:i', strtotime($order->created_at)) }}</strong>
                </div>
                <div class="info-row" style="margin-bottom: 12px;">
                    <span style="color: #666; display: inline-block; width: 140px;"><i class="fa fa-info-circle"></i> Trạng thái:</span>
                    @php
                        // Determine status based on delivery first, then confirm
                        $displayStatus = $order->confirm;
                        if($order->delivery == 'success') {
                            $displayStatus = 'success';
                        } elseif($order->delivery == 'pending' && $order->confirm == 'confirm') {
                            $displayStatus = 'shipping';
                        }
                        
                        $statusLabels = [
                            'pending' => 'Chờ xác nhận',
                            'confirm' => 'Đã xác nhận',
                            'shipping' => 'Đang giao',
                            'success' => 'Đã giao',
                            'cancel' => 'Đã hủy'
                        ];
                        $statusColors = [
                            'pending' => '#ffc107',
                            'confirm' => '#2196F3',
                            'shipping' => '#9c27b0',
                            'success' => '#4caf50',
                            'cancel' => '#f44336'
                        ];
                    @endphp
                    <span style="background: {{ $statusColors[$displayStatus] ?? '#999' }}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                        {{ $statusLabels[$displayStatus] ?? 'Không xác định' }}
                    </span>
                </div>
            </div>
            <div class="uk-width-medium-1-2">
                <div class="info-row" style="margin-bottom: 12px;">
                    <span style="color: #666; display: inline-block; width: 140px;"><i class="fa fa-user"></i> Người nhận:</span>
                    <strong>{{ $order->fullname }}</strong>
                </div>
                <div class="info-row" style="margin-bottom: 12px;">
                    <span style="color: #666; display: inline-block; width: 140px;"><i class="fa fa-phone"></i> Số điện thoại:</span>
                    <strong>{{ $order->phone }}</strong>
                </div>
                <div class="info-row" style="margin-bottom: 12px;">
                    <span style="color: #666; display: inline-block; width: 140px;"><i class="fa fa-map-marker"></i> Địa chỉ:</span>
                    <strong>{{ $order->address }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Products List -->
    <div class="products-section" style="background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; border: 1px solid #e5e5e5;">
        <h3 style="margin-bottom: 20px; color: #da2229; font-size: 18px; font-weight: 600;">
            <i class="fa fa-cube"></i> Danh sách sản phẩm
        </h3>
        
        <div class="products-table">
            <table class="uk-table uk-table-striped" style="margin: 0;">
                <thead>
                    <tr style="background: linear-gradient(135deg, #da2229 0%, #ff6b6b 100%); color: white;">
                        <th style="padding: 12px;">Sản phẩm</th>
                        <th style="padding: 12px; width: 100px; text-align: center;">Số lượng</th>
                        <th style="padding: 12px; width: 150px; text-align: right;">Đơn giá</th>
                        <th style="padding: 12px; width: 150px; text-align: right;">Thành tiền</th>
                        <th style="padding: 12px; width: 150px; text-align: center;">Bảo hành</th>
                    </tr>
                </thead>
                <tbody>
                    @if($order->products && $order->products->count() > 0)
                        @foreach($order->products as $product)
                        <tr>
                            <td style="padding: 15px;">
                                <div class="uk-flex uk-flex-middle">
                                    @php
                                        $productImage = $product->image ?? ($product->album ? json_decode($product->album, true)[0] ?? '' : '');
                                    @endphp
                                    <img src="{{ $productImage ? asset($productImage) : asset('frontend/resources/img/no-image.png') }}" 
                                         alt="{{ $product->pivot->name }}" 
                                         style="width: 70px; height: 70px; object-fit: cover; border-radius: 4px; border: 1px solid #e5e5e5; margin-right: 15px;">
                                    <div>
                                        <div style="font-weight: 500; color: #333; margin-bottom: 5px;">{{ $product->pivot->name }}</div>
                                        @if($product->pivot->option)
                                        <div style="font-size: 13px; color: #999;">{{ $product->pivot->option }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <span style="font-weight: 500;">{{ $product->pivot->qty }}</span>
                            </td>
                            <td style="padding: 15px; text-align: right; color: #666;">
                                {{ convert_price($product->pivot->price, true) }}
                            </td>
                            <td style="padding: 15px; text-align: right; color: #da2229; font-weight: 600; font-size: 16px;">
                                {{ convert_price($product->pivot->price * $product->pivot->qty, true) }}
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                @php
                                    // Check if warranty exists for this product
                                    $hasWarranty = \App\Models\ProductWarranty::where('order_id', $order->id)
                                        ->where('order_product_uuid', $product->pivot->uuid)
                                        ->exists();
                                @endphp
                                
                                @if($hasWarranty)
                                    <span style="background: #4caf50; color: white; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                        <i class="fa fa-check-circle"></i> Đã kích hoạt
                                    </span>
                                @else
                                    @if($order->delivery == 'success')
                                        <button class="uk-button activate-warranty-btn" 
                                                data-order-id="{{ $order->id }}"
                                                data-product-uuid="{{ $product->pivot->uuid }}"
                                                data-product-name="{{ $product->pivot->name }}"
                                                style="background: #da2229; color: white; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; border: none; cursor: pointer;">
                                            <i class="fa fa-shield"></i> Kích hoạt
                                        </button>
                                    @else
                                        <span style="color: #999; font-size: 12px;">
                                            <i class="fa fa-info-circle"></i> Chờ giao hàng
                                        </span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payment Summary -->
    <div class="payment-summary" style="background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; border: 1px solid #e5e5e5;">
        <h3 style="margin-bottom: 20px; color: #da2229; font-size: 18px; font-weight: 600;">
            <i class="fa fa-calculator"></i> Thanh toán
        </h3>
        
        <div class="summary-row" style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
            <span style="color: #666;">Tạm tính:</span>
            <span style="font-weight: 500;">{{ convert_price($order->cart['cartTotal'] ?? 0, true) }}</span>
        </div>
        <div class="summary-row" style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
            <span style="color: #666;">Phí vận chuyển:</span>
            <span style="font-weight: 500; color: #4caf50;">Miễn phí</span>
        </div>
        <div class="summary-row" style="display: flex; justify-content: space-between; padding: 15px 0; background: linear-gradient(135deg, #fff5f5 0%, #ffe5e5 100%); margin: 10px -20px -20px -20px; padding: 15px 20px; border-radius: 0 0 8px 8px;">
            <span style="font-size: 18px; font-weight: 600; color: #333;">Tổng thanh toán:</span>
            <span style="font-size: 24px; font-weight: 700; color: #da2229;">{{ convert_price($order->cart['cartTotal'] ?? 0, true) }}</span>
        </div>
    </div>

    <!-- Actions -->
    <div class="order-actions" style="text-align: center; margin-top: 30px;">
        <a href="{{ route('customer.orders') }}" class="uk-button" style="background: #666; color: white; padding: 10px 30px; border-radius: 4px; margin-right: 10px;">
            <i class="fa fa-arrow-left"></i> Quay lại danh sách
        </a>
        <a href="{{ route('customer.warranty.list') }}" class="uk-button" style="background: #da2229; color: white; padding: 10px 30px; border-radius: 4px;">
            <i class="fa fa-shield"></i> Xem bảo hành của tôi
        </a>
    </div>
</div>

<!-- Warranty Activation Modal -->
<div id="warrantyActivationModal" class="uk-modal">
    <div class="uk-modal-dialog" style="border-radius: 8px; overflow: hidden;">
        <a class="uk-modal-close uk-close"></a>
        <div class="uk-modal-header" style="background: linear-gradient(135deg, #da2229 0%, #ff6b6b 100%); color: white; padding: 20px;">
            <h3 style="margin: 0; font-size: 20px; font-weight: 600;">
                <i class="fa fa-shield"></i> Kích hoạt bảo hành
            </h3>
        </div>
        <div class="uk-modal-body" style="padding: 30px;">
            <div id="warrantyActivationContent">
                <div style="text-align: center; padding: 40px 20px;">
                    <i class="fa fa-spinner fa-spin" style="font-size: 48px; color: #da2229;"></i>
                    <p style="margin-top: 20px; color: #666;">Đang tải thông tin...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.activate-warranty-btn:hover {
    background: #c01e24 !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(218, 34, 41, 0.3);
}

.uk-modal-dialog {
    max-width: 600px;
}

.info-item {
    background: #f8f9fa;
    padding: 12px 15px;
    border-radius: 4px;
    margin-bottom: 10px;
}

.confirm-warranty-btn {
    background: #da2229;
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
    transition: all 0.3s ease;
}

.confirm-warranty-btn:hover {
    background: #c01e24;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(218, 34, 41, 0.3);
}

.success-animation {
    text-align: center;
    padding: 30px;
}

.success-checkmark {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
}

.checkmark-circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 2;
    stroke-miterlimit: 10;
    stroke: #4caf50;
    fill: none;
    animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
}

.checkmark-check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
}

@keyframes stroke {
    100% {
        stroke-dashoffset: 0;
    }
}
</style>

<script>
$(document).ready(function() {
    // Activate warranty button click
    $('.activate-warranty-btn').click(function() {
        var orderId = $(this).data('order-id');
        var productUuid = $(this).data('product-uuid');
        var productName = $(this).data('product-name');
        
        // Show modal with product info
        var modalContent = `
            <div style="text-align: center; margin-bottom: 25px;">
                <div style="background: linear-gradient(135deg, #fff5f5 0%, #ffe5e5 100%); padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <i class="fa fa-cube" style="font-size: 48px; color: #da2229; margin-bottom: 10px;"></i>
                    <h4 style="margin: 10px 0; color: #333;">${productName}</h4>
                </div>
            </div>
            
            <div style="margin-bottom: 25px;">
                <div class="info-item">
                    <i class="fa fa-calendar" style="color: #da2229; margin-right: 10px;"></i>
                    <strong>Ngày mua:</strong> {{ date('d/m/Y', strtotime($order->created_at)) }}
                </div>
                <div class="info-item">
                    <i class="fa fa-shield" style="color: #da2229; margin-right: 10px;"></i>
                    <strong>Thời hạn bảo hành:</strong> 12 tháng
                </div>
                <div class="info-item">
                    <i class="fa fa-calendar-check-o" style="color: #da2229; margin-right: 10px;"></i>
                    <strong>Bảo hành đến:</strong> {{ date('d/m/Y', strtotime('+12 months', strtotime($order->created_at))) }}
                </div>
            </div>
            
            <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; padding: 15px; margin-bottom: 20px;">
                <i class="fa fa-info-circle" style="color: #856404;"></i>
                <span style="color: #856404; font-size: 14px;">
                    Thông tin bảo hành sẽ tự động lấy từ đơn hàng của bạn
                </span>
            </div>
            
            <button class="confirm-warranty-btn" data-order-id="${orderId}" data-product-uuid="${productUuid}">
                <i class="fa fa-check-circle"></i> Xác nhận kích hoạt bảo hành
            </button>
        `;
        
        $('#warrantyActivationContent').html(modalContent);
        UIkit.modal('#warrantyActivationModal').show();
    });
    
    // Confirm warranty activation
    $(document).on('click', '.confirm-warranty-btn', function() {
        var btn = $(this);
        var orderId = btn.data('order-id');
        var productUuid = btn.data('product-uuid');
        
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...');
        
        $.ajax({
            url: '{{ route("customer.warranty.activate") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                order_id: orderId,
                order_product_uuid: productUuid
            },
            success: function(response) {
                if(response.code == 200) {
                    // Show success animation
                    var successContent = `
                        <div class="success-animation">
                            <svg class="success-checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                                <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                                <path class="checkmark-check" fill="none" stroke="#4caf50" stroke-width="3" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                            </svg>
                            <h3 style="color: #4caf50; margin-bottom: 10px;">Kích hoạt thành công!</h3>
                            <p style="color: #666; margin-bottom: 20px;">Bảo hành của bạn đã được kích hoạt.</p>
                            <button onclick="location.reload()" class="uk-button" style="background: #da2229; color: white; padding: 10px 30px; border-radius: 4px;">
                                <i class="fa fa-refresh"></i> Tải lại trang
                            </button>
                        </div>
                    `;
                    $('#warrantyActivationContent').html(successContent);
                } else {
                    alert(response.message || 'Có lỗi xảy ra!');
                    btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Xác nhận kích hoạt bảo hành');
                }
            },
            error: function() {
                alert('Có lỗi xảy ra khi kích hoạt bảo hành!');
                btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Xác nhận kích hoạt bảo hành');
            }
        });
    });
});
</script>
</div>
                </div>
            </div>
        </div>
    </div>
@endsection

