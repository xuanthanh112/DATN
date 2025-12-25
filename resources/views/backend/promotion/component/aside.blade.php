<div class="col-lg-4">
    <div class="ibox">
        <div class="ibox-title">
            <h5>Thời gian áp dụng chương trình</h5>
        </div>
        <div class="ibox-content">
            <div class="form-row mb15">
                <label for="" class="control-label text-left">Ngày bắt đầu <span class="text-danger"> (*)</span></label>
                <div class="form-date">
                    <input 
                        type="text"
                        name="startDate"
                        value="{{ old('startDate', (isset($model->startDate)) ? convertDateTime($model->startDate) : null) }}"
                        class="form-control datepicker"
                        placeholder=""
                        autocomplete="off"
                    >
                    <span><i class="fa fa-calendar"></i></span>
                </div>
            </div>
            <div class="form-row mb15">
                <label for="" class="control-label text-left">Ngày kết thúc <span class="text-danger"> (*)</span></label>
                <div class="form-date">
                    <input 
                        type="text"
                        name="endDate"
                        value="{{ 
                            old('endDate', (isset($model->endDate)) ? convertDateTime($model->endDate) : null ) 
                        }}"
                        class="form-control datepicker"
                        placeholder=""
                        autocomplete="off"
                        @if((old('neverEndDate', ($model->neverEndDate ?? null)) == 'accept'))
                            disabled
                        @endif
                    >
                    <span><i class="fa fa-calendar"></i></span>
                </div>
            </div>
            <div class="form-row">
                <div class="uk-flex uk-flex-middle">
                    <input 
                        type="checkbox"
                        name="neverEndDate"
                        value="accept"
                        class=""
                        id="neverEnd"
                        @if((old('neverEndDate', ($model->neverEndDate ?? null)) == 'accept'))
                            checked="checked"
                        @endif
                    >
                    <label class="fix-label ml5" for="neverEnd">Không có ngày kết thúc</label>
                </div>
            </div>
        </div>
    </div>
    {{-- Phần "Đối tượng áp dụng" đã được loại bỏ --}}
</div>
<input 
    type="hidden" 
    class="input-product-and-quantity" 
    value="{{ json_encode(__('module.item')) }}"
>
<input 
    type="hidden" 
    name="applyStatus" 
    value="all"
>