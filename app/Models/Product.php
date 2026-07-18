<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['business_id', 'category_id', 'name', 'brand', 'barcode', 'sku', 'image', 'unit', 'cost_price', 'selling_price', 'wholesale_price', 'reorder_level', 'expiry_date', 'supplier_id', 'location', 'allow_decimal', 'tax_rate', 'status'];

    protected $casts = ['expiry_date' => 'date'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function branchStock()
    {
        return $this->hasMany(BranchStock::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockForBranch($branchId)
    {
        return $this->branchStock()->where('branch_id', $branchId)->first();
    }
}
