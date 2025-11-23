<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name','Rado Network') }}</title>

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="min-h-screen flex justify-center items-center bg-zinc-950 text-white antialiased">
    <div class="w-full max-w-md mx-auto px-4">
        {{ $slot }}
    </div>
</body>
</html>
