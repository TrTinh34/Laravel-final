<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class PaymentController extends Controller
{
    private $clientId;
    private $apiKey;
    private $checksumKey;

    public function __construct()
    {
        // Lấy các thông tin cấu hình từ file .env
        $this->clientId    = env('PAYOS_CLIENT_ID');
        $this->apiKey      = env('PAYOS_API_KEY');
        $this->checksumKey = env('PAYOS_CHECKSUM_KEY');
    }

    /**
     * Tạo Link thanh toán VietQR gửi lên PayOS bằng cURL thuần PHP
     */
    /**
     * Hàm tạo Link thanh toán VietQR
     */
    public function createPaymentLink($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Đảm bảo đơn hàng ở trạng thái chờ thanh toán
        if ($order->status !== 'pending') {
            return back()->with('error', 'Đơn hàng này không ở trạng thái chờ thanh toán.');
        }

        $paymentData = [
            'orderCode'   => intval($order->id . time()), 
            'amount'      => intval($order->total_amount), // ĐÃ ĐỔI THÀNH total_amount theo DB của bạn
            'description' => 'Thanh toan don hang #' . $order->id,
            'returnUrl'   => route('payment.success'),    
            'cancelUrl'   => route('payment.cancel'),     
        ];

        // Tạo chữ ký bảo mật
        ksort($paymentData);
        $dataQueryStr = http_build_query($paymentData);
        $signature    = hash_hmac('sha256', $dataQueryStr, env('PAYOS_CHECKSUM_KEY'));
        $paymentData['signature'] = $signature;

        try {
            $ch = curl_init('https://api-merchant.payos.vn/v2/payment-requests');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paymentData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'x-client-id: ' . env('PAYOS_CLIENT_ID'),
                'x-api-key: ' . env('PAYOS_API_KEY'),
            ]);

            $result = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($result, true);

            if (isset($response['code']) && $response['code'] == '00') {
                return redirect($response['data']['checkoutUrl']);
            } else {
                return back()->with('error', 'PayOS từ chối: ' . ($response['desc'] ?? 'Lỗi không xác định'));
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi kết nối: ' . $e->getMessage());
        }
    }

    /**
     * Cổng nhận thông báo webhook tự động khi thanh toán xong
     */
    public function handleWebhook(Request $request)
    {
        if ($request->isMethod('get') || !$request->has('data')) {
            return response()->json(['success' => true, 'message' => 'Cổng Webhook thông suốt!']);
        }

        $body = $request->all();
        $data = $body['data'] ?? [];

        ksort($data);
        $dataQueryStr = http_build_query($data);
        $localSignature = hash_hmac('sha256', $dataQueryStr, env('PAYOS_CHECKSUM_KEY'));

        if ($localSignature === $body['signature']) {
            // ĐÃ CẬP NHẬT: Tìm đơn hàng theo ID (hoặc orderCode tuỳ cách bạn lưu) và đổi status thành completed hoặc processing
            // Vì PayOS orderCode gửi lên là số ($order->id . time()), ta cần bóc tách hoặc log lại. 
            // Giải pháp đơn giản nhất để test là lấy từ description: 'Thanh toan don hang #18'
            preg_match('/#(\d+)/', $data['description'], $matches);
            if (isset($matches[1])) {
                $order = Order::find($matches[1]);
                if ($order && $order->status === 'pending') {
                    $order->update(['status' => 'processing']); // Đổi sang trạng thái đang xử lý
                }
            }
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Sai chữ ký'], 400);
    }
}