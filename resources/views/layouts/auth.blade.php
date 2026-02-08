<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      class="scroll-smooth">

<head>
    @include('partials.head')
</head>

<body class="bg-background text-foreground min-h-screen flex items-center justify-center ">
<x-notifications/>
<div
    class="w-full max-w-md bg-surface text-surface-foreground rounded-lg shadow-lg flex flex-col items-center p-4 py-6 md:px-8 md:py-10 mx-5">

    <span class="flex h-9 w-9 mb-1 items-center justify-center rounded-md">
        <livewire:logo-icon/>
    </span>
    @if (isset($heading))
        <div class="mb-6 mt-3">
            <h1 class="text-2xl font-semibold text-foreground text-center">{{ $heading }}</h1>
            @if (isset($description))
                <p class="mt-2 text-sm text-muted-foreground text-center">{{ $description }}</p>
            @endif
        </div>
    @endif
    <main class="w-full">

        {{ $slot }}
    </main>
</div>
</body>

</html>
