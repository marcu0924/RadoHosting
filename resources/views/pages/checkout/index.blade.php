@extends('layouts.main')

@section('title', 'Checkout')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-16 space-y-8">
    <h1 class="text-3xl font-extrabold text-white">Checkout</h1>

    @if (session('error'))
        <div class="rounded-xl border border-red-500/50 bg-red-500/10 p-4 text-sm text-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-2xl border border-zinc-800 bg-zinc-900/60 p-6 space-y-2">
        <div class="text-zinc-400 text-sm">Selected Plan</div>
        <div class="text-xl font-bold text-white">{{ $plan['name'] }}</div>
        <div class="text-zinc-300">
            ${{ number_format($plan['price'], 2) }} / month • {{ $plan['ram'] }} • {{ $plan['cpu'] }} • {{ $plan['storage'] }}
        </div>
    </div>

    <div class="rounded-2xl border border-zinc-800 bg-zinc-950 p-6 text-zinc-400 text-sm">
        Payment integration goes here (Stripe later). For now, provisioning happens immediately.
    </div>

    <form method="POST" action="{{ route('checkout.provision', $plan['slug']) }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm text-zinc-300 mb-2">Server Name</label>
            <input name="name" value="{{ old('name') }}"
                   class="w-full rounded-xl bg-zinc-950 border border-zinc-800 px-4 py-3 text-sm text-white"
                   placeholder="e.g. My Survival World" required>
            @error('name')
                <div class="mt-2 text-sm text-red-300">{{ $message }}</div>
            @enderror
        </div>

        <button
            class="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-5 py-3 text-sm font-semibold text-zinc-900 hover:bg-emerald-400">
            Simulate Success (Provision Server)
        </button>
    </form>
</div>
@endsection
