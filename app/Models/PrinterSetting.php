<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrinterSetting extends Model
{
    protected $fillable = ['business_id', 'printer_type', 'print_type', 'receipt_size', 'logo_position', 'footer_message', 'signature_image', 'terms_conditions', 'show_qr', 'show_signature', 'show_stamp', 'show_phone', 'show_address', 'show_email', 'show_party_balance'];

    protected $casts = ['show_qr' => 'boolean', 'show_signature' => 'boolean', 'show_stamp' => 'boolean', 'show_phone' => 'boolean', 'show_address' => 'boolean', 'show_email' => 'boolean', 'show_party_balance' => 'boolean'];

    public function business() { return $this->belongsTo(Business::class); }
}
