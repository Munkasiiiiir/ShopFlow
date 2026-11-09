<button {{ $attributes->merge(['type' => 'submit', 'class' => 'sf-btn-primary']) }}>
    {{ $slot }}
</button>
