@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['index']['title']])
<div class="row mt20">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title uk-flex uk-flex-middle uk-flex-space-between">
                <h5>{{ $config['seo']['index']['table']; }} </h5>
                <div>
                    <a href="{{ route('order.index') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-arrow-left"></i> Quay lại danh sách đơn hàng
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                @include('backend.order.component.filter-trashed')
                @include('backend.order.component.table-trashed')
            </div>
        </div>
    </div>
</div>

