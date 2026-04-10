<x-app-layout>

    <x-slot name="title">
        {{ 'Beranda' }}
    </x-slot>

    <x-slot name="header">
        <livewire:layout.header-mobile/>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Address --}}
            <livewire:services.address-card/>

            {{-- Product --}}
            <livewire:services.beranda.product/>
        </div>
    </div>
</x-app-layout>
