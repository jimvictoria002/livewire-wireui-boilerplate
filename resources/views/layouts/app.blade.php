<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      class="scroll-smooth">

<head>
    @include('partials.head')
</head>

<body class="bg-surface text-foreground overflow-hidden ">
<x-notifications/>
<livewire:sidebar>
    {{ $slot }}
</livewire:sidebar>
</body>

</html>
