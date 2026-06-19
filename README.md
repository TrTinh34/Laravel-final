# TailAdmin Laravel Starter Kit

This starter kit is based on [TailAdmin Laravel - Free Laravel Dashboard](https://github.com/TailAdmin/tailadmin-laravel).

We decided to merge our [LaravelDaily/starter-kit](https://github.com/LaravelDaily/starter-kit) with TailAdmin components.

As a result, you get full **simple** Laravel Auth (*login, register, forget password, profile*), styled as TailAdmin.

![](https://laraveldaily.com/uploads/2025/11/tailadmin-starter-kit-profile.png)

![](https://laraveldaily.com/uploads/2025/11/tailadmin-starter-kit-login.png)

The main point is no React/Vue/Livewire required. Only Blade and Tailwind.

Also, you're getting an example table/form with two-level menu on the sidebar to manage Users.

![](https://laraveldaily.com/uploads/2025/12/tailadmin-starter-kit-users-list.png)

![](https://laraveldaily.com/uploads/2025/12/tailadmin-starter-kit-user-edit.png)

---

## How to use

To use this kit, you can install it using:

```
laravel new --using=laraveldaily/tailadmin-starter-kit
```

From there, you can modify the kit to your needs and add more pages.

For more components, TailAdmin theme also has a [Pro version](https://checkout.tailadmin.com/buy/ed68b4bb-f0c6-4d20-a241-d3a5a81b0f25?aff=EEK4LN) (*affiliate link to support my work*) with 500+ components and dashboard variants.
"# Laravel-final" 



run rồi chạy
lệnh expose:  expose share http://localhost:8000


url expose hết hạn thì thay đổi lại:
APP_URL= (url expose vừa sinh ra) trong .env
VNPAY_RETURN_URL= (url expose vừa sinh ra) trong .env



Fix — chạy lại đủ 3 lệnh này:
Terminal 1 — Laravel server:
bashphp artisan serve
Terminal 2 — Queue worker (để gửi mail):
bashphp artisan queue:work
Terminal 3 — Expose tunnel:
bashexpose share http://127.0.0.1:8000

Sau khi chạy expose share, nhớ cập nhật URL mới vào .env rồi chạy:
bashphp artisan config:clear




-Chức năng thanh toán trực tuyến:

Bước 1 — Khách nhấn "Thanh toán"
    Web của bạn tạo một link VNPay kèm thông tin: số tiền, mã đơn hàng, chữ ký bảo mật
    Redirect khách sang trang VNPay

Bước 2 — Khách thanh toán trên trang VNPay
    Khách nhập thông tin thẻ, xác nhận OTP
    VNPay xử lý giao dịch

Bước 3 — VNPay redirect khách về web của bạn
    Dù thành công hay thất bại đều về 1 URL duy nhất: /vnpay/return
    Kèm theo các thông tin: mã đơn, số tiền, mã kết quả (vnp_ResponseCode)

Bước 4 — Web của bạn kiểm tra kết quả
    vnp_ResponseCode == '00'  →  Thành công → cập nhật đơn hàng → gửi mail
    vnp_ResponseCode != '00'  →  Thất bại   → thông báo thất bại

Bước 5 — VNPay gọi ngầm IPN về /vnpay/ipn
    Đây là bước VNPay tự gọi về server của bạn không qua trình duyệt
    Dùng để đảm bảo đơn hàng được cập nhật kể cả khi khách tắt trình duyệt ở bước 3






-Chức năng gửi mail:

Bước 1 — Có sự kiện xảy ra
    Khách thanh toán thành công → status đơn chuyển thành processing
    Admin cập nhật đơn → status chuyển thành shipping, completed, cancelled

Bước 2 — Laravel gọi Notification
    phpNotification::route('mail', $order->customer_email)
        ->notify(new OrderStatusChanged($order));
    Tạo một "thông báo" kèm thông tin đơn hàng
    Chỉ định gửi qua kênh mail

Bước 3 — Notification đẩy vào Queue
    Vì có ShouldQueue nên không gửi ngay
    Đẩy vào hàng chờ (database queue) rồi trả về response ngay cho người dùng
    Queue worker (php artisan queue:work) chạy ngầm, lấy job ra xử lý

Bước 4 — Laravel kết nối Gmail SMTP
    Dùng thông tin trong .env (host, port, username, password)
    Gửi email đến địa chỉ của khách

Bước 5 — Khách nhận được mail
    Nội dung mail tùy theo status đơn hàng
    processing → thanh toán thành công
    shipping → đang giao hàng
    completed → giao thành công
    cancelled → đơn bị hủy