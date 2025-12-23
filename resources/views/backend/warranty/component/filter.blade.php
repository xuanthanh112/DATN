<form action="{{ route('warranty.index') }}" method="GET">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            <div class="perpage">
                <div class="uk-flex uk-flex-middle uk-gap-small">
                    <select name="perpage" class="input-sm perpage filter mr10">
                        @for($i = 20; $i <= 100; $i+=20)
                            <option {{ ($i == request('perpage')) ? 'selected' : '' }} value="{{ $i }}">{{ $i }} bản ghi</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    <div class="uk-search uk-flex uk-flex-middle mr10">
                        <input 
                            type="text" 
                            name="keyword" 
                            value="{{ request('keyword') }}" 
                            placeholder="Tìm tên, SĐT, email, sản phẩm..." 
                            class="input-search"
                            style="width: 300px;"
                        >
                        <button type="submit" class="btn btn-primary btn-sm ml10">Tìm kiếm</button>
                    </div>
                    
                    <select name="status" class="setupSelect2 ml10" style="width:150px;">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang BH</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Hết hạn</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                    </select>
                    
                    <button type="submit" class="btn btn-success btn-sm ml10">
                        <i class="fa fa-filter"></i> Lọc
                    </button>
                    
                    <a href="{{ route('warranty.index') }}" class="btn btn-warning btn-sm ml10">
                        <i class="fa fa-refresh"></i> Reset
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

