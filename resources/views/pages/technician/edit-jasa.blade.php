<x-app-layout>
    <x-slot name="title">
        {{ 'Edit Jasa' }}
    </x-slot>

    <x-slot name="header">
        <div class=" flex items-center gap-2 py-4 px-4 bg-gray-50 fixed top-0 w-full z-50 shadow-sm">
            <a 
                href="{{ route('jasa.technician') }}"
                class="leading-none"
            >
                <i class="bi bi-chevron-left font-bold"></i>
            </a>
            <h1 class="font-semibold">Edit Jasa</h1>
        </div>
    </x-slot>

    
    <livewire:services.dashboard-technician.form-posting-jasa :id_jasa="$id_jasa"/>
</x-app-layout>
