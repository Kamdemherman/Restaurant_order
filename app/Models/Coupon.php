<?php
// FILE: app/Models/Coupon.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_order_amount', 'max_discount_amount',
        'is_single_use', 'is_used', 'used_by', 'used_at',
        'expires_at', 'is_active', 'notes',
    ];

    protected $casts = [
        'is_single_use' => 'boolean',
        'is_used' => 'boolean',
        'is_active' => 'boolean',
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    public function isValid(float $orderAmount = 0): bool
    {
        if (!$this->is_active) return false;
        if ($this->is_single_use && $this->is_used) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($orderAmount < $this->min_order_amount) return false;
        return true;
    }

    public function calculateDiscount(float $orderAmount): float
    {
        if ($this->type === 'percentage') {
            $discount = $orderAmount * ($this->value / 100);
            if ($this->max_discount_amount) {
                $discount = min($discount, $this->max_discount_amount);
            }
        } else {
            $discount = min($this->value, $orderAmount);
        }
        return round($discount, 2);
    }
}
