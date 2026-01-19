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
                        <h2 class="heading-2"><span>Chi tiết bảo hành #{{ str_pad($warranty->id, 6, '0', STR_PAD_LEFT) }}</span></h2>
                        <div class="description">
                            Thông tin chi tiết về bảo hành sản phẩm
                        </div>
                    </div>
                    <div class="panel-body">
                        
                        {{-- Status Badge --}}
                        <div class="mb20 text-center" style="padding: 20px; background: {{ $warranty->status == 'active' ? '#d4edda' : '#f8d7da' }}; border-radius: 8px;">
                            @if($warranty->status == 'active')
                                <h2 style="color: #155724; margin: 0;">🟢 Đang bảo hành</h2>
                                <p style="color: #155724; margin: 10px 0 0 0;">Còn {{ $warranty->remaining_days }} ngày</p>
                            @else
                                <h2 style="color: #721c24; margin: 0;">🔴 Đã hết hạn</h2>
                            @endif
                        </div>

                        {{-- Product Info --}}
                        <div class="info-section mb20">
                            <h3 style="border-bottom: 2px solid #da2229; padding-bottom: 10px; margin-bottom: 15px;">📦 Thông tin sản phẩm</h3>
                            <table style="width: 100%;">
                                <tr>
                                    <td style="padding: 8px 0; width: 180px;"><strong>Tên sản phẩm:</strong></td>
                                    <td style="padding: 8px 0;">{{ $warranty->product_name }}</td>
                                </tr>
                                @if($warranty->product_code)
                                <tr>
                                    <td style="padding: 8px 0;"><strong>Mã sản phẩm:</strong></td>
                                    <td style="padding: 8px 0;">{{ $warranty->product_code }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 8px 0;"><strong>Đơn hàng:</strong></td>
                                    <td style="padding: 8px 0;">#{{ $warranty->order->code ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0;"><strong>Ngày mua:</strong></td>
                                    <td style="padding: 8px 0;">{{ $warranty->purchase_date->format('d/m/Y') }}</td>
                                </tr>
                            </table>
                        </div>

                        {{-- Warranty Info --}}
                        <div class="info-section mb20">
                            <h3 style="border-bottom: 2px solid #da2229; padding-bottom: 10px; margin-bottom: 15px;">📅 Thông tin bảo hành</h3>
                            <table style="width: 100%;">
                                <tr>
                                    <td style="padding: 8px 0; width: 180px;"><strong>Mã bảo hành:</strong></td>
                                    <td style="padding: 8px 0;">#{{ str_pad($warranty->id, 6, '0', STR_PAD_LEFT) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0;"><strong>Ngày kích hoạt:</strong></td>
                                    <td style="padding: 8px 0;">{{ $warranty->activation_date->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0;"><strong>Thời hạn:</strong></td>
                                    <td style="padding: 8px 0;">{{ $warranty->warranty_months }} tháng</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0;"><strong>Ngày hết hạn:</strong></td>
                                    <td style="padding: 8px 0;">{{ $warranty->warranty_end_date->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0;"><strong>Trạng thái:</strong></td>
                                    <td style="padding: 8px 0;">
                                        @if($warranty->status == 'active')
                                            <span style="color: #28a745; font-weight: bold;">🟢 Đang hiệu lực</span>
                                        @else
                                            <span style="color: #dc3545; font-weight: bold;">🔴 Hết hạn</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        {{-- Customer Info --}}
                        <div class="info-section mb20">
                            <h3 style="border-bottom: 2px solid #da2229; padding-bottom: 10px; margin-bottom: 15px;">👤 Thông tin khách hàng</h3>
                            <table style="width: 100%;">
                                <tr>
                                    <td style="padding: 8px 0; width: 180px;"><strong>Họ và tên:</strong></td>
                                    <td style="padding: 8px 0;">{{ $warranty->customer_name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0;"><strong>Số điện thoại:</strong></td>
                                    <td style="padding: 8px 0;">{{ $warranty->customer_phone }}</td>
                                </tr>
                                @if($warranty->customer_email)
                                <tr>
                                    <td style="padding: 8px 0;"><strong>Email:</strong></td>
                                    <td style="padding: 8px 0;">{{ $warranty->customer_email }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 8px 0;"><strong>Địa chỉ:</strong></td>
                                    <td style="padding: 8px 0;">{{ $warranty->customer_address }}</td>
                                </tr>
                            </table>
                        </div>

                        {{-- Note --}}
                        @if($warranty->customer_note)
                        <div class="info-section mb20">
                            <h3 style="border-bottom: 2px solid #da2229; padding-bottom: 10px; margin-bottom: 15px;">📝 Ghi chú</h3>
                            <p>{{ $warranty->customer_note }}</p>
                        </div>
                        @endif

                        {{-- Actions --}}
                        <div class="text-center mt30">
                            <a href="{{ route('customer.warranty.list') }}" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Quay lại danh sách
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


