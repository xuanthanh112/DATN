<div class="customer-sidebar">
    <div class="sidebar-header">
        <div class="customer-avatar">
            <i class="fa fa-user-circle"></i>
        </div>
        <div class="customer-name">{{ $customerAuth->name ?? 'Khách hàng' }}</div>
        <div class="customer-email">{{ $customerAuth->email ?? '' }}</div>
    </div>
    
    <div class="sidebar-menu">
        <ul class="uk-list">
            <li class="{{ request()->is('customer/profile*') ? 'active' : '' }}">
                <a href="{{ route('customer.profile') }}">
                    <i class="fa fa-user"></i>
                    <span>Hồ sơ của tôi</span>
                </a>
            </li>
            <li class="{{ request()->is('customer/password*') ? 'active' : '' }}">
                <a href="{{ route('customer.password.change') }}">
                    <i class="fa fa-lock"></i>
                    <span>Đổi mật khẩu</span>
                </a>
            </li>
            <li class="{{ request()->is('customer/order*') ? 'active' : '' }}">
                <a href="{{ route('customer.orders') }}">
                    <i class="fa fa-shopping-bag"></i>
                    <span>Đơn hàng của tôi</span>
                </a>
            </li>
            <li class="{{ request()->is('customer/warranty*') ? 'active' : '' }}">
                <a href="{{ route('customer.warranty.list') }}">
                    <i class="fa fa-shield"></i>
                    <span>Thông tin bảo hành</span>
                </a>
            </li>
            <li>
                <a href="{{ route('customer.logout') }}">
                    <i class="fa fa-sign-out"></i>
                    <span>Đăng xuất</span>
                </a>
            </li>
        </ul>
    </div>
</div>
