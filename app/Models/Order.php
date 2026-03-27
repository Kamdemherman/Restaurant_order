<?php
// FILE: app/Models/Order.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number', 'user_id', 'coupon_id', 'status',
        'subtotal', 'discount_amount', 'total',
        'delivery_address', 'delivery_lat', 'delivery_lng',
        'notes', 'payment_method', 'payment_status',
        'is_printed', 'printed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'delivery_lat' => 'decimal:8',
        'delivery_lng' => 'decimal:8',
        'is_printed' => 'boolean',
        'printed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->order_number)) {
                $model->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'warning',
            'confirmed' => 'info',
            'preparing' => 'primary',
            'ready'     => 'success',
            'delivered' => 'secondary',
            'cancelled' => 'danger',
            default     => 'light',
        };
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
