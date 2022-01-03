<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'App Name') }}</title>

        <!-- Styles -->
        {{-- <link rel="stylesheet" href="{{ mix('css/app.css') }}"> --}}
        @include('layouts.styles')

        <!-- Scripts -->
        @routes
        @include('layouts.scripts')
    </head>
    <body class="font-sans leading-none text-gray-700 antialiased">
        @inertia
    </body>
</html>