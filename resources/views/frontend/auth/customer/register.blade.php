@extends('frontend.homepage.layout')
@section('content')
    <div class="register-container">
        <div class="uk-container uk-container-center">
            <div class="register-page">
                <div class="uk-grid uk-grid-collapse">
                    <div class="uk-width-large-1-2">
                        <div class="register-wrapper">
                            <div class="welcome-content">
                                <h1>Tham gia cùng chúng tôi!</h1>
                                <p>Tạo tài khoản để nhận ưu đãi độc quyền và trải nghiệm mua sắm tuyệt vời</p>
                                <div class="features-list">
                                    <div class="feature-item">
                                        <div class="feature-icon">✓</div>
                                        <div class="feature-text">
                                            <div class="feature-title">Ưu đãi độc quyền</div>
                                            <div class="feature-desc">Giảm giá đặc biệt cho thành viên mới</div>
                                        </div>
                                    </div>
                                    <div class="feature-item">
                                        <div class="feature-icon">✓</div>
                                        <div class="feature-text">
                                            <div class="feature-title">Bảo hành nhanh chóng</div>
                                            <div class="feature-desc">Kích hoạt bảo hành dễ dàng qua QR code</div>
                                        </div>
                                    </div>
                                    <div class="feature-item">
                                        <div class="feature-icon">✓</div>
                                        <div class="feature-text">
                                            <div class="feature-title">Quản lý đơn hàng</div>
                                            <div class="feature-desc">Theo dõi đơn hàng mọi lúc mọi nơi</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="register-form">
                            <form action="{{ route('customer.reg')}}" method="post">
                                @csrf
                                    <div class="form-heading">Đăng ký</div>
                                    <div class="form-subheading">Tạo tài khoản mới của bạn</div>
                                    <div class="form-row">
                                        <input 
                                            type="text" 
                                            class="input-text" 
                                            name="name"
                                            value="{{ old('name') }}"  
                                            placeholder="Họ tên"
                                        >
                                        @if($errors->has('name'))
                                            <span class="error-message">* {{ $errors->first('name') }}</span>
                                        @endif
                                    </div>
                                    <div class="form-row">
                                        <input 
                                            type="text" 
                                            class="input-text" 
                                            name="email"
                                            value="{{ old('email') }}" 
                                            placeholder="Email"
                                        >
                                        @if($errors->has('email'))
                                            <span class="error-message">* {{ $errors->first('email') }}</span>
                                        @endif
                                    </div>
                                    <div class="form-row">
                                        <input 
                                            type="password" 
                                            name="password"
                                            class="input-text" 
                                            placeholder="Mật khẩu"
                                            autocomplete="off"
                                        >
                                        @if($errors->has('password'))
                                            <span class="error-message">* {{ $errors->first('password') }}</span>
                                        @endif
                                    </div>
                                    <div class="form-row">
                                        <input 
                                            type="password" 
                                            class="input-text" 
                                            name="re_password"
                                            placeholder="Nhập lại mật khẩu"
                                            autocomplete="off"
                                        >
                                        @if($errors->has('re_password'))
                                            <span class="error-message">* {{ $errors->first('re_password') }}</span>
                                        @endif
                                    </div>
                                    <div class="form-row">
                                        <input 
                                            type="text" 
                                            class="input-text" 
                                            name="phone"
                                            value="{{ old('phone') }}" 
                                            placeholder="Số điện thoại"
                                        >
                                    </div>
                                    <div class="form-row">
                                        <input 
                                            type="text" 
                                            class="input-text" 
                                            name="address"
                                            value="{{ old('address') }}" 
                                            placeholder="Địa chỉ"
                                        >
                                        <input type="hidden" name="customer_catalogue_id" value="1">
                                    </div>
                                    <button type="submit" class="btn btn-primary block full-width m-b">Đăng ký</button>
                                    <div class="login-box">
                                        Đã có tài khoản? <a href="{{ route('fe.auth.login') }}">Đăng nhập ngay</a>
                                    </div>
                            </form>
                            <p class="m-t mt5">
                                <small>{{ $system['homepage_brand'] }} 2023</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
