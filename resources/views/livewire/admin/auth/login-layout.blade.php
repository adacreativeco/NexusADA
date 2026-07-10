<!DOCTYPE html>
<html lang="tr" class="nx-admin">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/ada-co-os-logo-transparent.svg') }}?v=2">
    <title>Giriş — ADA Co-OS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/nexus-admin.css'])
    @livewireStyles
</head>
<body class="nx-admin">
    {{ $slot }}
    @livewireScripts
</body>
</html>
