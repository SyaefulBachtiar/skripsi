<x-app-layout>
     <x-slot name="title">
        {{ 'Beranda' }}
    </x-slot>

    <x-slot name="header">
        <livewire:layout.header-mobile/>
    </x-slot>

    <div class="pt-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <livewire:services.admin.quick-access/>

            <livewire:services.admin.daftar-acc-teknisi/>
        </div>
    </div>
</x-app-layout>
