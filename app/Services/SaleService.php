<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * POS checkout (FR-4.4 to FR-4.8). Owns the DB transaction: sale, items,
 * stock decrease and invoice number commit or roll back together (NFR-10).
 * Prices are always taken from the database, never trusted from the browser.
 */
class SaleService
{
    public function __construct(private StockService $stock) {}

    /**
     * @param  array{customer_id:int, discount?:float, tax?:float, paid_amount:float, payment_method:string,
     *               items: array<int, array{product_id:int, quantity:int}>}  $data
     */
    public function createSale(array $data, User $user): Sale
    {
        return DB::transaction(function () use ($data, $user) {
            $lines = [];
            $subtotal = 0;
            foreach ($data['items'] as $row) {
                $product = Product::findOrFail($row['product_id']);
                $lineTotal = $product->sale_price * $row['quantity'];
                $lines[] = [$product, (int) $row['quantity'], $lineTotal];
                $subtotal += $lineTotal;
            }

            $discount = (float) ($data['discount'] ?? 0);
            $tax = (float) ($data['tax'] ?? 0);
            $grandTotal = $subtotal - $discount + $tax;
            $paid = (float) $data['paid_amount'];

            if ($grandTotal < 0) {
                throw ValidationException::withMessages(['discount' => 'Discount cannot exceed the subtotal plus tax.']);
            }
            if ($data['payment_method'] === 'cash' && $paid < $grandTotal) {
                throw ValidationException::withMessages(['paid_amount' => 'Paid amount is less than the grand total.']);
            }

            $sale = Sale::create([
                'invoice_no' => null,
                'customer_id' => $data['customer_id'],
                'user_id' => $user->id,
                'sale_date' => now(),
                'subtotal' => $subtotal, 'discount' => $discount, 'tax' => $tax,
                'grand_total' => $grandTotal, 'paid_amount' => $paid,
                'change_amount' => max($paid - $grandTotal, 0),
                'payment_method' => $data['payment_method'],
                'status' => 'completed',
            ]);

            foreach ($lines as [$product, $qty, $lineTotal]) {
                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $product->sale_price,   // snapshot (keeps old invoices correct)
                    'cost_price' => $product->cost_price,   // snapshot for profit reports
                    'subtotal' => $lineTotal,
                    'returned_qty' => 0,
                ]);
                // Throws InsufficientStockException -> whole sale rolls back (FR-4.6, FR-1.9)
                $this->stock->decrease($product, $qty, StockMovementType::Sale, $sale, $user);
            }

            $prefix = Setting::getValue('invoice_prefix', 'INV-');
            $sale->update(['invoice_no' => $prefix.str_pad((string) $sale->id, 6, '0', STR_PAD_LEFT)]);

            return $sale->fresh(['items.product', 'customer', 'user']);
        });
    }
}
