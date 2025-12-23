@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['index']['title']])

<div class="row mt20">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{{ $config['seo']['index']['table'] }}</h5>
                <div class="ibox-tools">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-wrench"></i></a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="{{ route('warranty.statistics') }}">📊 Thống kê</a></li>
                        <li><a href="{{ route('warranty.export', request()->query()) }}">📥 Xuất Excel</a></li>
                    </ul>
                </div>
            </div>
            <div class="ibox-content">
                @include('backend.warranty.component.filter')
                @include('backend.warranty.component.table')
            </div>
        </div>
    </div>
</div>

