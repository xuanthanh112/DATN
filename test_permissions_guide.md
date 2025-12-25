# HƯỚNG DẪN TEST HỆ THỐNG PHÂN QUYỀN

## BƯỚC 1: TẠO NHÓM THÀNH VIÊN MỚI

1. Đăng nhập vào Admin Panel
2. Vào menu: **QL Nhóm Thành Viên** → **QL Nhóm Thành Viên**
3. Click **"Thêm mới"**
4. Tạo nhóm mới:
   - **Tên**: "Editor" (hoặc tên khác)
   - **Mô tả**: "Nhóm Editor - chỉ xem và chỉnh sửa"
   - **Trạng thái**: Bật
5. Click **"Lưu lại"**

## BƯỚC 2: GÁN QUYỀN CHO NHÓM

1. Vào menu: **QL Nhóm Thành Viên** → **Cấp quyền**
2. Bạn sẽ thấy bảng:
   - **Cột dọc**: Danh sách các quyền (permissions)
   - **Cột ngang**: Danh sách các nhóm thành viên
3. Tích vào các checkbox để gán quyền cho nhóm "Editor"
   - Ví dụ: Chỉ tích `product.index`, `product.update` (không tích `product.create`, `product.destroy`)
4. Click **"Lưu lại"**

## BƯỚC 3: TẠO USER MỚI VÀ GÁN VÀO NHÓM

1. Vào menu: **QL Nhóm Thành Viên** → **QL Thành Viên**
2. Click **"Thêm mới"**
3. Điền thông tin:
   - **Tên**: "Editor Test"
   - **Email**: "editor@test.com"
   - **Mật khẩu**: "123456"
   - **Nhóm thành viên**: Chọn "Editor" (nhóm vừa tạo)
   - **Trạng thái**: Bật
4. Click **"Lưu lại"**

## BƯỚC 4: TEST QUYỀN

### Test 1: Đăng nhập với user Editor
1. Đăng xuất khỏi admin hiện tại
2. Đăng nhập với:
   - Email: `editor@test.com`
   - Password: `123456`

### Test 2: Kiểm tra quyền truy cập
- ✅ **Có quyền**: Vào được trang (ví dụ: `/admin/product/index` nếu đã gán `product.index`)
- ❌ **Không có quyền**: Sẽ bị lỗi 403 Forbidden (ví dụ: `/admin/product/create` nếu không gán `product.create`)

### Test 3: Kiểm tra trong Controller
Mở file `app/Http/Controllers/Backend/Product/ProductController.php`:
```php
public function index(Request $request){
    $this->authorize('modules', 'product.index'); // ← Kiểm tra quyền ở đây
    // ...
}
```

## BƯỚC 5: KIỂM TRA TRONG DATABASE

### Xem quyền của nhóm:
```sql
SELECT 
    uc.name as group_name,
    p.name as permission_name,
    p.canonical as permission_canonical
FROM user_catalogues uc
JOIN user_catalogue_permission ucp ON uc.id = ucp.user_catalogue_id
JOIN permissions p ON ucp.permission_id = p.id
WHERE uc.name = 'Editor';
```

### Xem user thuộc nhóm nào:
```sql
SELECT 
    u.name as user_name,
    u.email,
    uc.name as group_name
FROM users u
JOIN user_catalogues uc ON u.user_catalogue_id = uc.id
WHERE u.email = 'editor@test.com';
```

## DANH SÁCH CÁC QUYỀN PHỔ BIẾN

- `product.index` - Xem danh sách sản phẩm
- `product.create` - Tạo sản phẩm mới
- `product.update` - Cập nhật sản phẩm
- `product.destroy` - Xóa sản phẩm
- `order.index` - Xem danh sách đơn hàng
- `order.destroy` - Xóa đơn hàng
- `customer.index` - Xem danh sách khách hàng
- `warranty.index` - Xem danh sách bảo hành
- `user.catalogue.index` - Xem nhóm thành viên
- `user.catalogue.permission` - Cấp quyền
- `permission.index` - Xem danh sách quyền

## LƯU Ý

1. **User phải có `publish = 1`** mới có quyền (kiểm tra trong `AuthServiceProvider`)
2. **Quyền được kiểm tra bằng `canonical`** (ví dụ: `product.index`)
3. **Sidebar hiện tại KHÔNG kiểm tra quyền** - sẽ hiển thị tất cả menu nhưng truy cập sẽ bị chặn
4. **Nếu bị lỗi 403**: Kiểm tra xem user có quyền `permission.canonical` tương ứng không

