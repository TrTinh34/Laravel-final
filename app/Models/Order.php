<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'status',
        'total_amount',
        'notes',
    ];

    // Nhãn hiển thị cho từng trạng thái
    public static array $statuses = [
        'pending'    => 'Chờ xác nhận',
        'processing' => 'Đang xử lý',
        'shipping'   => 'Đang giao hàng',
        'completed'  => 'Hoàn thành',
        'cancelled'  => 'Đã hủy',
    ];

    // Màu badge cho từng trạng thái
    public static array $statusColors = [
        'pending'    => 'warning',
        'processing' => 'info',
        'shipping'   => 'primary',
        'completed'  => 'success',
        'cancelled'  => 'danger',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::$statuses[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::$statusColors[$this->status] ?? 'secondary';
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}