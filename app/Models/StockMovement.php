<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['product_id', 'user_id', 'type', 'quantity', 'stock_after', 'reference_type', 'reference_id'];

    protected function casts(): array
    {
        return ['type' => StockMovementType::class, 'quantity' => 'integer', 'stock_after' => 'integer', 'created_at' => 'datetime'];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
