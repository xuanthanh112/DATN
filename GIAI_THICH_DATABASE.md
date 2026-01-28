# GIẢI THÍCH CHI TIẾT VỀ DATABASE

## 1. TỔNG QUAN

Hệ thống sử dụng MySQL/MariaDB với khoảng 30+ bảng, được tổ chức theo mô hình quan hệ (Relational Database). Database hỗ trợ đa ngôn ngữ, soft delete, và các mối quan hệ phức tạp.

---

## 2. CÁC NHÓM BẢNG CHÍNH

### 2.1. NHÓM QUẢN LÝ NGƯỜI DÙNG VÀ PHÂN QUYỀN

#### **2.1.1. Bảng `users`**
**Mục đích:** Lưu thông tin tài khoản quản trị viên (Admin, Editor, Writer)

**Các trường chính:**
- `id`: Primary Key
- `email`: Email đăng nhập (unique)
- `password`: Mật khẩu đã hash
- `name`: Tên người dùng
- `user_catalogue_id`: Foreign Key → `user_catalogues.id` (nhóm người dùng)
- `publish`: Trạng thái (1 = active, 0 = inactive)
- `deleted_at`: Soft delete timestamp

**Quan hệ:**
- `belongsTo(UserCatalogue)`: Mỗi user thuộc một nhóm người dùng

#### **2.1.2. Bảng `user_catalogues`**
**Mục đích:** Quản lý các nhóm người dùng (Admin, Editor, Writer, etc.)

**Các trường chính:**
- `id`: Primary Key
- `name`: Tên nhóm (ví dụ: "Administrator", "Editor")
- `description`: Mô tả nhóm
- `publish`: Trạng thái

**Quan hệ:**
- `hasMany(User)`: Một nhóm có nhiều users
- `belongsToMany(Permission)` qua `user_catalogue_permission`: Một nhóm có nhiều quyền

#### **2.1.3. Bảng `permissions`**
**Mục đích:** Danh sách các quyền trong hệ thống

**Các trường chính:**
- `id`: Primary Key
- `name`: Tên quyền
- `canonical`: Mã quyền duy nhất (ví dụ: "modules.user.catalogue.index")
- `module`: Module thuộc về (ví dụ: "user", "product", "order")

**Quan hệ:**
- `belongsToMany(UserCatalogue)` qua `user_catalogue_permission`: Một quyền được gán cho nhiều nhóm

#### **2.1.4. Bảng `user_catalogue_permission` (Pivot Table)**
**Mục đích:** Liên kết nhóm người dùng với quyền (Many-to-Many)

**Các trường:**
- `user_catalogue_id`: Foreign Key → `user_catalogues.id`
- `permission_id`: Foreign Key → `permissions.id`
- `created_at`, `updated_at`: Timestamps

**Quan hệ:**
- Pivot table cho quan hệ Many-to-Many giữa `user_catalogues` và `permissions`

---

### 2.2. NHÓM QUẢN LÝ KHÁCH HÀNG

#### **2.2.1. Bảng `customers`**
**Mục đích:** Lưu thông tin khách hàng (người mua hàng)

**Các trường chính:**
- `id`: Primary Key
- `name`: Tên khách hàng
- `email`: Email (unique)
- `password`: Mật khẩu đã hash
- `phone`: Số điện thoại
- `customer_catalogue_id`: Foreign Key → `customer_catalogues.id` (nhóm khách hàng)
- `province_id`, `district_id`, `ward_id`: Địa chỉ
- `address`: Địa chỉ chi tiết
- `publish`: Trạng thái
- `deleted_at`: Soft delete

**Quan hệ:**
- `belongsTo(CustomerCatalogue)`: Mỗi khách hàng thuộc một nhóm
- `hasMany(Order)`: Một khách hàng có nhiều đơn hàng (quan hệ optional - hỗ trợ khách vãng lai)
- `hasMany(ProductWarranty)`: Một khách hàng có nhiều bảo hành

#### **2.2.2. Bảng `customer_catalogues`**
**Mục đích:** Phân loại khách hàng (VIP, Thường, Doanh nghiệp, etc.)

