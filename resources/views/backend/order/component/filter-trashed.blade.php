<form action="{{ route('order.trashed') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            <div class="uk-flex uk-flex-middle">
                @include('backend.dashboard.component.perpage')
                <div class="date-item-box">
                    <input 
                        type="type" 
                        name="created_at" 
                        readonly 
                        value="{{ request('created_at') ?: old('created_at') }}" class="rangepicker form-control"
                        placeholder="Click chọn ngày"
                    >
                </div>
            </div>
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    <div class="mr10">
                        @foreach(__('cart') as $key => $val)
                        @php
                            ${$key} = request($key) ?: old($key);
                        @endphp
                        <select name="{{ $key }}" class="form-control setupSelect2 ml10">
                            @foreach($val as $index => $item)
                            <option {{ (${$key} == $index)  ? 'selected' : '' }} value="{{ $index }}">{{ $item }}</option>
                            @endforeach
                        </select>
                        @endforeach
                    </div>
                    @include('backend.dashboard.component.keyword')
                    <button type="button" class="btn btn-success btn-sm ml10" id="restoreMultipleOrders" style="display:none;">
                        <i class="fa fa-undo"></i> Khôi phục đã chọn
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<form id="restoreMultipleForm" action="{{ route('order.restoreMultiple') }}" method="post" style="display:none;">
    @csrf
    <input type="hidden" name="id" id="selectedOrderIds">
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.checkBoxItem');
    const checkAll = document.getElementById('checkAll');
    const restoreBtn = document.getElementById('restoreMultipleOrders');
    const restoreForm = document.getElementById('restoreMultipleForm');
    const selectedIdsInput = document.getElementById('selectedOrderIds');
    
    function updateRestoreButton() {
        const checked = document.querySelectorAll('.checkBoxItem:checked');
        if(checked.length > 0) {
            restoreBtn.style.display = 'inline-block';
        } else {
            restoreBtn.style.display = 'none';
        }
    }
    
    checkAll?.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateRestoreButton();
    });
    
    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateRestoreButton);
    });
    
    restoreBtn.addEventListener('click', function() {
        const checked = document.querySelectorAll('.checkBoxItem:checked');
        const ids = Array.from(checked).map(cb => cb.value);
        
        if(ids.length === 0) {
            alert('Vui lòng chọn ít nhất một đơn hàng để khôi phục!');
            return;
        }
        
        if(confirm('Bạn có chắc chắn muốn khôi phục ' + ids.length + ' đơn hàng đã chọn?')) {
            selectedIdsInput.value = JSON.stringify(ids);
            restoreForm.submit();
        }
    });
});
</script>

