(function($) {
	"use strict";
	var HT = {}; // Khai báo là 1 đối tượng
	var timer;
    var _token = $('meta[name="csrf-token"]').attr('content');

    HT.review = () => {

        // Khởi tạo modal để sử dụng sau
        var modal = null;

        // Xử lý click vào button "Gửi đánh giá" để mở modal
        $(document).on('click', '.btn-review', function(e){
            e.preventDefault();
            e.stopPropagation();
            
            // Thử mở modal bằng UIkit trước
            if(typeof UIkit !== 'undefined'){
                try {
                    // Thử dùng UI.modal (UIkit 2)
                    if(typeof UI !== 'undefined' && UI.modal){
                        var reviewModal = UI.modal("#review");
                        if(reviewModal){
                            reviewModal.show();
                            return;
                        }
                    }
                    // Hoặc UIkit.modal (UIkit 3)
                    if(UIkit && UIkit.modal){
                        var reviewModal = UIkit.modal("#review");
                        if(reviewModal){
                            reviewModal.show();
                            return;
                        }
                    }
                } catch(err) {
                    console.error('Error with UIkit modal:', err);
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
                    'z-index': '10001'
                });
                
                // Thêm overlay backdrop
                if($('.uk-modal-backdrop').length === 0){
                    $('body').append('<div class="uk-modal-backdrop uk-open" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;"></div>');
                }
            } else {
                console.error('Modal element #review not found!');
            }
        });

        $(document).on('click', '.btn-send-review', function(){
            let option = {
                score: $('.rate:checked').val(),
                description : $('.review-textarea').val(),
                gender: $('.gender:checked').val(),
                fullname: $('.product-preview input[name=fullname]').val(),
                email: $('.product-preview input[name=email]').val(),
                phone: $('.product-preview input[name=phone]').val(),
                reviewable_type: $('.reviewable_type').val(),
                reviewable_id: $('.reviewable_id').val(),
                _token: _token,
                parent_id: $('.review_parent_id').val()
            }

            if(typeof option.score == 'undefined'){
                alert('Bạn chưa chọn điểm đánh giá')
                return false
            }

            $.ajax({
				url: 'ajax/review/create', 
				type: 'POST', 
				data: option, 
				dataType: 'json', 
				beforeSend: function() {
					
				},
				success: function(res) {
					if(res.code === 10){
                        toastr.success(res.messages, 'Thông báo từ hệ thống!')
                        
                        // Đóng modal
                        var modalElement = $('#review');
                        if(typeof UI !== 'undefined' && UI.modal){
                            try {
                                var reviewModal = UI.modal("#review");
                                if(reviewModal) reviewModal.hide();
                            } catch(e) {}
                        } else if(typeof UIkit !== 'undefined' && UIkit.modal){
                            try {
                                var reviewModal = UIkit.modal("#review");
                                if(reviewModal) reviewModal.hide();
                            } catch(e) {}
                        }
                        
                        // Fallback: đóng thủ công
                        modalElement.removeClass('uk-open').hide();
                        $('.uk-modal-backdrop').remove();
                        
                        location.reload()

                     }else{
                        toastr.error(res.messages, 'Thông báo từ hệ thống!')
                     }
				},
			});
        })
    }
	

	$(document).ready(function(){
		HT.review()
	});

})(jQuery);

