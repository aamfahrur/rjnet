<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" type="image/svg+xml" href="/images/logo-icon.svg" />
    <title inertia>RJNet - RT/RW Net Management</title>
    @vite(['resources/js/app.tsx'])
    @inertiaHead
</head>
<body class="bg-gray-50 dark:bg-[#0a0e17]">
    @inertia
</body>
</html>
