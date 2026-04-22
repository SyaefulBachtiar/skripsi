<x-app-layout>

    <x-slot name="title">
        {{ 'Lacak' }}
    </x-slot>

    <x-slot name="header">
        <livewire:layout.header-mobile/>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <livewire:services.lacak-pesanan/>
    </div>
</x-app-layout>
