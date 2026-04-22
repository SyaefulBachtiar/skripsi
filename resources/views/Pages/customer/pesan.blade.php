<x-app-layout>

    <x-slot name="title">
        {{ 'Pesan' }}
    </x-slot>

    <x-slot name="header">
        <livewire:layout.header-mobile/>
    </x-slot>

    <div class="max-w-7xl mx-auto px-6 sm:px-6 lg:px-8">
         <livewire:services.pesan-customer.konten-pesan/>
    </div>
</x-app-layout>
