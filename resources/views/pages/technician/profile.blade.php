<x-app-layout>

    <x-slot name="title">
        {{ 'Profile' }}
    </x-slot>

    <div class="pb-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 pb-10 sm:pb-0">

            <livewire:services.profile-technician.profile-information/>

            <livewire:services.profile-technician.profile-data/>

            <livewire:services.profile-technician.profile-data-diri/>

        </div>
    </div>
</x-app-layout>
