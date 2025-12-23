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
        <h2><i class="fa fa-shopping-bag"></i> Đơn hàng của tôi</h2>
        <p>Quản lý và theo dõi đơn hàng của bạn</p>
    </div>
    
    <div class="order-search" style="margin-bottom: 20px;">
        <form action="{{ route('customer.orders') }}" method="GET" class="uk-form">
            <div class="uk-grid uk-grid-small">
                <div class="uk-width-medium-3-4">
                    <input type="text" name="keyword" value="{{ request('keyword') }}" 
                           placeholder="Tìm kiếm theo mã đơn hàng, tên sản phẩm..." 
                           class="uk-width-1-1" 
                           style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div class="uk-width-medium-1-4">
                    <button type="submit" class="uk-button uk-width-1-1" 
                            style="background: #da2229; color: white; padding: 10px;">
                        <i class="fa fa-search"></i> Tìm kiếm
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if($orders->count() > 0)
    <div class="orders-list">
        @foreach($orders as $order)
        <div class="order-item" style="background: white; border: 1px solid #e5e5e5; border-radius: 8px; margin-bottom: 20px; overflow: hidden;">
            <!-- Order Header -->
            <div class="order-header" style="background: linear-gradient(135deg, #da2229 0%, #ff6b6b 100%); color: white; padding: 15px 20px;">
                <div class="uk-grid uk-grid-small uk-flex uk-flex-middle">
                    <div class="uk-width-medium-1-2">
                        <div style="font-size: 16px; font-weight: 600;">
                            <i class="fa fa-tag"></i> Mã đơn hàng: <strong>{{ $order->code }}</strong>
                        </div>
                    </div>
                    <div class="uk-width-medium-1-2 uk-text-right">
                        <div style="font-size: 14px;">
                            <i class="fa fa-calendar"></i> {{ date('d/m/Y H:i', strtotime($order->created_at)) }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Content -->
            <div class="order-content" style="padding: 20px;">
                <!-- Delivery Info -->
                <div class="delivery-info" style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0;">
                    <div class="uk-grid uk-grid-small">
                        <div class="uk-width-medium-1-2">
                            <div style="margin-bottom: 8px;">
                                <i class="fa fa-user" style="color: #da2229; width: 20px;"></i>
                                <strong>{{ $order->fullname }}</strong>
                            </div>
                            <div style="margin-bottom: 8px;">
                                <i class="fa fa-phone" style="color: #da2229; width: 20px;"></i>
                                {{ $order->phone }}
                            </div>
                        </div>
                        <div class="uk-width-medium-1-2">
                            <div style="margin-bottom: 8px;">
                                <i class="fa fa-map-marker" style="color: #da2229; width: 20px;"></i>
                                {{ $order->address }}
                            </div>
                            <div style="margin-bottom: 8px;">
                                <i class="fa fa-info-circle" style="color: #da2229; width: 20px;"></i>
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
                    </div>
                </div>

                <!-- Products -->
                @if($order->products && $order->products->count() > 0)
                <div class="order-products">
                    @foreach($order->products as $index => $product)
                    @if($index < 2)
                    <div class="uk-grid uk-grid-small uk-flex uk-flex-middle" style="margin-bottom: {{ $index < min(1, $order->products->count() - 1) ? '10px' : '0' }};">
                        <div class="uk-width-medium-1-10">
                            @php
                                $productImage = $product->image ?? ($product->album ? json_decode($product->album, true)[0] ?? '' : '');
                            @endphp
                            <img src="{{ $productImage ? asset($productImage) : asset('frontend/resources/img/no-image.png') }}" 
                                 alt="{{ $product->pivot->name }}" 
                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #e5e5e5;">
                        </div>
                        <div class="uk-width-medium-6-10">
                            <div style="font-weight: 500; color: #333;">{{ $product->pivot->name }}</div>
                            @if($product->pivot->option)
                            <div style="font-size: 13px; color: #999;">{{ $product->pivot->option }}</div>
                            @endif
                        </div>
                        <div class="uk-width-medium-1-10 uk-text-center">
                            <span style="color: #666;">x{{ $product->pivot->qty }}</span>
                        </div>
                        <div class="uk-width-medium-2-10 uk-text-right">
                            <span style="color: #da2229; font-weight: 600;">{{ convert_price($product->pivot->price, true) }}</span>
                        </div>
                    </div>
                    @endif
                    @endforeach
                    @if($order->products->count() > 2)
                    <div style="text-align: center; padding: 10px; color: #666; font-size: 13px;">
                        ... và {{ $order->products->count() - 2 }} sản phẩm khác
                    </div>
                    @endif
                </div>
                @endif
            </div>
            
            <!-- Order Footer -->
            <div class="order-footer" style="background: #f8f9fa; padding: 15px 20px; border-top: 1px solid #e5e5e5;">
                <div class="uk-grid uk-grid-small uk-flex uk-flex-middle">
                    <div class="uk-width-medium-1-2">
                        <div style="font-size: 16px;">
                            Tổng thanh toán: 
                            <strong style="color: #da2229; font-size: 20px;">
                                {{ convert_price($order->cart['cartTotal'] ?? 0, true) }}
                            </strong>
                        </div>
                    </div>
                    <div class="uk-width-medium-1-2 uk-text-right">
                        <a href="{{ route('customer.order.detail', ['id' => $order->id]) }}" 
                           class="uk-button" 
                           style="background: #da2229; color: white; padding: 8px 20px; border-radius: 4px; font-weight: 500;">
                            <i class="fa fa-eye"></i> Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($orders->hasPages())
    <div class="pagination-wrapper" style="margin-top: 30px;">
        {{ $orders->links() }}
    </div>
    @endif
    @else
    <div class="empty-state" style="text-align: center; padding: 60px 20px; background: white; border-radius: 8px; border: 1px solid #e5e5e5;">
        <i class="fa fa-shopping-bag" style="font-size: 80px; color: #ddd; margin-bottom: 20px;"></i>
        <h3 style="color: #666; font-size: 18px; margin-bottom: 10px;">Chưa có đơn hàng nào</h3>
        <p style="color: #999; margin-bottom: 20px;">Bạn chưa có đơn hàng nào trong hệ thống</p>
        <a href="{{ route('home.index') }}" class="uk-button" style="background: #da2229; color: white; padding: 10px 30px; border-radius: 4px;">
            <i class="fa fa-shopping-cart"></i> Mua sắm ngay
        </a>
    </div>
    @endif
</div>

<style>
.order-item {
    transition: all 0.3s ease;
}

.order-item:hover {
    box-shadow: 0 4px 12px rgba(218, 34, 41, 0.15);
    transform: translateY(-2px);
}

.uk-button:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}
</style>
</div>
                </div>
            </div>
        </div>
    </div>
@endsection

