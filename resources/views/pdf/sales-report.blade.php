<style>body{font-family:DejaVu Sans,sans-serif;font-size:12px}table{width:100%;border-collapse:collapse}td,th{border:1px solid #999;padding:4px;text-align:left}h2{margin-bottom:2px}</style>
<h2>Sales Report</h2>
<table><tr><th>Invoice</th><th>Date</th><th>Customer</th><th>Cashier</th><th>Payment</th><th>Total</th></tr>
@foreach($sales as $s)<tr><td>{{ $s->invoice_no }}</td><td>{{ $s->sale_date }}</td><td>{{ $s->customer?->name }}</td><td>{{ $s->user?->name }}</td><td>{{ $s->payment_method->value }}</td><td>{{ $s->grand_total }}</td></tr>@endforeach
</table>
<p><b>Total: {{ number_format($sales->sum('grand_total'),2) }}</b></p>
