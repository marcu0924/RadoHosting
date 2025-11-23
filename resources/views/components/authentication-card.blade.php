<div {{ $attributes->merge(['class' => 'auth-card bg-zinc-900 border border-zinc-800 rounded-xl p-8 shadow-xl']) }}>
    <div class="flex justify-center mb-6">
        {{ $logo }}
    </div>

    {{ $slot }}
</div>
