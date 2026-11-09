<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\SupplierPayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Supplier payments against a purchase (FR-3.8, FR-3.9). Keeps paid/due in sync. */
class SupplierPaymentService
{
    /**
     * @param  array{amount:float, payment_date:string, payment_method:string, note?:string}  $data
     */
    public function recordPayment(Purchase $purchase, array $data, User $user): SupplierPayment
    {
        return DB::transaction(function () use ($purchase, $data, $user) {
            $locked = Purchase::whereKey($purchase->id)->lockForUpdate()->firstOrFail();

            if ($data['amount'] > (float) $locked->due_amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment cannot be more than the due amount ('.number_format((float) $locked->due_amount, 2).').',
                ]);
            }

            $payment = SupplierPayment::create([
                'supplier_id' => $locked->supplier_id,
                'purchase_id' => $locked->id,
                'user_id' => $user->id,
                'amount' => $data['amount'],
                'payment_date' => $data['payment_date'],
                'payment_method' => $data['payment_method'],
                'note' => $data['note'] ?? null,
            ]);

            $locked->update([
                'paid_amount' => (float) $locked->paid_amount + $data['amount'],
                'due_amount' => (float) $locked->due_amount - $data['amount'],
            ]);

            return $payment;
        });
    }
}
