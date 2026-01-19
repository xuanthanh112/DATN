<div class="ibox">
    <div class="ibox-title">
        <h5>Top 10 Sản Phẩm Bán Chạy</h5>
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="fa fa-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
        @if(isset($topProducts) && count($topProducts) > 0)
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th class="text-center" width="5%">#</th>
                    <th width="8%">Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th class="text-center" width="15%">Số lượng đã bán</th>
                    <th class="text-right" width="20%">Tổng doanh thu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topProducts as $index => $product)
                <tr>
                    <td class="text-center">
                        <strong>{{ $index + 1 }}</strong>
                    </td>
                    <td>
                        @if(isset($product->image))
                        <img src="{{ $product->image }}" alt="{{ $product->name ?? '' }}" class="img-responsive" style="max-width: 60px; max-height: 60px; object-fit: cover;">
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('product.edit', $product->id) }}" target="_blank">
                            {{ $product->name ?? 'N/A' }}
                        </a>
                    </td>
                    <td class="text-center">
                        <span class="label label-primary">{{ number_format($product->total_quantity) }}</span>
                    </td>
                    <td class="text-right">
                        <strong class="text-navy">{{ convert_price($product->total_revenue, true) }}đ</strong>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-muted">
                    <td colspan="3" class="text-right"><strong>Tổng cộng:</strong></td>
                    <td class="text-center">
                        <strong class="text-info">{{ number_format($topProducts->sum('total_quantity')) }}</strong>
                    </td>
                    <td class="text-right">
                        <strong class="text-success">{{ convert_price($topProducts->sum('total_revenue'), true) }}đ</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
        @else
        <div class="text-center text-muted p-lg">
            <i class="fa fa-inbox fa-3x"></i>
            <p class="mt-2">Chưa có dữ liệu bán hàng</p>
        </div>
        @endif
    </div>
</div>

