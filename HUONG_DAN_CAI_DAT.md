# HƯỚNG DẪN CÀI ĐẶT HỆ THỐNG VPHOME

Tài liệu hướng dẫn cài đặt thư viện (dependencies), cấu hình môi trường và load dữ liệu sản phẩm cho dự án.

---

## Mục lục

1. [Yêu cầu hệ thống](#1-yêu-cầu-hệ-thống)
2. [Cài đặt thư viện PHP (Composer)](#2-cài-đặt-thư-viện-php-composer)
3. [Cài đặt thư viện Frontend (npm)](#3-cài-đặt-thư-viện-frontend-npm)
4. [Cấu hình môi trường](#4-cấu-hình-môi-trường)
5. [Chạy migration và khởi tạo database](#5-chạy-migration-và-khởi-tạo-database)
6. [Load dữ liệu (Seeder & Scripts)](#6-load-dữ-liệu-seeder--scripts)
7. [Load dữ liệu sản phẩm](#7-load-dữ-liệu-sản-phẩm)
8. [Chạy ứng dụng](#8-chạy-ứng-dụng)
9. [Các thư viện chính của hệ thống](#9-các-thư-viện-chính-của-hệ-thống)

---

## 1. Yêu cầu hệ thống

| Thành phần | Phiên bản tối thiểu |
|------------|---------------------|
| **PHP** | 8.1 trở lên |
| **Composer** | 2.x |
| **Node.js** | 16.x trở lên (cho Vite/build frontend) |
| **npm** hoặc **yarn** | Tuỳ theo package manager |
| **MySQL** hoặc **MariaDB** | 5.7+ / 10.3+ |
| **Extension PHP** | BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, cURL |

Kiểm tra PHP:
```bash
php -v
php -m  # liệt kê extension
```

---

## 2. Cài đặt thư viện PHP (Composer)

### 2.1. Cài đặt dependencies

Tại thư mục gốc dự án:

```bash
composer install
```

- Môi trường **production**: dùng `composer install --no-dev` (bỏ gói dev).
- Lần đầu clone project: Composer sẽ tạo thư mục `vendor/` và file `composer.lock` (nếu chưa có).

### 2.2. Cập nhật thư viện

```bash
composer update
```

Chỉ cập nhật một package:

```bash
composer update laravel/framework
```

### 2.3. Tự động load sau khi cài

Sau `composer install`/`update`, Laravel thường chạy:

- `php artisan package:discover` (đăng ký service provider)
- Nếu chưa có file `.env`, nên copy từ `.env.example` và tạo `APP_KEY` (xem mục 4).

---

## 3. Cài đặt thư viện Frontend (npm)

### 3.1. Cài đặt dependencies

```bash
npm install
```

Tạo thư mục `node_modules/` và file `package-lock.json` (hoặc `yarn.lock` nếu dùng yarn).

### 3.2. Build assets (CSS/JS)

**Development** (chạy server Vite, hỗ trợ hot reload):

```bash
npm run dev
```

**Production** (build ra file tĩnh):

```bash
npm run build
```

File build nằm trong `public/build/` và được gọi trong view qua `@vite` hoặc `mix()`.

### 3.3. Dùng Yarn (tuỳ chọn)

```bash
yarn install
yarn dev
yarn build
```

---

## 4. Cấu hình môi trường

### 4.1. Tạo file .env

```bash
copy .env.example .env
```

(Linux/macOS: `cp .env.example .env`)

### 4.2. Sinh Application Key

```bash
php artisan key:generate
```

### 4.3. Cấu hình database

Mở `.env` và sửa:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vphome          # Tên database đã tạo
DB_USERNAME=root
DB_PASSWORD=                  # Mật khẩu MySQL (để trống nếu local không đặt)
```

**Lưu ý:** Tạo sẵn database (ví dụ `vphome`) trong MySQL/MariaDB trước khi chạy migration.

### 4.4. Cấu hình ứng dụng (tuỳ môi trường)

```env
APP_NAME="VPHOME"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
```

---

## 5. Chạy migration và khởi tạo database

### 5.1. Chạy migration

```bash
php artisan migrate
```

Tạo toàn bộ bảng theo thứ tự trong `database/migrations/`.

### 5.2. Reset và chạy lại migration (cẩn thận – xoá dữ liệu)

```bash
php artisan migrate:fresh
```

### 5.3. Vừa reset vừa chạy seeder

```bash
php artisan migrate:fresh --seed
```

Seeder được gọi theo `DatabaseSeeder` (xem mục 6).

---

## 6. Load dữ liệu (Seeder & Scripts)

Hệ thống có hai cách load dữ liệu:

- **Artisan Seeder**: `php artisan db:seed` (hoặc `--class=TênSeeder`).
- **Script PHP độc lập**: chạy trực tiếp `php ten_file.php`.

### 6.1. Tạo tài khoản Admin

**Cách 1 – Script (khuyến nghị cho lần đầu):**

```bash
php create_admin_user.php
```

- Tạo hoặc cập nhật UserCatalogue "Admin" (id = 1).
- Tạo/reset user admin:
  - **Email:** `admin@vphome.com`
  - **Password:** `password`

**Cách 2 – Seeder:**  
Nếu dùng `UserSeeder`, cần có `UserFactory`. Hiện `UserSeeder` tạo rất nhiều user; có thể sửa trong `database/seeders/UserSeeder.php` (ví dụ `count(1)`) hoặc tạo admin thủ công, rồi dùng script trên cho nhanh.

### 6.2. Chạy toàn bộ Seeder

```bash
php artisan db:seed
```

Thứ tự gọi trong `DatabaseSeeder`:

1. `UserSeeder` (user)
2. `LanguageSeeder` (ngôn ngữ)
3. `ProductCatalogueSeeder` (danh mục sản phẩm)
4. `ProductSeeder` (sản phẩm mẫu)

**Lưu ý:** `UserSeeder` mặc định tạo rất nhiều user (cấu hình trong `UserSeeder.php`). Nếu chỉ cần vài user để test, có thể sửa `UserSeeder.php` thành `User::factory()->count(1)->create()` hoặc dùng cách trong [6.1](#61-tạo-tài-khoản-admin): chạy `php create_admin_user.php` sau `migrate`, rồi chạy lần lượt `LanguageSeeder`, `ProductCatalogueSeeder`, `ProductSeeder` (xem [7.3](#73-thứ-tự-khuyến-nghị-database-trắng)).

### 6.3. Chạy từng Seeder

```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=LanguageSeeder
php artisan db:seed --class=ProductCatalogueSeeder
php artisan db:seed --class=ProductSeeder
```

Chạy theo đúng thứ tự trên nếu database mới và chưa có language/catalogue.

### 6.4. Tạo đơn hàng mẫu (sau khi đã có sản phẩm và khách hàng)

```bash
php create_fake_orders.php
```

- Cần có: ít nhất một **Customer** và một **Product** (publish = 2).
- Script tạo đơn hàng fake trong khoảng thời gian cấu hình sẵn trong file.

---

## 7. Load dữ liệu sản phẩm

### 7.1. Điều kiện trước khi chạy ProductSeeder

Cần tồn tại trong database:

- Ít nhất **1 User** (ví dụ admin từ `create_admin_user.php`).
- Ít nhất **1 Language** (ví dụ từ `LanguageSeeder`).
- Ít nhất **1 ProductCatalogue** và bản ghi tương ứng trong `product_catalogue_language` (ví dụ từ `ProductCatalogueSeeder`).

### 7.2. Chạy ProductSeeder

```bash
php artisan db:seed --class=ProductSeeder
```

ProductSeeder sẽ:

- Dùng User id = 1, Language id = 1, ProductCatalogue id = 1.
- Tạo một số sản phẩm mẫu với: tên, canonical, mô tả, giá, mã, v.v.
- Ghi vào `product_language` và `product_catalogue_product`.
- Nếu thiếu User/Language/ProductCatalogue, seeder in thông báo và không tạo sản phẩm.

### 7.3. Thứ tự khuyến nghị (database trắng)

```bash
# 1. Migration
php artisan migrate

# 2. Admin + dữ liệu nền (language, catalogue)
php create_admin_user.php
php artisan db:seed --class=LanguageSeeder
php artisan db:seed --class=ProductCatalogueSeeder

# 3. Sản phẩm mẫu
php artisan db:seed --class=ProductSeeder

# 4. (Tuỳ chọn) Đơn hàng fake
php create_fake_orders.php
```

### 7.4. Load sản phẩm qua giao diện Admin

1. Đăng nhập: **Email** `admin@vphome.com`, **Password** `password`.
2. Vào **QL Sản phẩm** → **Thêm mới**.
3. Điền thông tin, chọn danh mục, ngôn ngữ, ảnh, giá, mã, v.v.
4. Lưu → sản phẩm được ghi vào `products`, `product_language`, `product_catalogue_product`.

Có thể nhập nhiều sản phẩm thủ công hoặc dùng ProductSeeder để có sẵn dữ liệu mẫu.

### 7.5. Import sản phẩm từ file (nếu có tính năng)

Nếu sau này có chức năng import Excel/CSV, thường nằm ở:

- Controller: `app/Http/Controllers/Backend/ProductController.php` (hoặc ProductImportController).
- Route: nhóm `backend`, ví dụ `/product/import`.

Kiểm tra route:

```bash
php artisan route:list --name=product
```

---

## 8. Chạy ứng dụng

### 8.1. Server development Laravel

```bash
php artisan serve
```

Mặc định: `http://127.0.0.1:8000`.

### 8.2. Kết hợp Vite (frontend)

Mở 2 terminal:

**Terminal 1:**
```bash
php artisan serve
```

**Terminal 2:**
```bash
npm run dev
```

### 8.3. Production

- Cấu hình web server (Apache/Nginx) trỏ document root tới `public/`.
- Đảm bảo đã chạy `composer install --no-dev`, `npm run build`, và cấu hình `.env` (APP_DEBUG=false, APP_ENV=production).

---

## 9. Các thư viện chính của hệ thống

### 9.1. PHP (Composer) – trích từ `composer.json`

| Package | Mục đích |
|--------|----------|
| `laravel/framework` ^10.10 | Framework Laravel |
| `mindscms/laravelshoppingcart` ^2.1 | Giỏ hàng (Cart) |
| `ramsey/uuid` ^4.7 | Tạo UUID (biến thể sản phẩm, v.v.) |
| `intervention/image` ^3.2 | Xử lý ảnh (resize, crop) |
| `paypal/rest-api-sdk-php` ^1.6 | PayPal REST API |
| `srmklive/paypal` ^3.0 | Laravel PayPal |
| `guzzlehttp/guzzle` ^7.2 | HTTP client (gọi API MoMo, Paypal, v.v.) |
| `simplesoftwareio/simple-qrcode` ^4.2 | Tạo mã QR |
| `yoeunes/toastr` ^2.3 | Thông báo dạng toast trên giao diện |
| `barryvdh/laravel-debugbar` ^3.8 | Debug bar (dev) |
| `predis/predis` ^2.2 | Redis (cache/session nếu dùng Redis) |
| `symfony/dom-crawler` | Crawler / parse HTML (nếu dùng) |
| `laravel/sanctum` ^3.2 | API authentication |
| `fakerphp/faker` ^1.9.1 | Dữ liệu giả cho Factory/Seeder (dev) |

### 9.2. Frontend (npm) – trích từ `package.json`

| Package | Mục đích |
|--------|----------|
| `vite` ^4.0 | Build frontend |
| `laravel-vite-plugin` ^0.7.5 | Tích hợp Vite với Laravel |
| `axios` ^1.1.2 | Gọi API từ frontend |
| `laravel-mix` ^6.0.49 | Build (nếu vẫn dùng Mix) |
| `laravel-echo` ^1.15.3 | Real-time (WebSocket) |
| `socket.io-client` ^2.4.0 | Socket client cho Echo |

---

## Tóm tắt lệnh thường dùng

```bash
# Cài đặt
composer install
npm install

# Môi trường
copy .env.example .env
php artisan key:generate

# Database
php artisan migrate
php create_admin_user.php
php artisan db:seed --class=LanguageSeeder
php artisan db:seed --class=ProductCatalogueSeeder
php artisan db:seed --class=ProductSeeder

# (Tuỳ chọn)
php create_fake_orders.php

# Chạy
php artisan serve
npm run dev
```

Sau khi làm đủ các bước trên, hệ thống có thể truy cập qua `http://127.0.0.1:8000`, đăng nhập admin và xem sản phẩm mẫu đã được load bởi ProductSeeder.
