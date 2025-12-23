<form action="{{ route('order.index') }}">
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
                    <button type="button" class="btn btn-danger btn-sm ml10" id="deleteMultipleOrders" style="display:none;">
                        <i class="fa fa-trash"></i> Xóa đã chọn
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<form id="deleteMultipleForm" action="{{ route('order.destroyMultiple') }}" method="post" style="display:none;">
    @csrf
    <input type="hidden" name="id" id="selectedOrderIds">
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.checkBoxItem');
    const checkAll = document.getElementById('checkAll');
    const deleteBtn = document.getElementById('deleteMultipleOrders');
    const deleteForm = document.getElementById('deleteMultipleForm');
    const selectedIdsInput = document.getElementById('selectedOrderIds');
    
    function updateDeleteButton() {
        const checked = document.querySelectorAll('.checkBoxItem:checked');
        if(checked.length > 0) {
            deleteBtn.style.display = 'inline-block';
        } else {
            deleteBtn.style.display = 'none';
        }
    }
    
    checkAll?.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateDeleteButton();
    });
    
    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateDeleteButton);
    });
    
    deleteBtn.addEventListener('click', function() {
        const checked = document.querySelectorAll('.checkBoxItem:checked');
        const ids = Array.from(checked).map(cb => cb.value);
        
        if(ids.length === 0) {
            alert('Vui lòng chọn ít nhất một đơn hàng để xóa!');
            return;
        }
        
        if(confirm('Bạn có chắc chắn muốn xóa ' + ids.length + ' đơn hàng đã chọn? Hành động này không thể hoàn tác!')) {
            selectedIdsInput.value = JSON.stringify(ids);
            deleteForm.submit();
        }
    });
});
</script>