**Các trường chính:**
- `id`: Primary Key
- `name`: Tên nhóm khách hàng
- `description`: Mô tả

**Quan hệ:**
- `hasMany(Customer)`: Một nhóm có nhiều khách hàng

---

### 2.3. NHÓM QUẢN LÝ SẢN PHẨM

#### **2.3.1. Bảng `products`**
**Mục đích:** Lưu thông tin sản phẩm (dữ liệu chung, không phụ thuộc ngôn ngữ)

**Các trường chính:**
- `id`: Primary Key
- `code`: Mã sản phẩm
- `image`: Ảnh đại diện
- `album`: JSON array chứa danh sách ảnh
- `price`: Giá sản phẩm
- `warranty`: Số tháng bảo hành
- `made_in`: Xuất xứ
- `product_catalogue_id`: Foreign Key → `product_catalogues.id` (danh mục chính)
- `user_id`: Foreign Key → `users.id` (người tạo)
- `publish`: Trạng thái hiển thị
- `deleted_at`: Soft delete

**Quan hệ:**
- `belongsTo(User)`: Mỗi sản phẩm được tạo bởi một user
- `belongsToMany(ProductCatalogue)` qua `product_catalogue_product`: Một sản phẩm có thể thuộc nhiều danh mục
- `belongsToMany(Language)` qua `product_language`: Một sản phẩm có nội dung nhiều ngôn ngữ
- `hasMany(ProductVariant)`: Một sản phẩm có nhiều biến thể (variant)
- `belongsToMany(Order)` qua `order_product`: Một sản phẩm có trong nhiều đơn hàng
- `belongsToMany(Promotion)` qua `promotion_product_variant`: Một sản phẩm có thể thuộc nhiều khuyến mãi
- `morphMany(Review)`: Một sản phẩm có nhiều đánh giá (polymorphic)
- `hasMany(ProductWarranty)`: Một sản phẩm có nhiều bảo hành

#### **2.3.2. Bảng `product_catalogues`**
**Mục đích:** Danh mục sản phẩm đa cấp (sử dụng Nested Set Model)

**Các trường chính:**
- `id`: Primary Key
- `parent_id`: ID danh mục cha (0 = root)
- `lft`, `rgt`: Left và Right values cho Nested Set Model
- `level`: Cấp độ trong cây (0 = root)
- `image`: Ảnh danh mục
- `user_id`: Foreign Key → `users.id` (người tạo)
- `attribute`: JSON chứa thuộc tính danh mục
- `publish`: Trạng thái
- `deleted_at`: Soft delete

**Quan hệ:**
- `belongsTo(User)`: Mỗi danh mục được tạo bởi một user
- `belongsToMany(Product)` qua `product_catalogue_product`: Một danh mục có nhiều sản phẩm
- `belongsToMany(Language)` qua `product_catalogue_language`: Một danh mục có nội dung nhiều ngôn ngữ

**Đặc biệt:** Sử dụng **Nested Set Model** để quản lý cây danh mục đa cấp, cho phép truy vấn toàn bộ cây con trong một lần query.

#### **2.3.3. Bảng `product_language` (Pivot Table)**
**Mục đích:** Lưu nội dung đa ngôn ngữ của sản phẩm

**Các trường:**
- `product_id`: Foreign Key → `products.id`
- `language_id`: Foreign Key → `languages.id`
- `name`: Tên sản phẩm (theo ngôn ngữ)
- `canonical`: URL slug (unique)
- `description`: Mô tả ngắn
- `content`: Nội dung chi tiết
- `meta_title`, `meta_keyword`, `meta_description`: SEO metadata
- `url`: URL tùy chỉnh
- `created_at`, `updated_at`: Timestamps

**Quan hệ:**
- Pivot table cho quan hệ Many-to-Many giữa `products` và `languages`

**Lưu ý quan trọng:** Khi chuyển ngôn ngữ, hệ thống tìm sản phẩm dựa trên `product_language` với `language_id` tương ứng. Nếu không có bản ghi trong bảng này cho ngôn ngữ đó, sản phẩm sẽ không hiển thị.

#### **2.3.4. Bảng `product_catalogue_product` (Pivot Table)**
**Mục đích:** Liên kết sản phẩm với danh mục (Many-to-Many)

**Các trường:**
- `product_id`: Foreign Key → `products.id`
- `product_catalogue_id`: Foreign Key → `product_catalogues.id`

**Quan hệ:**
- Pivot table cho quan hệ Many-to-Many giữa `products` và `product_catalogues`

#### **2.3.5. Bảng `product_variants`**
**Mục đích:** Lưu thông tin biến thể sản phẩm (màu sắc, kích thước, etc.)

**Các trường chính:**
- `id`: Primary Key
- `product_id`: Foreign Key → `products.id`
- `uuid`: UUID duy nhất cho variant (dùng trong order_product)
- `price`: Giá variant (có thể khác giá sản phẩm gốc)
- `image`: Ảnh variant
- `publish`: Trạng thái

**Quan hệ:**
- `belongsTo(Product)`: Mỗi variant thuộc một sản phẩm
- `belongsToMany(Language)` qua `product_variant_language`: Variant có tên nhiều ngôn ngữ

---

### 2.4. NHÓM QUẢN LÝ ĐƠN HÀNG

#### **2.4.1. Bảng `orders`**
**Mục đích:** Lưu thông tin đơn hàng

**Các trường chính:**
- `id`: Primary Key
- `code`: Mã đơn hàng (tự động sinh, unique)
- `fullname`: Họ tên người nhận
- `phone`: Số điện thoại
- `email`: Email
- `province_id`, `district_id`, `ward_id`: Địa chỉ giao hàng
- `address`: Địa chỉ chi tiết
- `cart`: JSON chứa thông tin giỏ hàng (tổng tiền, số lượng, etc.)
- `promotion`: JSON chứa thông tin khuyến mãi đã áp dụng
- `customer_id`: Foreign Key → `customers.id` (nullable - hỗ trợ khách vãng lai)
- `guest_cookie`: Cookie cho khách vãng lai (nullable)
- `method`: Phương thức thanh toán (COD, VNPay, MoMo, PayPal)
- `confirm`: Trạng thái xác nhận (pending, confirm, cancle)
- `payment`: Trạng thái thanh toán (unpaid, paid)
- `delivery`: Trạng thái giao hàng (pending, shipping, success)
- `shipping`: Phí vận chuyển
- `description`: Ghi chú của khách hàng
- `deleted_at`: Soft delete

**Quan hệ:**
- `belongsTo(Customer)`: Mỗi đơn hàng có thể thuộc một khách hàng (optional)
- `belongsToMany(Product)` qua `order_product`: Một đơn hàng có nhiều sản phẩm
- `hasMany(OrderPayment)`: Một đơn hàng có thể có nhiều lần thanh toán
- `hasMany(ProductWarranty)`: Một đơn hàng có thể tạo nhiều bảo hành (optional)

**Đặc biệt:**
- `cart` và `promotion` lưu dạng JSON để linh hoạt lưu trữ dữ liệu phức tạp
- `customer_id` nullable để hỗ trợ đơn hàng của khách vãng lai

#### **2.4.2. Bảng `order_product` (Pivot Table)**
**Mục đích:** Lưu chi tiết sản phẩm trong đơn hàng

**Các trường:**
- `id`: Primary Key
- `order_id`: Foreign Key → `orders.id`
- `product_id`: Foreign Key → `products.id`
- `uuid`: UUID của variant sản phẩm (nếu có)
- `name`: Tên sản phẩm tại thời điểm đặt hàng (lưu snapshot)
- `qty`: Số lượng
- `price`: Giá tại thời điểm đặt hàng (sau khi áp dụng khuyến mãi)
- `priceOriginal`: Giá gốc trước khuyến mãi
- `option`: JSON chứa thông tin option (màu, size, etc.)

**Quan hệ:**
- Pivot table cho quan hệ Many-to-Many giữa `orders` và `products`
- Lưu snapshot dữ liệu tại thời điểm đặt hàng (không bị ảnh hưởng khi sản phẩm thay đổi sau đó)

#### **2.4.3. Bảng `order_paymentable` (Polymorphic)**
**Mục đích:** Lưu lịch sử thanh toán (hỗ trợ thanh toán nhiều lần)

**Các trường:**
- `id`: Primary Key
- `order_id`: Foreign Key → `orders.id`
- `method`: Phương thức thanh toán (VNPay, MoMo, PayPal, COD)
- `amount`: Số tiền thanh toán
- `status`: Trạng thái (pending, success, failed)
- `transaction_id`: Mã giao dịch từ payment gateway
- `paymentable_type`, `paymentable_id`: Polymorphic (có thể liên kết với nhiều loại đối tượng)

**Quan hệ:**
- `belongsTo(Order)`: Mỗi thanh toán thuộc một đơn hàng
- Polymorphic relation: Có thể liên kết với nhiều loại đối tượng khác

---

### 2.5. NHÓM QUẢN LÝ BẢO HÀNH

#### **2.5.1. Bảng `product_warranties`**
**Mục đích:** Lưu thông tin bảo hành sản phẩm

**Các trường chính:**
- `id`: Primary Key
- `order_id`: Foreign Key → `orders.id` (nullable - hỗ trợ bảo hành offline)
- `order_product_uuid`: UUID sản phẩm trong đơn hàng (để xác định chính xác variant)
- `product_id`: Foreign Key → `products.id`
- `product_name`: Tên sản phẩm (snapshot)
- `product_code`: Mã sản phẩm
- `serial_number`: Số serial (nếu có)
- `customer_id`: Foreign Key → `customers.id` (nullable - hỗ trợ khách vãng lai)
- `customer_name`, `customer_phone`, `customer_email`: Thông tin khách hàng (snapshot)
- `customer_address`: Địa chỉ khách hàng
- `province_id`, `district_id`, `ward_id`: Địa chỉ
- `purchase_date`: Ngày mua hàng
- `activation_date`: Ngày kích hoạt bảo hành
- `warranty_months`: Số tháng bảo hành
- `warranty_end_date`: Ngày hết hạn bảo hành (tự động tính)
- `qr_code`: Mã QR duy nhất để tra cứu
- `product_images`: JSON chứa ảnh sản phẩm khi kích hoạt
- `invoice_image`: Ảnh hóa đơn
- `status`: Trạng thái (pending, active, expired, rejected)
- `customer_note`: Ghi chú của khách hàng
- `admin_note`: Ghi chú của admin
- `deleted_at`: Soft delete

**Quan hệ:**
- `belongsTo(Order)`: Mỗi bảo hành có thể liên kết với một đơn hàng (optional)
- `belongsTo(Product)`: Mỗi bảo hành thuộc một sản phẩm
- `belongsTo(Customer)`: Mỗi bảo hành có thể thuộc một khách hàng (optional)

**Đặc biệt:**
- Hỗ trợ cả bảo hành từ đơn hàng online và bảo hành offline (không có order_id)
- Lưu snapshot thông tin khách hàng và sản phẩm tại thời điểm kích hoạt
- QR code unique để tra cứu nhanh

---

### 2.6. NHÓM QUẢN LÝ KHUYẾN MÃI

#### **2.6.1. Bảng `promotions`**
**Mục đích:** Lưu thông tin chương trình khuyến mãi

**Các trường chính:**
- `id`: Primary Key
- `name`: Tên chương trình
- `code`: Mã khuyến mãi (unique)
- `type`: Loại khuyến mãi (percentage, fixed, product_and_quantity, order_amount_range)
- `description`: Mô tả
- `method`: Phương thức áp dụng
- `discountInformation`: JSON chứa thông tin chi tiết điều kiện khuyến mãi
- `discountValue`: Giá trị giảm giá
- `discountType`: Loại giảm giá (percentage, fixed)
- `maxDiscountValue`: Giá trị giảm tối đa
- `startDate`, `endDate`: Thời gian áp dụng
- `neverEndDate`: Boolean - không có ngày kết thúc
- `publish`: Trạng thái
- `deleted_at`: Soft delete

**Quan hệ:**
- `belongsToMany(Product)` qua `promotion_product_variant`: Một khuyến mãi áp dụng cho nhiều sản phẩm/variant

#### **2.6.2. Bảng `promotion_product_variant` (Pivot Table)**
**Mục đích:** Liên kết khuyến mãi với sản phẩm/variant cụ thể

**Các trường:**
- `id`: Primary Key
- `promotion_id`: Foreign Key → `promotions.id`
- `product_id`: Foreign Key → `products.id`
- `variant_uuid`: UUID của variant (nếu áp dụng cho variant cụ thể)
- `model`: Model sản phẩm
- `created_at`, `updated_at`: Timestamps

**Quan hệ:**
- Pivot table cho quan hệ Many-to-Many giữa `promotions` và `products`

---

### 2.7. NHÓM ĐA NGÔN NGỮ

#### **2.7.1. Bảng `languages`**
**Mục đích:** Quản lý các ngôn ngữ hệ thống

**Các trường chính:**
- `id`: Primary Key
- `name`: Tên ngôn ngữ (ví dụ: "Tiếng Việt", "English")
- `canonical`: Mã ngôn ngữ (ví dụ: "vn", "en") - unique
- `image`: Ảnh cờ
- `publish`: Trạng thái
- `current`: Ngôn ngữ đang được sử dụng
- `default`: Ngôn ngữ mặc định
- `deleted_at`: Soft delete

**Quan hệ:**
- `belongsToMany(Product)` qua `product_language`: Một ngôn ngữ được dùng cho nhiều sản phẩm
- `belongsToMany(ProductCatalogue)` qua `product_catalogue_language`: Một ngôn ngữ được dùng cho nhiều danh mục
- `belongsToMany(Post)` qua `post_language`: Một ngôn ngữ được dùng cho nhiều bài viết
- `belongsToMany(PostCatalogue)` qua `post_catalogue_language`: Một ngôn ngữ được dùng cho nhiều danh mục bài viết
- `hasMany(System)`: Một ngôn ngữ có nhiều cấu hình hệ thống

---

### 2.8. NHÓM QUẢN LÝ NỘI DUNG

#### **2.8.1. Bảng `posts`**
**Mục đích:** Lưu thông tin bài viết/tin tức

**Các trường chính:**
- `id`: Primary Key
- `image`: Ảnh đại diện
- `album`: JSON array ảnh
- `video`: Video (nếu có)
- `publish`: Trạng thái
- `user_id`: Foreign Key → `users.id` (người tạo)
- `deleted_at`: Soft delete

**Quan hệ:**
- `belongsTo(User)`: Mỗi bài viết được tạo bởi một user
- `belongsToMany(PostCatalogue)` qua `post_catalogue_post`: Một bài viết có thể thuộc nhiều danh mục
- `belongsToMany(Language)` qua `post_language`: Một bài viết có nội dung nhiều ngôn ngữ
- `morphMany(Review)`: Một bài viết có thể có đánh giá (polymorphic)

#### **2.8.2. Bảng `post_catalogues`**
**Mục đích:** Danh mục bài viết

**Các trường chính:**
- `id`: Primary Key
- `parent_id`: ID danh mục cha
- `image`: Ảnh danh mục
- `publish`: Trạng thái
- `follow`: Follow/noindex cho SEO
- `user_id`: Foreign Key → `users.id`
- `deleted_at`: Soft delete

**Quan hệ:**
- `belongsTo(User)`: Mỗi danh mục được tạo bởi một user
- `belongsToMany(Post)` qua `post_catalogue_post`: Một danh mục có nhiều bài viết
- `belongsToMany(Language)` qua `post_catalogue_language`: Một danh mục có nội dung nhiều ngôn ngữ

---

### 2.9. NHÓM ĐÁNH GIÁ VÀ PHẢN HỒI

#### **2.9.1. Bảng `reviews`**
**Mục đích:** Lưu đánh giá sản phẩm/bài viết (Polymorphic)

**Các trường chính:**
- `id`: Primary Key
- `reviewable_type`: Loại đối tượng được đánh giá (Product, Post, etc.)
- `reviewable_id`: ID của đối tượng được đánh giá
- `email`: Email người đánh giá
- `fullname`: Tên người đánh giá
- `phone`: Số điện thoại
- `description`: Nội dung đánh giá
- `score`: Điểm đánh giá (1-5 sao)

