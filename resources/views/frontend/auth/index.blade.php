@extends('frontend.homepage.layout')
@section('content')
    <div class="login-container">
        <div class="uk-container uk-container-center">
            <div class="login-page">
                <div class="uk-grid uk-grid-collapse">
                    <div class="uk-width-large-1-2">
                        <div class="login-wrapper">
                            <div class="welcome-content">
                                <h1>Chào mừng trở lại!</h1>
                                <p>Đăng nhập để trải nghiệm đầy đủ các tính năng của {{ $system['homepage_brand'] }}</p>
                                <div class="features-list">
                                    <div class="feature-item">
                                        <div class="feature-icon">✓</div>
                                        <div class="feature-text">
                                            <div class="feature-title">1000+ Sản phẩm</div>
                                            <div class="feature-desc">Đa dạng sản phẩm chất lượng cao</div>
                                        </div>
                                    </div>
                                    <div class="feature-item">
                                        <div class="feature-icon">✓</div>
                                        <div class="feature-text">
                                            <div class="feature-title">500+ Khách hàng</div>
                                            <div class="feature-desc">Tin tưởng và hài lòng với dịch vụ</div>
                                        </div>
                                    </div>
                                    <div class="feature-item">
                                        <div class="feature-icon">✓</div>
                                        <div class="feature-text">
                                            <div class="feature-title">24/7 Hỗ trợ</div>
                                            <div class="feature-desc">Luôn sẵn sàng phục vụ khách hàng</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="login-form">
                            <form action="{{ route('fe.auth.dologin') }}" class="uk-form form">
                                @csrf
                                <div class="form-heading">Đăng nhập</div>
                                <div class="form-subheading">Nhập thông tin để tiếp tục</div>
                                <div class="form-row">
                                    <input 
                                        type="text"
                                        name="email"
                                        value=""
                                        placeholder="Email đăng nhập"
                                        class="input-text"
                                    >
                                </div>
                                <div class="form-row">
                                    <input 
                                        type="password"
                                        name="password"
                                        value=""
                                        placeholder="Mật khẩu"
                                        class="input-text"
                                    >
                                </div>
                                <button type="submit" value="login" name="login">Đăng nhập</button>
                                <div class="form-row forgot-password">
                                    <a href="{{ route('forgot.customer.password') }}">Quên mật khẩu?</a>
                                </div>
                                <div class="register-box">
                                    Bạn mới biết đến {{ $system['homepage_brand'] }}? <a href="{{ route('customer.register') }}">Đăng ký ngay</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection



