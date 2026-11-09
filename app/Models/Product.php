<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = ['category_id', 'brand_id', 'unit_id', 'name', 'sku', 'cost_price', 'sale_price', 'stock', 'alert_qty', 'description', 'image', 'is_active'];

    protected function casts(): array
    {
        return ['cost_price' => 'decimal:2', 'sale_price' => 'decimal:2', 'stock' => 'integer', 'alert_qty' => 'integer', 'is_active' => 'boolean'];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->alert_qty;
    }

    public function stockStatus(): string
    {
        return $this->stock === 0 ? 'out_of_stock' : ($this->isLowStock() ? 'low_stock' : 'in_stock');
    }
}
