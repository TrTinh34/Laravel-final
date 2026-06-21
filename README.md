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
php artisan serve
Terminal 2 — Queue worker (để gửi mail):
php artisan queue:work
Terminal 3 — Expose tunnel:
expose share http://127.0.0.1:8000

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






-Chức năng gửi mail: laravel notification(hệ thống thông báo)

Khi admin cập nhật trạng thái đơn hàng trong OrderController, code kiểm tra xem status mới có thuộc nhóm cần gửi mail không (shipping, completed, cancelled). Nếu có thì gọi OrderStatusChanged và truyền vào đối tượng đơn hàng.
OrderStatusChanged nhận đơn hàng, đọc status của nó, rồi tùy theo status mà xây dựng nội dung email khác nhau — đang giao hàng thì một nội dung, hoàn thành thì nội dung khác, bị hủy thì nội dung khác nữa.
Sau đó Laravel tự kết nối đến Gmail SMTP bằng thông tin trong .env và gửi email đến địa chỉ của khách hàng.

-Queue

Cụ thể, Queue chia quy trình gửi Mail của bạn thành 2 giai đoạn tách biệt:

Giai đoạn 1: Đẩy nhiệm vụ vào Hàng đợi (Diễn ra tức thì)
Admin bấm nút cập nhật trạng thái đơn hàng.

OrderController kiểm tra trạng thái và kích hoạt OrderStatusChanged.

[VỊ TRÍ CỦA QUEUE]: Thay vì gửi email ngay lập tức, Laravel sẽ đóng gói toàn bộ thông tin đơn hàng cùng nội dung email đã được chuẩn bị thành một "Nhiệm vụ" (Job).

Laravel ném nhanh nhiệm vụ này vào Database hoặc Redis (nơi lưu trữ hàng đợi của bạn).

OrderController lập tức trả về thông báo cho Admin: "Cập nhật trạng thái thành công!". Toàn bộ quá trình này chỉ mất khoảng 0.01 giây. Admin không hề biết ngầm bên dưới email thực chất chưa được gửi đi.

Giai đoạn 2: Xử lý ngầm (Diễn ra bất đồng bộ ở nền)
Ở dưới nền máy tính/server, có một lệnh Worker (lệnh php artisan queue:work) đang chạy ngầm liên tục để "rình" hàng đợi.

Khi Worker thấy nhiệm vụ gửi mail do OrderStatusChanged ném vào, nó sẽ nhặt nhiệm vụ đó lên.

[VỊ TRÍ KẾT NỐI SMTP]: Worker lúc này mới chính là đứa thực hiện việc kết nối đến Gmail SMTP bằng thông tin trong .env và gửi email đến cho khách hàng.