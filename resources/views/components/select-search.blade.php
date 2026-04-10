@props([
    'label' => null,
    'placeholder' => 'Cari...',
    'model' => null,
    'searchModel' => null,
    'options' => [],
    'disabled' => false,
])

<div class="relative w-full" 
     x-data="{ open: false }" 
     @click.away="open = false">
    
    @if($label)
        <label class="block text-sm font-bold text-gray-700 mb-1">{{ $label }}</label>
    @endif

    <div class="relative">
        <input 
            type="text"
            placeholder="{{ $placeholder }}"
            wire:model.live.debounce.300ms="{{ $searchModel }}"
            @focus="open = true"
            @input="open = true"
            {{ $disabled ? 'disabled' : '' }}
            class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm pl-4 pr-10 py-2.5 transition-all disabled:bg-gray-50 disabled:text-gray-400"
        >
        
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
            {{-- Spinner: muncul saat loading request untuk searchModel ini --}}
            <svg 
                wire:loading 
                wire:target="{{ $searchModel }}" 
                class="w-4 h-4 animate-spin text-indigo-500" 
                fill="none" viewBox="0 0 24 24"
            >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>

            {{-- Chevron bawah: sembunyikan saat loading --}}
            <svg 
                wire:loading.remove 
                wire:target="{{ $searchModel }}"
                x-show="!open" 
                class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>

            {{-- Chevron atas: sembunyikan saat loading --}}
            <svg 
                wire:loading.remove 
                wire:target="{{ $searchModel }}"
                x-show="open" 
                class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
            </svg>
        </div>
    </div>

    <div 
        x-show="open && !{{ $disabled ? 'true' : 'false' }}" 
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="absolute z-[100] mt-2 w-full bg-white border border-gray-200 rounded-2xl shadow-xl max-h-60 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-200"
        style="display: none;"
    >
        {{-- Skeleton loading rows --}}
        <div wire:loading wire:target="{{ $searchModel }}" class="p-2 space-y-1">
            @foreach(range(1, 4) as $i)
                <div class="h-9 bg-gray-100 rounded-xl animate-pulse"></div>
            @endforeach
        </div>

        {{-- Hasil opsi: sembunyikan saat loading agar tidak flicker --}}
        <div wire:loading.remove wire:target="{{ $searchModel }}">
            @forelse($options as $opt)
                <button 
                    type="button"
                    wire:click="selectOption('{{ $model }}', '{{ $opt->name }}')"
                    @click="open = false"
                    class="w-full text-left px-4 py-3 text-sm hover:bg-indigo-50 transition-colors border-b border-gray-50 last:border-none flex items-center justify-between group"
                >
                    <span class="text-gray-700">{{ $opt->name }}</span>
                </button>
            @empty
                <div class="px-4 py-4 text-sm text-gray-500 text-center italic">
                    <i class="bi bi-search mb-1 block text-lg"></i>
                    Data tidak ditemukan
                </div>
            @endforelse
        </div>
    </div>
</div>