<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes;

    protected $fillable = ['reference_no', 'supplier_id', 'user_id', 'purchase_date', 'total_amount', 'paid_amount', 'due_amount', 'note'];

    protected function casts(): array
    {
        return ['purchase_date' => 'date', 'total_amount' => 'decimal:2', 'paid_amount' => 'decimal:2', 'due_amount' => 'decimal:2'];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }
}
