<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th style="width: 50px;">STT</th>
            <th>Mã BH</th>
            <th>Sản phẩm</th>
            <th>Khách hàng</th>
            <th>SĐT</th>
            <th>Ngày KH</th>
            <th>Hết hạn</th>
            <th class="text-center" style="width: 100px;">Trạng thái</th>
            <th class="text-center" style="width: 80px;">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($warranties) && is_object($warranties))
            @foreach($warranties as $key => $warranty)
            <tr>
                <td>{{ $warranties->firstItem() + $key }}</td>
                <td>
                    <div class="text-success"><strong>#{{ str_pad($warranty->id, 6, '0', STR_PAD_LEFT) }}</strong></div>
                    <div class="text-small text-muted">Đơn: #{{ $warranty->order->code ?? 'N/A' }}</div>
                </td>
                <td>
                    <div>{{ $warranty->product_name }}</div>
                    @if($warranty->product_code)
                        <div class="text-small text-muted">{{ $warranty->product_code }}</div>
                    @endif
                </td>
                <td>
                    <div>{{ $warranty->customer_name }}</div>
                    @if($warranty->customer_email)
                        <div class="text-small text-muted">{{ $warranty->customer_email }}</div>
                    @endif
                </td>
                <td>{{ $warranty->customer_phone }}</td>
                <td>{{ $warranty->activation_date ? $warranty->activation_date->format('d/m/Y') : '-' }}</td>
                <td>
                    <div>{{ $warranty->warranty_end_date ? $warranty->warranty_end_date->format('d/m/Y') : '-' }}</div>
                    @if($warranty->status == 'active')
                        <div class="text-small text-success">Còn {{ $warranty->remaining_days }} ngày</div>
                    @endif
                </td>
                <td class="text-center">
                    @if($warranty->status == 'active')
                        <span class="label label-success">Đang BH</span>
                    @elseif($warranty->status == 'expired')
                        <span class="label label-danger">Hết hạn</span>
                    @elseif($warranty->status == 'pending')
                        <span class="label label-warning">Chờ duyệt</span>
                    @else
                        <span class="label label-default">{{ $warranty->status_label }}</span>
                    @endif
                </td>
                <td class="text-center">
                    <a href="{{ route('warranty.detail', $warranty->id) }}" class="btn btn-info btn-sm" title="Xem chi tiết">
                        <i class="fa fa-eye"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>

<div class="uk-flex uk-flex-middle uk-flex-space-between">
    <div>
        @if(isset($warranties))
            Hiển thị {{ $warranties->firstItem() }} - {{ $warranties->lastItem() }} / {{ $warranties->total() }} bản ghi
        @endif
    </div>
    <div>
        {{ $warranties->links('pagination::bootstrap-4') }}
    </div>
</div>

