@include('backend.dashboard.component.breadcrumb', ['title' => 'Chi tiết bảo hành #'.str_pad($warranty->id, 6, '0', STR_PAD_LEFT)])

<div class="row mt20">
    <div class="col-lg-8">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Thông tin bảo hành</h5>
            </div>
            <div class="ibox-content">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px;">Mã bảo hành</th>
                            <td><strong class="text-success">#{{ str_pad($warranty->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Trạng thái</th>
                            <td>
                                @if($warranty->status == 'active')
                                    <span class="label label-success">Đang bảo hành</span>
                                @elseif($warranty->status == 'expired')
                                    <span class="label label-danger">Hết hạn</span>
                                @elseif($warranty->status == 'pending')
                                    <span class="label label-warning">Chờ duyệt</span>
                                @else
                                    <span class="label label-default">{{ $warranty->status_label }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Ngày mua hàng</th>
                            <td>{{ $warranty->purchase_date ? $warranty->purchase_date->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Ngày kích hoạt</th>
                            <td>{{ $warranty->activation_date ? $warranty->activation_date->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Thời hạn bảo hành</th>
                            <td>{{ $warranty->warranty_months }} tháng</td>
                        </tr>
                        <tr>
                            <th>Ngày hết hạn</th>
                            <td>
                                {{ $warranty->warranty_end_date ? $warranty->warranty_end_date->format('d/m/Y') : '-' }}
                                @if($warranty->status == 'active')
                                    <span class="text-success">(Còn {{ $warranty->remaining_days }} ngày)</span>
                                @endif
                            </td>
                        </tr>
                        @if($warranty->customer_note)
                        <tr>
                            <th>Ghi chú khách hàng</th>
                            <td>{{ $warranty->customer_note }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="ibox mt20">
            <div class="ibox-title">
                <h5>Thông tin sản phẩm</h5>
            </div>
            <div class="ibox-content">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px;">Tên sản phẩm</th>
                        <td>{{ $warranty->product_name }}</td>
                    </tr>
                    @if($warranty->product_code)
                    <tr>
                        <th>Mã sản phẩm</th>
                        <td>{{ $warranty->product_code }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Đơn hàng</th>
                        <td>
                            <a href="{{ route('order.detail', $warranty->order_id) }}" target="_blank">
                                #{{ $warranty->order->code ?? $warranty->order_id }}
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Thông tin khách hàng</h5>
            </div>
            <div class="ibox-content">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 100px;">Họ tên</th>
                        <td>{{ $warranty->customer_name }}</td>
                    </tr>
                    <tr>
                        <th>Số ĐT</th>
                        <td>{{ $warranty->customer_phone }}</td>
                    </tr>
                    @if($warranty->customer_email)
                    <tr>
                        <th>Email</th>
                        <td>{{ $warranty->customer_email }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Địa chỉ</th>
                        <td>{{ $warranty->customer_address }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="ibox mt20">
            <div class="ibox-content text-center">
                <a href="{{ route('warranty.index') }}" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>

