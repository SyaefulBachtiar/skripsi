<x-app-layout>
    <x-slot name="title">
        {{ 'Edit Jasa' }}
    </x-slot>
    
    <livewire:services.dashboard-technician.form-posting-jasa :id_jasa="$id_jasa"/>
</x-app-layout>
