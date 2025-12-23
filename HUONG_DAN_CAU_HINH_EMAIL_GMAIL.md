# 📧 HƯỚNG DẪN CẤU HÌNH GỬI EMAIL QUA GMAIL

## 🔧 BƯỚC 1: TẠO APP PASSWORD CHO GMAIL

### A. Bật xác thực 2 bước (2-Step Verification)
1. Truy cập: https://myaccount.google.com/security
2. Tìm mục **"2-Step Verification"** → Bật ON
3. Làm theo hướng dẫn để kích hoạt

### B. Tạo App Password
1. Truy cập: https://myaccount.google.com/apppasswords
2. Chọn **"Mail"** và **"Other (Custom name)"**
3. Nhập tên: `Laravel VPHome`
4. Click **"Generate"**
5. **Copy mật khẩu 16 ký tự** (ví dụ: `abcd efgh ijkl mnop`)
   - ⚠️ **LƯU Ý:** Chỉ hiển thị 1 lần, hãy copy ngay!

---

## 📝 BƯỚC 2: CẤU HÌNH TRONG FILE .ENV

Mở file `.env` trong thư mục gốc dự án và thêm/sửa các dòng sau:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=abcd efgh ijkl mnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="VPHome"
```

### 📌 LƯU Ý:
- **MAIL_USERNAME:** Email Gmail của bạn (ví dụ: `vphome@gmail.com`)
- **MAIL_PASSWORD:** App Password 16 ký tự (bỏ khoảng trắng, viết liền: `abcdefghijklmnop`)
- **MAIL_FROM_ADDRESS:** Có thể dùng email khác, nhưng nên dùng cùng email với MAIL_USERNAME
- **MAIL_FROM_NAME:** Tên hiển thị khi gửi email

---

## 🔄 BƯỚC 3: CLEAR CACHE

Sau khi sửa `.env`, chạy lệnh:

```bash
php artisan config:clear
php artisan cache:clear
```

---

## ✅ BƯỚC 4: TEST GỬI EMAIL

### Cách 1: Test bằng Tinker
```bash
php artisan tinker
```

Sau đó chạy:
```php
Mail::raw('Test email từ Laravel', function($message) {
    $message->to('your_test_email@gmail.com')
            ->subject('Test Email VPHome');
});
```

### Cách 2: Đặt hàng thử
1. Vào trang thanh toán
2. Điền form với email thật của bạn
3. Đặt hàng
4. Kiểm tra hộp thư email

---

## ⚠️ LƯU Ý QUAN TRỌNG

1. **App Password:** KHÔNG dùng mật khẩu Gmail thông thường, phải dùng App Password
2. **Bỏ khoảng trắng:** App Password có dạng `abcd efgh ijkl mnop` → viết liền: `abcdefghijklmnop`
3. **Giới hạn Gmail:** Gmail có giới hạn 500 email/ngày cho tài khoản miễn phí
4. **Bảo mật:** KHÔNG commit file `.env` lên Git!

---

## 🐛 XỬ LÝ LỖI

### Lỗi: "Authentication failed"
- Kiểm tra App Password đã copy đúng chưa
- Đảm bảo đã bật 2-Step Verification
- Thử tạo App Password mới

### Lỗi: "Connection timeout"
- Kiểm tra firewall/antivirus có chặn port 587 không
- Thử đổi port sang 465 và encryption sang `ssl`

### Email không đến
- Kiểm tra thư mục Spam
- Kiểm tra log: `storage/logs/laravel.log`
- Test với email khác

---

## 📊 KIỂM TRA LOG

Xem log email:
```bash
tail -f storage/logs/laravel.log | grep -i mail
```

Hoặc xem toàn bộ log:
```bash
tail -f storage/logs/laravel.log
```

---

## 🎯 VÍ DỤ CẤU HÌNH HOÀN CHỈNH

```env
# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=vphome.shop@gmail.com
MAIL_PASSWORD=abcdefghijklmnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=vphome.shop@gmail.com
MAIL_FROM_NAME="VPHome - Cửa hàng điện tử"
```

---

**Chúc bạn cấu hình thành công! 🎉**

