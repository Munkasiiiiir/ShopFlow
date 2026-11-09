<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\SaleStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = ['invoice_no', 'customer_id', 'user_id', 'sale_date', 'subtotal', 'discount', 'tax', 'grand_total', 'paid_amount', 'change_amount', 'payment_method', 'status'];

    protected function casts(): array
    {
        return ['sale_date' => 'datetime', 'subtotal' => 'decimal:2', 'discount' => 'decimal:2', 'tax' => 'decimal:2', 'grand_total' => 'decimal:2', 'paid_amount' => 'decimal:2', 'change_amount' => 'decimal:2', 'payment_method' => PaymentMethod::class, 'status' => SaleStatus::class];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function returns()
    {
        return $this->hasMany(SaleReturn::class);
    }
}
