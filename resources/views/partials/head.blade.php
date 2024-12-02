<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title inertia>Kontakami {{ @$pageTitle ? "| {$pageTitle}" : '' }}</title>
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
<link rel="stylesheet" href="https://muhammadlailil.github.io/iconsax/style/iconsax.css" />
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@if (config('app.env') === 'development')
    @routes
@endif
@vite('resources/css/app.css')

@vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
@inertiaHead
