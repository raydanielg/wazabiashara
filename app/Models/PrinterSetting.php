<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrinterSetting extends Model
{
    protected $fillable = ['business_id', 'printer_type', 'receipt_size', 'logo_position', 'footer_message', 'show_qr', 'show_signature', 'show_stamp'];

    protected $casts = ['show_qr' => 'boolean', 'show_signature' => 'boolean', 'show_stamp' => 'boolean'];

    public function business() { return $this->belongsTo(Business::class); }
}
