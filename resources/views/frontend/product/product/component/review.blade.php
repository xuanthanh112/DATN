@php
    $totalReviews = $model->reviews()->count();
    $totalRate = number_format($model->reviews()->avg('score'), 1);
    $starPercent = ($totalReviews == 0) ? '0' : $totalRate/5*100;

    $fiveStar = $model->reviews()->where('score', 5)->count();
@endphp
<div class="review-container">
    <div class="panel-head">
       <h2 class="review-heading">Đánh giá sản phẩm</h2>
       <div class="review-statistic">
            <div class="uk-grid uk-grid-medium uk-flex uk-flex-middle">
                <div class="uk-width-large-1-3">
                    <div class="review-averate review-item">
                        <div class="title">Đánh giá trung bình</div>
                        <div class="score">{{ $totalRate }}/5</div>
                        <div class="star-rating">
                            <div class="stars" style="--star-width: {{ $starPercent  }}%"></div>
                        </div>
                        <div class="total-rate">{{ $totalReviews }} đánh giá</div>
                    </div>
                </div>
                <div class="uk-width-large-1-3">
                    <div class="progress-block review-item">
                        @for($i = 5; $i >= 1; $i--)
                        @php
                            $countStar = $model->reviews()->where('score', $i)->count();
                            $starPercent = ($countStar > 0) ? $countStar / $totalReviews * 100 : 0;
                        @endphp
                        <div class="progress-item">
                            <div class="uk-flex uk-flex-middle">
                                <span class="text">{{ $i }}</span>
                                <i class="fa fa-star"></i>
                                <div class="uk-progress">
                                    <div class="uk-progress-bar" style="width: {{ $starPercent }}%;"></div>
                                </div>
                                <span class="text">{{ $countStar }}</span>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
                <div class="uk-width-large-1-3">
                    <div class="review-action review-item" style="position: relative; z-index: 10;">
                        <div class="text">Bạn đã dùng sản phẩm này?</div>
                        <a href="javascript:void(0)" class="btn btn-review" id="btn-review-trigger" onclick="openReviewModalNow(event); return false;" style="cursor: pointer !important; pointer-events: auto !important; display: inline-block; text-decoration: none; position: relative; z-index: 9999 !important;">
                            {{ isset($existingReview) ? 'Chỉnh sửa đánh giá' : 'Gửi đánh giá' }}
                        </a>
                    </div>
                </div>
            </div>
       </div>
    </div>
    <div class="panel-body">
        <div class="review-filter">
            <div class="uk-flex uk-flex-middle">
                <span class="filter-text">Lọc xem theo: </span>
                <div class="filter-item">
                    <span class="filter-star-btn" data-star="all" style="cursor: pointer; padding: 5px 10px; border-radius: 4px; margin-right: 5px;">Tất cả</span>
                    <span class="filter-star-btn" data-star="5" style="cursor: pointer; padding: 5px 10px; border-radius: 4px; margin-right: 5px;">5 sao</span>
                    <span class="filter-star-btn" data-star="4" style="cursor: pointer; padding: 5px 10px; border-radius: 4px; margin-right: 5px;">4 sao</span>
                    <span class="filter-star-btn" data-star="3" style="cursor: pointer; padding: 5px 10px; border-radius: 4px; margin-right: 5px;">3 sao</span>
                    <span class="filter-star-btn" data-star="2" style="cursor: pointer; padding: 5px 10px; border-radius: 4px; margin-right: 5px;">2 sao</span>
                    <span class="filter-star-btn" data-star="1" style="cursor: pointer; padding: 5px 10px; border-radius: 4px; margin-right: 5px;">1 sao</span>
                </div>
            </div>
        </div>
        <div class="review-wrapper">
            @if($model->reviews && $model->reviews->count() > 0)
                @foreach($model->reviews as  $review)
                @php
                    $avatar = getReviewName($review->fullname);
                    $name = $review->fullname;
                    $email = $review->email;
                    $phone = $review->phone;
                    $description = $review->description;
                    $rating = generateStar($review->score);
                    $created_at = convertDateTime($review->created_at);
                @endphp
                <div class="review-block-item" data-review-score="{{ $review->score }}">
                    <div class="review-general uk-clearfix">
                        <div class="review-avatar">
                            <span class="shae">{{ $avatar }}</span>
                        </div>
                        <div class="review-content-block">
                            <div class="review-content">
                                <div class="name uk-flex uk-flex-middle">
                                    <span>{{ $name }}</span>
                                    {{-- <span class="review-buy">
                                        <i class="fa fa-check-circle" aria-hidden="true"></i>
                                        Đã mua hàng tại {{ $system['homepage_brand'] }}
                                    </span> --}}
                                </div>
                                {!! $rating !!}
                                <div class="description">
                                    {{ $description }}
                                </div>
                                <div class="review-toolbox">
                                    <div class="uk-flex uk-flex-middle">
                                        <div class="created_at">Ngày {{ $created_at }}</div>
                                        {{-- <div class="review-reply" data-uk-modal="{target:'#review'}">Trả lời</div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="review-block-item uk-clearfix reply-block">
                        <div class="review-avatar">
                            <span class="shae">LV</span>
                        </div>
                        <div class="review-content-block">
                            <div class="review-content">
                                <div class="name uk-flex uk-flex-middle">
                                    <span>Nguyễn Công Tuấn</span>
                                    <span class="review-buy">
                                        <i class="fa fa-check-circle" aria-hidden="true"></i>
                                        Đã mua hàng tại {{ $system['homepage_brand'] }}
                                    </span>
                                </div>
                                <div class="review-star">
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star-o"></i>
                                </div>
                                <div class="description">
                                    Chào anh Cường,
                                    Dạ Samsung Galaxy Z Flip4 5G 128GB có giá niêm yết 23.990.000đ, được giảm còn 11.990.000đ  áp dụng đơn hàng online đến 22h ngày 25/12 anh nha. Anh đang ở tỉnh, thành nào để bên em kiểm tra shop có hàng gần nhất ạ?  Để được hỗ trợ chi tiết về sản phẩm, anh vui lòng liên hệ tổng đài miễn phí 18006601 hoặc để lại SĐT bên em liên hệ tư vấn nhanh nhất ạ.Thân mến!
                                </div>
                                <div class="review-toolbox">
                                    <div class="uk-flex uk-flex-middle">
                                        <div class="created_at">Ngày 22/12/2023</div>
                                        <div class="review-reply">Trả lời</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<div id="review" class="uk-modal">
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        <div class="review-popup-wrapper">
            <div class="panel-head">Đánh giá sản phẩm</div>
            <div class="panel-body">
                <div class="product-preview">
                    <span class="image img-scaledown"><img src="{{ image($model->image) }}" alt="{{ $model->name }}"></span>
                    <div class="product-title uk-text-center">{{ $model->name }}</div>
                    <div class="popup-rating uk-clearfix uk-text-center">
                        <div class="rate uk-clearfix ">
                            <input type="radio" id="star5" name="rate" class="rate" value="5" {{ isset($existingReview) && $existingReview->score == 5 ? 'checked' : '' }} />
                            <label for="star5" title="Tuyệt vời">5 stars</label>
                            <input type="radio" id="star4" name="rate" class="rate" value="4" {{ isset($existingReview) && $existingReview->score == 4 ? 'checked' : '' }} />
                            <label for="star4" title="Hài lòng">4 stars</label>
                            <input type="radio" id="star3" name="rate" class="rate" value="3" {{ isset($existingReview) && $existingReview->score == 3 ? 'checked' : '' }} />
                            <label for="star3" title="Bình thường">3 stars</label>
                            <input type="radio" id="star2" name="rate" class="rate" value="2" {{ isset($existingReview) && $existingReview->score == 2 ? 'checked' : '' }} />
                            <label for="star2" title="Tạm được">2 stars</label>
                            <input type="radio" id="star1" name="rate" class="rate" value="1" {{ isset($existingReview) && $existingReview->score == 1 ? 'checked' : '' }} />
                            <label for="star1" title="Không thích">1 star</label>
                        </div>
                        <div class="rate-text uk-hidden">
                            Không thích
                        </div>
                    </div>
                    <div class="review-form">
                        <div action="" class="uk-form form">
                            <div class="form-row">
                                <textarea name="" id="" class="review-textarea" placeholder="Hãy chia sẻ cảm nhận của bạn về sản phẩm...">{{ isset($existingReview) ? $existingReview->description : '' }}</textarea>
                            </div>
                            <div class="form-row">
                                <div class="uk-flex uk-flex-middle">
                                    <div class="gender-item uk-flex uk-flex-middle">
                                        <input type="radio" name="gender" class="gender" value="Nam" id="male" {{ isset($existingReview) && $existingReview->gender == 'Nam' ? 'checked' : '' }}>
                                        <label for="male">Nam</label>
                                    </div>
                                    <div class="gender-item uk-flex uk-flex-middle">
                                        <input type="radio" name="gender" class="gender" value="Nữ" id="femail" {{ isset($existingReview) && $existingReview->gender == 'Nữ' ? 'checked' : '' }}>
                                        <label for="femail">Nữ</label>
                                    </div>
                                </div>
                            </div>
                            <div class="uk-grid uk-grid-medium">
                                <div class="uk-width-large-1-2">
                                    <div class="form-row">
                                        <input 
                                            type="text" 
                                            name="fullname" 
                                            value="{{ isset($existingReview) ? $existingReview->fullname : (isset($customer) ? $customer->name : '') }}" 
                                            class="review-text"
                                            placeholder="Nhập vào họ tên"
                                            {{ isset($customer) ? 'readonly' : '' }}
                                        >
                                    </div>
                                </div>
                                <div class="uk-width-large-1-2">
                                    <div class="form-row">
                                        <input 
                                            type="text" 
                                            name="phone" 
                                            value="{{ isset($existingReview) ? $existingReview->phone : (isset($customer) ? $customer->phone : '') }}" 
                                            class="review-text"
                                            placeholder="Nhập vào số điện thoại"
                                            {{ isset($customer) ? 'readonly' : '' }}
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <input 
                                    type="text" 
                                    name="email" 
                                    value="{{ isset($existingReview) ? $existingReview->email : (isset($customer) ? $customer->email : '') }}" 
                                    class="review-text"
                                    placeholder="Nhập vào email"
                                    {{ isset($customer) ? 'readonly' : '' }}
                                >
                            </div>
                            <div class="uk-text-center">
                                <button type="submit" value="send" class="btn-send-review" name="create">
                                    {{ isset($existingReview) ? 'Cập nhật đánh giá' : 'Hoàn tất' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" class="reviewable_type" value="{{ $reviewable }}">
<input type="hidden" class="reviewable_id" value="{{ $model->id }}">
<input type="hidden" class="review_parent_id" value="0">

<script>
// Hàm global để có thể gọi từ onclick
function openReviewModalNow(e){
    if(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    // Thử mở modal bằng UIkit
    if(typeof UI !== 'undefined' && UI.modal){
        try {
            var reviewModal = UI.modal("#review");
            if(reviewModal){
                reviewModal.show();
                return;
            }
        } catch(err) {
            console.error('Error with UI.modal:', err);
        }
    }
    
    // Fallback: hiển thị trực tiếp
    var modalElement = $('#review');
    if(modalElement.length){
        modalElement.addClass('uk-open').css({
            'display': 'block',
            'z-index': '10000',
            'position': 'fixed',
            'top': '0',
            'left': '0',
            'width': '100%',
            'height': '100%',
            'background': 'rgba(0,0,0,0.5)'
        });
        
        // Đảm bảo dialog hiển thị
        modalElement.find('.uk-modal-dialog').css({
            'position': 'relative',
            'margin': '50px auto',
            'background': 'white',
            'max-width': '600px',
            'z-index': '10001',
            'padding': '20px',
            'border-radius': '8px'
        });
        
        // Thêm overlay backdrop
        if($('.uk-modal-backdrop').length === 0){
            $('body').append('<div class="uk-modal-backdrop uk-open" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;"></div>');
        }
    } else {
        console.error('Modal element #review not found!');
    }
}

// Hàm đóng modal
function closeReviewModal(){
    // Thử đóng bằng UIkit
    if(typeof UI !== 'undefined' && UI.modal){
        try {
            var reviewModal = UI.modal("#review");
            if(reviewModal){
                reviewModal.hide();
                return;
            }
        } catch(err) {
            console.error('Error closing UI.modal:', err);
        }
    }
    
    // Fallback: đóng thủ công
    var modalElement = $('#review');
    modalElement.removeClass('uk-open').css('display', 'none');
    $('.uk-modal-backdrop').remove();
}

$(document).ready(function(){
    // Xử lý click vào button "Gửi đánh giá"
    $('.btn-review').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        openReviewModalNow(e);
    });
    
    // Xử lý click vào nút đóng modal (X)
    $(document).on('click', '.uk-modal-close, .uk-close', function(e){
        e.preventDefault();
        e.stopPropagation();
        closeReviewModal();
    });
    
    // Xử lý click vào backdrop để đóng modal
    $(document).on('click', '#review', function(e){
        // Chỉ đóng nếu click vào chính modal (không phải dialog)
        if($(e.target).attr('id') === 'review'){
            closeReviewModal();
        }
    });
    
    // Xử lý lọc đánh giá theo sao
    $('.filter-star-btn').on('click', function(){
        var selectedStar = $(this).data('star');
        
        // Xóa active class từ tất cả filter
        $('.filter-star-btn').removeClass('active');
        // Thêm active class cho filter được chọn
        $(this).addClass('active');
        
        // Xóa message "Không có đánh giá" nếu có
        $('.no-reviews-message').remove();
        
        // Lọc reviews
        if(selectedStar === 'all'){
            // Hiển thị tất cả reviews
            $('.review-block-item').show();
        } else {
            // Ẩn tất cả reviews
            $('.review-block-item').hide();
            // Chỉ hiển thị reviews có số sao khớp
            $('.review-block-item[data-review-score="' + selectedStar + '"]').show();
        }
        
        // Kiểm tra xem có review nào được hiển thị không
        var visibleReviews = $('.review-block-item:visible').length;
        if(visibleReviews === 0 && selectedStar !== 'all'){
            $('.review-wrapper').append('<div class="no-reviews-message" style="text-align: center; padding: 40px; color: #999; font-size: 16px;">Không có đánh giá nào với ' + selectedStar + ' sao</div>');
        }
    });
    
    // Mặc định chọn "Tất cả"
    $('.filter-star-btn[data-star="all"]').addClass('active');
});
</script>