<?php
// FILE: app/Models/NewsletterSubscription.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscription extends Model
{
    protected $fillable = ['email', 'name', 'token', 'is_active', 'confirmed_at', 'unsubscribed_at'];

    protected $casts = [
        'is_active' => 'boolean',
        'confirmed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->token)) {
                $model->token = Str::random(64);
            }
        });
    }
}
