<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 theme-aware-bg-card border theme-aware-border rounded-md font-semibold text-xs theme-aware-text-secondary uppercase tracking-widest shadow-sm hover:theme-aware-bg-secondary focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
