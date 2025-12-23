@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['statistics']['title']])

<div class="row mt20">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{{ $config['seo']['statistics']['title'] }}</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="widget style1 navy-bg">
                            <div class="row">
                                <div class="col-xs-4">
                                    <i class="fa fa-shield fa-5x"></i>
                                </div>
                                <div class="col-xs-8 text-right">
                                    <span> Tổng bảo hành </span>
                                    <h2 class="font-bold">{{ $statistics['total'] ?? 0 }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="widget style1 lazur-bg">
                            <div class="row">
                                <div class="col-xs-4">
                                    <i class="fa fa-check-circle fa-5x"></i>
                                </div>
                                <div class="col-xs-8 text-right">
                                    <span> Đang bảo hành </span>
                                    <h2 class="font-bold">{{ $statistics['active'] ?? 0 }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="widget style1 yellow-bg">
                            <div class="row">
                                <div class="col-xs-4">
                                    <i class="fa fa-clock-o fa-5x"></i>
                                </div>
                                <div class="col-xs-8 text-right">
                                    <span> Sắp hết hạn (30 ngày) </span>
                                    <h2 class="font-bold">{{ $statistics['expiring_soon'] ?? 0 }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="widget style1 red-bg">
                            <div class="row">
                                <div class="col-xs-4">
                                    <i class="fa fa-times-circle fa-5x"></i>
                                </div>
                                <div class="col-xs-8 text-right">
                                    <span> Đã hết hạn </span>
                                    <h2 class="font-bold">{{ $statistics['expired'] ?? 0 }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="hr-line-dashed"></div>
                
                <div class="text-center">
                    <a href="{{ route('warranty.index') }}" class="btn btn-primary">
                        <i class="fa fa-list"></i> Xem danh sách bảo hành
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

