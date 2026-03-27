<?php
// FILE: app/Models/PrinterConfig.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrinterConfig extends Model
{
    protected $fillable = [
        'name', 'type', 'host', 'port', 'usb_device',
        'paper_width', 'auto_print', 'is_active',
    ];

    protected $casts = [
        'auto_print' => 'boolean',
        'is_active' => 'boolean',
    ];
}
