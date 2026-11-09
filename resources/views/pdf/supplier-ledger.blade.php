<style>body{font-family:DejaVu Sans,sans-serif;font-size:12px}table{width:100%;border-collapse:collapse}td,th{border:1px solid #999;padding:4px;text-align:left}h2{margin-bottom:2px}</style>
<h2>Supplier Ledger — {{ $supplier->name }}</h2>
<p>{{ $supplier->company }} · Phone: {{ $supplier->phone }}</p>
<h3>Purchases</h3>
<table><tr><th>Date</th><th>Reference</th><th>Total</th><th>Paid</th><th>Due</th></tr>
@foreach($supplier->purchases as $p)<tr><td>{{ $p->purchase_date->format('Y-m-d') }}</td><td>{{ $p->reference_no }}</td><td>{{ $p->total_amount }}</td><td>{{ $p->paid_amount }}</td><td>{{ $p->due_amount }}</td></tr>@endforeach
</table>
<h3>Payments</h3>
<table><tr><th>Date</th><th>Purchase</th><th>Amount</th><th>Method</th></tr>
@foreach($supplier->payments as $pay)<tr><td>{{ $pay->payment_date->format('Y-m-d') }}</td><td>{{ $pay->purchase?->reference_no }}</td><td>{{ $pay->amount }}</td><td>{{ $pay->payment_method->value }}</td></tr>@endforeach
</table>
<p><b>Total due: {{ number_format($supplier->totalDue(),2) }}</b></p>
