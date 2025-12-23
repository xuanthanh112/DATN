<div class="mb10"><div class="text-warning" style="font-size:12px;"><i>*Danh sách đơn hàng đã bị xóa (soft delete). Bạn có thể khôi phục hoặc xóa vĩnh viễn.</i></div></div>
<table class="table table-striped table-bordered order-table">
    <thead>
    <tr>
        <th>
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th>Mã</th>
        <th>Ngày tạo</th>
        <th>Ngày xóa</th>
        <th>Khách hàng</th>
        <th class="text-right">Giảm giá (vnđ)</th>
        <th class="text-right">Tổng cuối (vnđ)</th>
        <th class="text-center">Trạng thái</th>
        <th>Thanh toán</th>
        <th>Giao hàng</th>
        <th>Hình thức</th>
        <th class="text-center">Thao tác</th>
    </tr>
    </thead>
    <tbody>
        @if(isset($orders) && is_object($orders))
            @foreach($orders as $order)
            <tr style="opacity: 0.7;">
                <td>
                    <input type="checkbox" value="{{ $order->id }}" class="input-checkbox checkBoxItem">
                </td>
                <td>
                    <a href="{{ route('order.detail', $order->id) }}">{{ $order->code }}</a>
                </td>
                <td>
                    {{ convertDateTime($order->created_at, 'd-m-Y') }}
                </td>
                <td>
                    {{ $order->deleted_at ? convertDateTime($order->deleted_at, 'd-m-Y H:i') : '-' }}
                </td>
                <td>
                    <div><b>N:</b> {{ $order->fullname }}</div>
                    <div><b>P:</b> {{ $order->phone }}</div>
                    <div><b>A:</b> {{ $order->address }}</div>
                </td>
                
                <td class="text-right order-discount">
                    {{ convert_price($order->promotion['discount'] ?? 0, true) }}
                </td>
                <td class="text-right order-total">
                    {{ convert_price($order->cart['cartTotal'] ?? 0, true) }}
                </td>
                <td class="text-center">
                    {!! ($order->confirm != 'cancle') ? __('cart.confirm')[$order->confirm] : '<span class="cancle-badge">'.__('cart.confirm')[$order->confirm].'</span>' !!}
                </td>
                @foreach(__('cart') as $keyItem => $item)
                @if($keyItem === 'confirm') @continue @endif
                <td class="text-center">
                    @if($order->confirm != 'cancle')
                        {{ $item[$order->{$keyItem}] ?? '-' }}
                    @else
                    -
                    @endif
                </td>
                @endforeach
                <td class="text-center">
                    @if($order->confirm != 'cancle')
                        <img title="{{ array_column(__('payment.method'), 'title', 'name')[$order->method] ?? '-' }}" style="max-width:54px;" src="{{ array_column(__('payment.method'), 'image', 'name')[$order->method] ?? '-' }}" alt="">
                    @else
                    -
                    @endif
                </td>
                <td class="text-center">
                    <form action="{{ route('order.restore', $order->id) }}" method="post" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm" title="Khôi phục đơn hàng" onclick="return confirm('Bạn có chắc chắn muốn khôi phục đơn hàng này?');">
                            <i class="fa fa-undo"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="12" class="text-center">Không có đơn hàng đã xóa nào.</td>
            </tr>
        @endif
    </tbody>
</table>
{{  $orders->links('pagination::bootstrap-4') ?? '' }}