**Quan hệ:**
- `morphTo()`: Polymorphic relation - có thể đánh giá cho Product, Post, hoặc các model khác

**Đặc biệt:** Sử dụng Polymorphic Relations để một bảng có thể liên kết với nhiều loại đối tượng khác nhau.

---

### 2.10. NHÓM CẤU HÌNH VÀ GIAO DIỆN

#### **2.10.1. Bảng `systems`**
**Mục đích:** Lưu cấu hình hệ thống (đa ngôn ngữ)

**Các trường chính:**
- `id`: Primary Key
- `language_id`: Foreign Key → `languages.id`
- `keyword`: Từ khóa cấu hình
- `content`: Nội dung cấu hình (JSON hoặc text)

**Quan hệ:**
- `belongsTo(Language)`: Mỗi cấu hình thuộc một ngôn ngữ

#### **2.10.2. Bảng `menus`**
**Mục đích:** Quản lý menu hệ thống

**Các trường chính:**
- `id`: Primary Key
- `menu_catalogue_id`: Foreign Key → `menu_catalogues.id`
- `parent_id`: ID menu cha
- `url`: URL menu
- `icon`: Icon menu
- `order`: Thứ tự hiển thị
- `publish`: Trạng thái

**Quan hệ:**
- `belongsTo(MenuCatalogue)`: Mỗi menu thuộc một nhóm menu
- `belongsToMany(Language)` qua `menu_language`: Menu có tên nhiều ngôn ngữ

#### **2.10.3. Bảng `slides`**
**Mục đích:** Quản lý slide/banner trang chủ

**Các trường chính:**
- `id`: Primary Key
- `image`: Ảnh slide
- `link`: Link khi click
- `order`: Thứ tự hiển thị
- `publish`: Trạng thái

#### **2.10.4. Bảng `widgets`**
**Mục đích:** Quản lý widget trang chủ (custom web interface)

**Các trường chính:**
- `id`: Primary Key
- `keyword`: Mã widget (ví dụ: "products-hl", "products-new")
- `name`: Tên widget
- `content`: Nội dung widget (JSON)
- `publish`: Trạng thái

**Quan hệ:**
- `belongsToMany(Language)`: Widget có nội dung nhiều ngôn ngữ

---

### 2.11. NHÓM HỖ TRỢ

#### **2.11.1. Bảng `routers`**
**Mục đích:** Quản lý routing URL (đa ngôn ngữ)

**Các trường chính:**
- `id`: Primary Key
- `canonical`: URL slug
- `module`: Module (product, post, etc.)
- `object_id`: ID đối tượng
- `language_id`: Foreign Key → `languages.id`

**Quan hệ:**
- `belongsTo(Language)`: Mỗi router thuộc một ngôn ngữ

#### **2.11.2. Bảng `sources`**
**Mục đích:** Quản lý nguồn khách hàng

**Các trường chính:**
- `id`: Primary Key
- `name`: Tên nguồn
- `description`: Mô tả

**Quan hệ:**
- `hasMany(Customer)`: Một nguồn có nhiều khách hàng

---

## 3. CÁC MỐI QUAN HỆ CHÍNH

### 3.1. Quan hệ một-nhiều (One-to-Many)

1. **`user_catalogues` → `users`**: Một nhóm người dùng có nhiều users
2. **`users` → `products`**: Một user tạo nhiều sản phẩm
3. **`users` → `product_catalogues`**: Một user tạo nhiều danh mục
4. **`customers` → `orders`**: Một khách hàng có nhiều đơn hàng (optional)
5. **`orders` → `order_product`**: Một đơn hàng có nhiều sản phẩm
6. **`orders` → `order_paymentable`**: Một đơn hàng có nhiều lần thanh toán
7. **`orders` → `product_warranties`**: Một đơn hàng có thể tạo nhiều bảo hành (optional)
8. **`products` → `product_variants`**: Một sản phẩm có nhiều variant
9. **`products` → `product_warranties`**: Một sản phẩm có nhiều bảo hành
10. **`customers` → `product_warranties`**: Một khách hàng có nhiều bảo hành (optional)
11. **`languages` → `systems`**: Một ngôn ngữ có nhiều cấu hình hệ thống

