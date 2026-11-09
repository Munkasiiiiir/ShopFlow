<?php

namespace App\Models;

use App\Enums\SupplierPaymentMethod;
use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    protected $fillable = ['supplier_id', 'purchase_id', 'user_id', 'amount', 'payment_date', 'payment_method', 'note'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'payment_date' => 'date', 'payment_method' => SupplierPaymentMethod::class];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
