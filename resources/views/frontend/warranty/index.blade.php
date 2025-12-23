@extends('frontend.homepage.layout')
@section('content')
<div class="profile-container pt20 pb20">
    <div class="uk-container uk-container-center">
        <div class="uk-grid uk-grid-medium">
            <div class="uk-width-large-1-4">
                @include('frontend.auth.customer.components.sidebar')
            </div>
            <div class="uk-width-large-3-4">
                <div class="panel-profile">
                    <div class="panel-head">
                        <h2 class="heading-2"><span>🛡️ Thông tin bảo hành của tôi</span></h2>
                        <div class="description">
                            Quản lý bảo hành sản phẩm đã kích hoạt
                        </div>
                    </div>
                    <div class="panel-body">
                        
                        {{-- Filter --}}
                        <form action="{{ route('customer.warranty.list') }}" method="GET" class="mb20">
                            <div class="uk-grid uk-grid-small">
                                <div class="uk-width-medium-1-2">
                                    <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm kiếm sản phẩm..." class="input-text">
                                </div>
                                <div class="uk-width-medium-1-4">
                                    <select name="status" class="input-text">
                                        <option value="">Tất cả</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang BH</option>
                                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Hết hạn</option>
                                    </select>
                                </div>
                                <div class="uk-width-medium-1-4">
                                    <button type="submit" class="btn btn-primary">Lọc</button>
                                </div>
                            </div>
                        </form>

                        {{-- List --}}
                        @if($warranties->count() > 0)
                            @foreach($warranties as $warranty)
                            <div class="warranty-item mb20" style="border: 1px solid #e5e5e5; border-radius: 8px; padding: 20px; background: #f8f9fa;">
                                <div class="uk-grid uk-grid-small">
                                    <div class="uk-width-medium-3-4">
                                        <h3 style="margin: 0 0 10px 0; font-size: 18px;">
                                            @if($warranty->status == 'active')
                                                <span style="color: #28a745;">🟢</span>
                                            @else
                                                <span style="color: #dc3545;">🔴</span>
                                            @endif
                                            {{ $warranty->product_name }}
                                        </h3>
                                        <div style="color: #666; font-size: 14px;">
                                            <div><strong>Mã BH:</strong> #{{ str_pad($warranty->id, 6, '0', STR_PAD_LEFT) }}</div>
                                            <div><strong>Đơn hàng:</strong> #{{ $warranty->order->code ?? '-' }}</div>
                                            <div><strong>Kích hoạt:</strong> {{ $warranty->activation_date->format('d/m/Y') }}</div>
                                            <div><strong>Hết hạn:</strong> {{ $warranty->warranty_end_date->format('d/m/Y') }}</div>
                                            @if($warranty->status == 'active')
                                                <div style="color: #28a745;"><strong>Còn lại:</strong> {{ $warranty->remaining_days }} ngày</div>
                                            @else
                                                <div style="color: #dc3545;"><strong>Trạng thái:</strong> Đã hết hạn</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="uk-width-medium-1-4 uk-text-right">
                                        <a href="{{ route('customer.warranty.detail', $warranty->id) }}" class="btn btn-info btn-sm">
                                            Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                            {{-- Pagination --}}
                            <div class="mt20">
                                {{ $warranties->links() }}
                            </div>
                        @else
                            <div class="text-center" style="padding: 60px 20px; color: #999;">
                                <i class="fa fa-inbox" style="font-size: 64px; margin-bottom: 20px;"></i>
                                <h3>Chưa có bảo hành nào</h3>
                                <p>Bạn chưa kích hoạt bảo hành cho sản phẩm nào.</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