### 3.2. Quan hệ nhiều-nhiều (Many-to-Many)

1. **`products` ↔ `languages`** (qua `product_language`): Sản phẩm đa ngôn ngữ
2. **`products` ↔ `product_catalogues`** (qua `product_catalogue_product`): Sản phẩm thuộc nhiều danh mục
3. **`products` ↔ `orders`** (qua `order_product`): Sản phẩm có trong nhiều đơn hàng
4. **`products` ↔ `promotions`** (qua `promotion_product_variant`): Sản phẩm thuộc nhiều khuyến mãi
5. **`user_catalogues` ↔ `permissions`** (qua `user_catalogue_permission`): Nhóm người dùng có nhiều quyền
6. **`product_catalogues` ↔ `languages`** (qua `product_catalogue_language`): Danh mục đa ngôn ngữ
7. **`posts` ↔ `post_catalogues`** (qua `post_catalogue_post`): Bài viết thuộc nhiều danh mục
8. **`posts` ↔ `languages`** (qua `post_language`): Bài viết đa ngôn ngữ

### 3.3. Quan hệ đặc biệt

#### **3.3.1. Polymorphic Relations**
- **`reviews`**: Có thể đánh giá cho `Product`, `Post`, hoặc các model khác thông qua `reviewable_type` và `reviewable_id`
- **`order_paymentable`**: Có thể liên kết với nhiều loại đối tượng

#### **3.3.2. Nested Set Model**
- **`product_catalogues`**: Sử dụng `parent_id`, `lft`, `rgt`, `level` để quản lý cây danh mục đa cấp
- Cho phép truy vấn toàn bộ cây con trong một lần query

#### **3.3.3. Optional Foreign Keys**
- **`orders.customer_id`**: Nullable để hỗ trợ khách vãng lai
- **`product_warranties.order_id`**: Nullable để hỗ trợ bảo hành offline
- **`product_warranties.customer_id`**: Nullable để hỗ trợ khách vãng lai

---

## 4. CÁC ĐẶC ĐIỂM ĐẶC BIỆT

### 4.1. Soft Deletes
Hầu hết các bảng chính sử dụng soft delete (`deleted_at`):
- Dữ liệu không bị xóa vĩnh viễn
- Có thể khôi phục
- Giữ lại dữ liệu cho báo cáo thống kê
- Áp dụng cho: `users`, `products`, `orders`, `customers`, `product_catalogues`, `languages`, etc.

### 4.2. JSON Storage
Một số bảng lưu dữ liệu phức tạp dạng JSON:
- **`orders.cart`**: Thông tin giỏ hàng (tổng tiền, số lượng, chi tiết)
- **`orders.promotion`**: Thông tin khuyến mãi đã áp dụng
- **`order_product.option`**: Option của sản phẩm (màu, size, etc.)
- **`products.album`**: Danh sách ảnh sản phẩm
- **`promotions.discountInformation`**: Thông tin chi tiết điều kiện khuyến mãi
- **`product_catalogues.attribute`**: Thuộc tính danh mục

### 4.3. Timestamps
Hầu hết các bảng có `created_at` và `updated_at` để theo dõi thời gian tạo và cập nhật.

### 4.4. Publish Flag
Nhiều bảng có trường `publish` để quản lý trạng thái hiển thị:
- `1` hoặc `2`: Active/Published
- `0`: Inactive/Unpublished

---

## 5. TÓM TẮT

Database của hệ thống được thiết kế với:
- **30+ bảng** được tổ chức theo nhóm chức năng
- **Quan hệ phức tạp**: One-to-Many, Many-to-Many, Polymorphic
- **Hỗ trợ đa ngôn ngữ** qua các pivot tables với `languages`
- **Soft Deletes** để bảo toàn dữ liệu
- **JSON Storage** cho dữ liệu linh hoạt
- **Nested Set Model** cho danh mục đa cấp
- **Optional Foreign Keys** để hỗ trợ các trường hợp đặc biệt (khách vãng lai, bảo hành offline)

Thiết kế này đảm bảo tính linh hoạt, mở rộng và dễ bảo trì của hệ thống.

