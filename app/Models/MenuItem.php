<?php
// FILE: app/Models/MenuItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MenuItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price',
        'image', 'is_available', 'is_featured', 'allergens',
        'preparation_time', 'sort_order',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'allergens' => 'array',
        'price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function addons()
    {
        return $this->hasMany(Addon::class);
    }

    public function availableAddons()
    {
        return $this->hasMany(Addon::class)->where('is_available', true);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
