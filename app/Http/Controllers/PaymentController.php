<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderStatusChanged;
class PaymentController extends Controller
{
    private $vnp_TmnCode;
    private $vnp_HashSecret;
    private $vnp_Url;

    public function __construct()
    {
        $this->vnp_TmnCode    = env('VNPAY_TMN_CODE');
        $this->vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $this->vnp_Url        = env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
    }

    
    public function createPaymentLink($orderId)
    {
        $order = Order::findOrFail($orderId);
        // tạo link thanh toán chỉ cho đơn hàng đang ở trạng thái "pending"
        if ($order->status !== 'pending') {
            return back()->with('error', 'Đơn hàng này không ở trạng thái chờ thanh toán.');
        }

        $vnp_TxnRef = $order->id;
        $vnp_OrderInfo = 'Thanh toan don hang #' . $order->id;
        $vnp_OrderType = 'other';
        $vnp_Amount = intval($order->total_amount) * 100; 
        $vnp_Locale = 'vi';
        $vnp_IpAddr = request()->ip();

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $this->vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => env('VNPAY_RETURN_URL'),
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        // CHUẨN HÓA CHỮ KÝ VNPAY 2.1.0 (Sửa lỗi sai chữ ký 70)
        $hashdata = str_replace(['+', '%20'], ['%20', '+'], $hashdata);
        $query = str_replace(['+', '%20'], ['%20', '+'], $query);

        $vnp_Url = $this->vnp_Url . "?" . $query;
        if (isset($this->vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return redirect()->to($vnp_Url);
    }

    /**
     * Xử lý trả kết quả về (Khắc phục hoàn toàn lỗi quay vòng CORS)
     */
    public function vnpayReturn(Request $request)
    {
        $vnp_SecureHash = $request->get('vnp_SecureHash');
        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == 'vnp_') {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        // CHUẨN HÓA CHỮ KÝ KHI NHẬN VỀ
        $hashdata = str_replace(['+', '%20'], ['%20', '+'], $hashdata);
        $secureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);

        if ($secureHash === $vnp_SecureHash) {
            if ($request->get('vnp_ResponseCode') == '00') {
                $orderId = $request->get('vnp_TxnRef');
                $order = Order::find($orderId);

                if ($order && $order->status === 'pending') {
                    $order->update(['status' => 'processing']);

                    Notification::route('mail', $order->customer_email)
                        ->notify(new OrderStatusChanged($order));
                }

                // KHẮC PHỤC LỖI XOAY VÒNG: Trả về trang thông báo thuần HTML nhẹ, không nạp Vite lỗi
                return response('
                    <!DOCTYPE html>
                    <html>
                    <head><title>Thanh toán thành công</title><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
                    <body style="font-family:sans-serif; text-align:center; padding:50px; background:#f4f6f9;">
                        <div style="background:white; max-width:500px; margin:0 auto; padding:30px; border-radius:8px; box-shadow:0 4px 6px rgba(0,0,0,0.1);">
                            <h2 style="color:#28a745;">✓ Thanh toán thành công!</h2>
                            <p>Đơn hàng #'.$orderId.' đã được duyệt tự động thành công.</p>
                            <a href="/" style="display:inline-block; margin-top:20px; padding:10px 20px; background:#007bff; color:white; text-decoration:none; border-radius:5px;">Quay lại trang chủ</a>
                        </div>
                    </body>
                    </html>
                ');
            }
        }

        return response('<h2>Thanh toán thất bại hoặc đã bị hủy bỏ.</h2><a href="/">Quay lại</a>', 400);
    }

    /**
     * Nhận kết quả IPN (Webhook ngầm)
     */
    public function handleWebhook(Request $request)
    {
        $vnp_SecureHash = $request->get('vnp_SecureHash');
        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == 'vnp_') {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $hashdata = str_replace(['+', '%20'], ['%20', '+'], $hashdata);
        $secureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);

        if ($secureHash === $vnp_SecureHash) {
            $orderId = $request->get('vnp_TxnRef');
            $vnp_Amount = $request->get('vnp_Amount');

            $order = Order::find($orderId);

            if (!$order) {
                return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
            }

            if (($order->total_amount * 100) != $vnp_Amount) {
                return response()->json(['RspCode' => '04', 'Message' => 'Invalid amount']);
            }

            if ($order->status !== 'pending') {
                return response()->json(['RspCode' => '02', 'Message' => 'Order already confirmed']);
            }

            if ($request->get('vnp_ResponseCode') == '00' && $request->get('vnp_TransactionStatus') == '00') {
                $order->update(['status' => 'processing']);
            }

            return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
        }

        return response()->json(['RspCode' => '97', 'Message' => 'Invalid Signature']);
    }
}