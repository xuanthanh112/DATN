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
                        <form action="{{ route('customer.warranty.list') }}" method="GET" class="warranty-filter-form mb20">
                            <div class="filter-container" style="background: linear-gradient(135deg, #fff5f5 0%, #ffe5e5 100%); border: 1px solid #ffcccc; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(218, 34, 41, 0.1);">
                                <div class="uk-grid uk-grid-small uk-flex-middle">
                                    <div class="uk-width-medium-1-2">
                                        <div class="search-input-wrapper" style="position: relative;">
                                            <i class="fa fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999; z-index: 1;"></i>
                                            <input 
                                                type="text" 
                                                name="keyword" 
                                                value="{{ request('keyword') }}" 
                                                placeholder="Tìm kiếm sản phẩm..." 
                                                class="warranty-search-input"
                                                style="width: 100%; padding: 12px 15px 12px 45px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; transition: all 0.3s ease; outline: none; background: #fff;"
                                                onfocus="this.style.borderColor='#da2229'; this.style.boxShadow='0 0 0 3px rgba(218, 34, 41, 0.1)';"
                                                onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';"
                                            >
                                        </div>
                                    </div>
                                    <div class="uk-width-medium-1-4">
                                        <div class="select-wrapper" style="position: relative;">
                                            <i class="fa fa-filter" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999; z-index: 1; pointer-events: none;"></i>
                                            <select 
                                                name="status" 
                                                class="warranty-status-select"
                                                style="width: 100%; padding: 12px 15px 12px 45px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; transition: all 0.3s ease; outline: none; background: #fff; appearance: none; cursor: pointer;"
                                                onfocus="this.style.borderColor='#da2229'; this.style.boxShadow='0 0 0 3px rgba(218, 34, 41, 0.1)';"
                                                onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';"
                                            >
                                                <option value="">Tất cả trạng thái</option>
                                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>🟢 Đang bảo hành</option>
                                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>🔴 Hết hạn</option>
                                            </select>
                                            <i class="fa fa-chevron-down" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #999; pointer-events: none; z-index: 1;"></i>
                                        </div>
                                    </div>
                                    <div class="uk-width-medium-1-4">
                                        <button 
                                            type="submit" 
                                            class="warranty-filter-btn"
                                            style="width: 100%; padding: 12px 20px; background: linear-gradient(135deg, #da2229 0%, #ff6b6b 100%); color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(218, 34, 41, 0.3);"
                                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(218, 34, 41, 0.4)';"
                                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(218, 34, 41, 0.3)';"
                                        >
                                            <i class="fa fa-search" style="margin-right: 8px;"></i>Lọc
                                        </button>
                                    </div>
                                </div>
                                @if(request('keyword') || request('status'))
                                <div class="filter-reset mt15" style="margin-top: 15px;">
                                    <a href="{{ route('customer.warranty.list') }}" style="color: #da2229; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center;">
                                        <i class="fa fa-times-circle" style="margin-right: 5px;"></i> Xóa bộ lọc
                                    </a>
                                </div>
                                @endif
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

