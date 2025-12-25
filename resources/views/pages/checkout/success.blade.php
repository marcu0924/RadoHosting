@extends('layouts.main')

@section('title', 'Success')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-16 text-center space-y-4">
    <h1 class="text-4xl font-extrabold text-white">Success ✅</h1>

    <p class="text-zinc-400">
        Your server is provisioning. You’ll see it appear in your dashboard shortly.
    </p>

    <div class="pt-6 flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('servers.index') }}"
           class="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-5 py-3 text-sm font-semibold text-zinc-900 hover:bg-emerald-400">
            Go to My Servers
        </a>

        @if(request('server_id'))
            <a href="{{ route('servers.show', request('server_id')) }}"
               class="inline-flex items-center justify-center rounded-xl border border-zinc-700 px-5 py-3 text-sm font-semibold text-white hover:border-emerald-500/60 hover:text-emerald-300">
                Open Server Dashboard
            </a>
        @endif
    </div>
</div>
@endsection
