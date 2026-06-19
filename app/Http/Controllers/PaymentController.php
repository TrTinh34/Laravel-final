<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class PaymentController extends Controller
{
    private $vnp_TmnCode;
    private $vnp_HashSecret;
    private $vnp_Url;

    public function __construct()
    {
        // Lấy thông tin cấu hình từ file .env
        $this->vnp_TmnCode    = env('VNPAY_TMN_CODE');
        $this->vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $this->vnp_Url        = env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
    }

    /**
     * Tạo Link thanh toán VNPAY bằng Code Thuần 100%
     */
    public function createPaymentLink($orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->status !== 'pending') {
            return back()->with('error', 'Đơn hàng này không ở trạng thái chờ thanh toán.');
        }

        $vnp_TxnRef = $order->id;
        $vnp_OrderInfo = 'Thanh toan don hang #' . $order->id;
        $vnp_OrderType = 'other';
        $vnp_Amount = intval($order->total_amount) * 100; // VNPAY yêu cầu nhân 100
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

        // Sắp xếp dữ liệu theo ksort giống như thuật toán của VNPAY yêu cầu
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

        $vnp_Url = $this->vnp_Url . "?" . $query;
        if (isset($this->vnp_HashSecret)) {
            // Tạo chữ ký bảo mật bằng HMAC-SHA512 chuẩn VNPAY
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return redirect()->to($vnp_Url);
    }

    /**
     * Xử lý hiển thị giao diện khi khách quay lại trang web
     */
    /**
 * Xử lý hiển thị giao diện khi khách quay lại trang web
 * ĐỒNG THỜI cập nhật trạng thái đơn hàng để phòng trường hợp không cấu hình được IPN Sandbox
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

    $secureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);

    // 1. Kiểm tra chữ ký hợp lệ
    if ($secureHash === $vnp_SecureHash) {
        // 2. Kiểm tra mã phản hồi thành công (00)
        if ($request->get('vnp_ResponseCode') == '00') {
            
            // Lấy ID đơn hàng từ tham số vnp_TxnRef
            $orderId = $request->get('vnp_TxnRef');
            $order = Order::find($orderId);

            // 3. Nếu đơn hàng tồn tại và đang ở trạng thái pending -> Cập nhật luôn sang processing
            if ($order && $order->status === 'pending') {
                $order->update(['status' => 'processing']);
            }

            return redirect()->route('payment.success')->with('success', 'Thanh toán thành công!');
        }
    }
    
    return redirect()->route('payment.cancel')->with('error', 'Thanh toán thất bại hoặc đã bị hủy.');
}

    /**
     * Nhận kết quả IPN (Webhook) ngầm từ VNPAY để duyệt đơn
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
            // Sử dụng vnp_Urlencode riêng để ép khoảng trắng thành %20 thay vì dấu +
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        // Bổ sung thêm hàm xử lý chuỗi mã hóa chuẩn RFC 3986 (biến + thành %20)
        $hashdata = str_replace(['+', '%20'], ['%20', '+'], $hashdata);
        $query = str_replace(['+', '%20'], ['%20', '+'], $query);

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
