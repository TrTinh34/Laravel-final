<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    /**
     * Khởi tạo Notification nhận vào đối tượng Order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Xác định kênh gửi thông báo (Ở đây chỉ dùng mail)
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Xây dựng nội dung Email dựa trên từng trạng thái
     */
    public function toMail(object $notifiable): MailMessage
    {
        $email = (new MailMessage)
            ->subject('Cập nhật trạng thái đơn hàng #' . $this->order->order_code)
            ->greeting('Xin chào ' . $this->order->customer_name . ',');

        // Tùy biến nội dung Email theo từng trạng thái đơn hàng
        switch ($this->order->status) {
            
            case 'shipping':
                $email->line('Đơn hàng của bạn đã được đóng gói cẩn thận và bàn giao cho đơn vị vận chuyển.')
                    ->line('Hệ thống đang giao hàng đến địa chỉ của bạn.')
                    ->line('Vui lòng chú ý điện thoại để shipper có thể liên lạc tiện nhất nhé!');
                break;

            case 'completed':
                $email->line('Đơn hàng #' . $this->order->order_code . ' đã được giao thành công!')
                    ->line('Cảm ơn bạn rất nhiều vì đã tin tưởng và mua sắm tại cửa hàng của chúng tôi.')
                    ->line('Nếu bạn hài lòng với sản phẩm, hãy dành chút thời gian đánh giá để giúp shop hoàn thiện hơn nhé.');
                break;

            case 'cancelled':
                $email->line('Chúng tôi rất tiếc phải thông báo rằng đơn hàng #' . $this->order->order_code . ' đã bị hủy trên hệ thống.')
                    ->line('Nếu có bất kỳ thắc mắc nào hoặc đây là sự nhầm lẫn, vui lòng liên hệ ngay với hotline của chúng tôi để được hỗ trợ giải quyết nhanh nhất.');
                break;
                // OrderStatusChanged.php
            case 'processing':
                $email->line('Đơn hàng #' . $this->order->order_code . ' đã được thanh toán thành công!')
                    ->line('Chúng tôi đang xử lý đơn hàng và sẽ sớm giao hàng đến bạn.')
                    ->line('Bạn sẽ nhận được email tiếp theo khi đơn hàng được chuyển cho đơn vị vận chuyển.');
                break;
        }

        // Khối thông tin chung hiển thị ở cuối Email
        return $email
            ->line('Tổng giá trị đơn hàng: ' . number_format($this->order->total_amount, 0, ',', '.') . 'đ')
            ->action('Xem chi tiết đơn hàng', url('/orders/' . $this->order->id)) // Đường dẫn tới trang đơn hàng của khách
            ->line('Cảm ơn bạn đã đồng hành cùng chúng tôi!');
    }
}