@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm theme-aware-text-secondary']) }}>
    {{ $value ?? $slot }}
</label>
