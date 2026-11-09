<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = ['sale_id', 'product_id', 'quantity', 'unit_price', 'cost_price', 'subtotal', 'returned_qty'];

    protected function casts(): array
    {
        return ['quantity' => 'integer', 'returned_qty' => 'integer', 'unit_price' => 'decimal:2', 'cost_price' => 'decimal:2', 'subtotal' => 'decimal:2'];
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function returnItems()
    {
        return $this->hasMany(SaleReturnItem::class);
    }

    public function returnableQty(): int
    {
        return $this->quantity - $this->returned_qty;
    }
}
