<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    protected $fillable = ['return_no', 'sale_id', 'user_id', 'return_date', 'total_refund', 'reason'];

    protected function casts(): array
    {
        return ['return_date' => 'datetime', 'total_refund' => 'decimal:2'];
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleReturnItem::class);
    }
}
