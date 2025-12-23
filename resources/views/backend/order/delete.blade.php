@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title'] ?? 'Xóa đơn hàng'])

<form action="{{ route('order.destroy', $order->id) }}" method="post" class="box">
    @csrf
    @method('DELETE')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>Bạn đang muốn xóa đơn hàng có mã: <strong>{{ $order->code }}</strong></p>
                        <p><strong>Khách hàng:</strong> {{ $order->fullname }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $order->phone }}</p>
                        <p><strong>Email:</strong> {{ $order->email }}</p>
                        <p><strong>Tổng tiền:</strong> {{ convert_price($order->cart['cartTotal'], true) }}đ</p>
                        <p><strong>Ngày tạo:</strong> {{ convertDateTime($order->created_at, 'd/m/Y H:i') }}</p>
                        <p class="text-danger mt20"><strong>Lưu ý:</strong> Không thể khôi phục đơn hàng sau khi xóa. Hãy chắc chắn bạn muốn thực hiện chức năng này.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Mã đơn hàng <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="code"
                                        value="{{ old('code', $order->code ?? '' ) }}"
                                        class="form-control"
                                        placeholder=""
                                        autocomplete="off"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tên khách hàng</label>
                                    <input 
                                        type="text"
                                        name="fullname"
                                        value="{{ old('fullname', $order->fullname ?? '' ) }}"
                                        class="form-control"
                                        placeholder=""
                                        autocomplete="off"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Số điện thoại</label>
                                    <input 
                                        type="text"
                                        name="phone"
                                        value="{{ old('phone', $order->phone ?? '' ) }}"
                                        class="form-control"
                                        placeholder=""
                                        autocomplete="off"
                                        readonly
                                    >
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Email</label>
                                    <input 
                                        type="text"
                                        name="email"
                                        value="{{ old('email', $order->email ?? '' ) }}"
                                        class="form-control"
                                        placeholder=""
                                        autocomplete="off"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
        <div class="text-right mb15">
            <a href="{{ route('order.index') }}" class="btn btn-default">Hủy bỏ</a>
            <button class="btn btn-danger" type="submit" name="send" value="send">Xóa đơn hàng</button>
        </div>
    </div>
</form>

