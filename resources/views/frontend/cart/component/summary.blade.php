<div class="panel-foot mt30 pay">
    <div class="cart-summary mb20">
        @if(isset($cartPromotion['selectedPromotion']) && $cartPromotion['selectedPromotion'] && $cartPromotion['discount'] > 0)
        <div class="promotion-info-box">
            <div class="promotion-icon">
                <i class="fa fa-gift"></i>
            </div>
            <div class="promotion-content">
                <div class="promotion-title">Đang áp dụng khuyến mại</div>
                <div class="promotion-name">{{ $cartPromotion['selectedPromotion']->name }}</div>
                <div class="promotion-discount">Giảm: <span class="discount-amount">-{{ convert_price($cartPromotion['discount'], true) }}đ</span></div>
            </div>
        </div>
        @endif
        <div class="cart-summary-item">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <span class="summay-title">Giảm giá</span>
                <div class="summary-value discount-value">-{{ convert_price($cartPromotion['discount'], true) }}đ</div>
            </div>
        </div>
        <div class="cart-summary-item">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <span class="summay-title">Phí giao hàng</span>
                <div class="summary-value">Miễn phí</div>
            </div>
        </div>
        <div class="cart-summary-item">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <span class="summay-title bold">Tổng tiền</span>
                <div class="summary-value cart-total">{{ (count($carts) && !is_null($carts) ) ? convert_price($cartCaculate['cartTotal'] - $cartPromotion['discount'], true) : 0   }}đ</div>
            </div>
        </div>
        <div class="buy-more">
            <a href="{{ write_url('san-pham') }}" class="btn-buymore">Chọn thêm sản phẩm khác</a>
        </div>
    </div>
</div>