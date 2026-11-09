@if (session('status'))
    <div class="sf-alert-success" role="status">{{ session('status') }}</div>
@endif

@if ($errors->any())
    <div class="sf-alert-error" role="alert">{{ $errors->first() }}</div>
@endif
