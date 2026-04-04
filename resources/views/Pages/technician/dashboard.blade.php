<x-app-layout>

    <x-slot name="title">
        {{ 'Dashboard' }}
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Address --}}
            <livewire:services.address-card/>

            @if(!empty($data->spesialisasi))

                {{-- Pekerjaan --}}
                {{-- <livewire:services.dashboard-technician.pekerjaan/> --}}

                {{-- List Antrian --}}
                <livewire:services.dashboard-technician.list-antrian/>

            @else
                <div class="pb-12 sm:pb-0">
                    <livewire:services.dashboard-technician.form-technician/>
                </div>

            @endif

        </div>
    </div>
</x-app-layout>
