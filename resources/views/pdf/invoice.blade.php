<style>body{font-family:DejaVu Sans,sans-serif;font-size:12px}table{width:100%;border-collapse:collapse}td,th{border:1px solid #999;padding:4px;text-align:left}h2{margin-bottom:2px}</style>
<h2>{{ \App\Models\Setting::getValue('shop_name','ShopFlow') }}</h2>
<p>{{ \App\Models\Setting::getValue('shop_address') }}<br>Phone: {{ \App\Models\Setting::getValue('shop_phone') }}</p>
<h3>Invoice {{ $sale->invoice_no }}</h3>
<p>Date: {{ $sale->sale_date }}<br>Customer: {{ $sale->customer?->name }}<br>Cashier: {{ $sale->user?->name }}</p>
<table><tr><th>Product</th><th>Qty</th><th>Unit price</th><th>Subtotal</th></tr>
@foreach($sale->items as $i)<tr><td>{{ $i->product?->name }}</td><td>{{ $i->quantity }}</td><td>{{ $i->unit_price }}</td><td>{{ $i->subtotal }}</td></tr>@endforeach
</table>
<p>Subtotal: {{ $sale->subtotal }}<br>Discount: {{ $sale->discount }}<br>Tax: {{ $sale->tax }}<br>
<b>Grand total: {{ $sale->grand_total }}</b><br>Paid: {{ $sale->paid_amount }} ({{ $sale->payment_method->value }})<br>Change: {{ $sale->change_amount }}</p>
<p>Thank you for shopping with us.</p>
