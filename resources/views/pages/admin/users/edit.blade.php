<x-app-layout>
     <x-slot name="title">
        {{ 'User edit' }}
    </x-slot>

    <x-slot name="header">
        <div class="py-5 px-4 bg-white shadow-sm w-full">
            <a href="{{ route('users.view') }}" class="flex items-center justify-start gap-2">
                <i class="bi bi-chevron-left leading-none"></i>
                <span class="leading-none">Kembali</span>
            </a>
        </div>
    </x-slot>

    <div class="sm:pt-10 pt-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <livewire:services.admin.users.edit :id="$id"/>
        </div>
    </div>
</x-app-layout>
