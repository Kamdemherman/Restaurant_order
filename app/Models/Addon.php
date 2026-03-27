<?php
// FILE: app/Models/Addon.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    protected $fillable = ['menu_item_id', 'name', 'price', 'is_available'];
    protected $casts = ['is_available' => 'boolean', 'price' => 'decimal:2'];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
