<div 
    x-data="{
        map: null,
        marker: null,
        modalMap: null,
        modalMarker: null,
        // Gunakan default value (Contoh: Jakarta) jika latitude/longitude kosong
        latitude: @entangle('latitude'), 
        longitude: @entangle('longitude'),
        showModal: false,
        searchQuery: '',
        searchResults: [],

        initMap() {
            // Gunakan koordinat dari Livewire, jika kosong pakai default (Jakarta)
            let lat = this.latitude || -6.200000;
            let lng = this.longitude || 106.816666;

            this.map = L.map(this.$refs.mapContainer).setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.map);
            
            this.marker = L.marker([lat, lng], { draggable: false }).addTo(this.map);
        },

        updateCoords(latlng) {
            // Leaflet menggunakan .lat dan .lng (bukan latitude/longitude)
            this.latitude = latlng.lat;
            this.longitude = latlng.lng;
            
            if(this.marker) this.marker.setLatLng(latlng);
            if(this.modalMarker) this.modalMarker.setLatLng(latlng);
        },

        openFullMap() {
            this.showModal = true;
            this.$nextTick(() => {
                let lat = this.latitude || -6.200000;
                let lng = this.longitude || 106.816666;

                if (!this.modalMap) {
                    this.modalMap = L.map(this.$refs.modalMapContainer).setView([lat, lng], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.modalMap);
                    this.modalMarker = L.marker([lat, lng], { draggable: true }).addTo(this.modalMap);
                    
                    this.modalMarker.on('dragend', (e) => { this.updateCoords(e.target.getLatLng()); });
                    this.modalMap.on('click', (e) => { this.updateCoords(e.latlng); });
                } else {
                    this.modalMap.setView([lat, lng], 15);
                    this.modalMarker.setLatLng([lat, lng]);
                    setTimeout(() => { this.modalMap.invalidateSize(); }, 100);
                }
            });
        },
        async searchAddress() {
            if (this.searchQuery.length < 3) return;
            const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${this.searchQuery}`);
            this.searchResults = await res.json();
        },

        selectLocation(res) {
            const loc = { lat: parseFloat(res.lat), lng: parseFloat(res.lon) };
            this.updateCoords(loc);
            this.modalMap.setView([loc.lat, loc.lng], 17);
            this.searchResults = [];
            this.searchQuery = res.display_name;
        },

        getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    const loc = { lat: position.coords.latitude, lng: position.coords.longitude };
                    this.updateCoords(loc);
                    this.map.setView([loc.lat, loc.lng], 17);
                    if(this.modalMap) this.modalMap.setView([loc.lat, loc.lng], 17);
                });
            }
        }
    }"
    x-init="initMap()"
    class="p-4 rounded bg-white shadow-sm border border-gray-100"
>
    <h1 class="text-lg sm:text-xl font-semibold text-gray-800">Atur Alamat</h1>
    
    <form class="pt-4 space-y-4 sm:space-y-6" wire:submit.prevent="save">
        <div>
            <x-input-label for="detail_alamat" :value="__('Nama Jalan, Gedung, No.Rumah')" />
            <x-textarea-input id="detail_alamat" class="w-full" wire:model="detail_alamat" />
            <x-input-error :messages="$errors->get('detail_alamat')" class="mt-2" />
        </div>

        <div class="space-y-2">
             <div class="flex justify-between items-end">
                <x-input-label :value="__('Lokasi di Peta')" />
                <button 
                    type="button"
                    @click="getCurrentLocation()" 
                    class="text-xs font-medium text-blue-600 hover:text-blue-800 flex items-center gap-1"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Gunakan Lokasi Saat Ini
                </button>
            </div>

            <div 
                @click="openFullMap()"
                x-ref="mapContainer" 
                class="w-full h-48 rounded-lg border border-gray-300 cursor-pointer shadow-inner"
            ></div>
            <p class="text-[10px] text-gray-400 italic">*Klik peta untuk memperbesar & mencari alamat</p>
        </div>

       <button 
            type="submit" 
            wire:loading.attr="disabled"
            class="w-full py-3 bg-gray-900 text-white rounded-lg font-semibold hover:bg-gray-800 transition disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2"
        >
            {{-- Teks ini akan hilang saat loading --}}
            <span wire:loading.remove wire:target="save">
                Simpan Alamat
            </span>

            {{-- Spinner ini hanya muncul saat loading fungsi 'save' --}}
            <span wire:loading wire:target="save" class="flex items-center gap-2">
                <svg class="animate-spin h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses...
            </span>
        </button>
    </form>

    <div 
        x-show="showModal" 
        x-transition 
        class="fixed inset-0 z-[9999] bg-white flex flex-col"
        style="display: none;"
    >
        <div class="p-4 shadow-md bg-white z-[10000] space-y-3">
            <div class="flex items-center gap-2">
                <button @click="showModal = false" class="p-2 hover:bg-gray-100 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <div class="relative flex-1">
                    <input 
                        type="text" 
                        x-model="searchQuery" 
                        @keyup.enter="searchAddress()"
                        placeholder="Cari nama jalan atau area..." 
                        class="w-full border-gray-300 rounded-full pl-4 pr-10 focus:ring-blue-500 focus:border-blue-500"
                    >
                    <button @click="searchAddress()" class="absolute right-3 top-2.5 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </button>
                </div>
            </div>

            <div x-show="searchResults.length > 0" class="absolute left-4 right-4 bg-white border rounded-lg shadow-xl max-h-60 overflow-y-auto z-[10001]">
                <template x-for="res in searchResults" :key="res.place_id">
                    <div @click="selectLocation(res)" class="p-3 border-b hover:bg-gray-50 cursor-pointer text-sm">
                        <span x-text="res.display_name"></span>
                    </div>
                </template>
            </div>
        </div>

        <div x-ref="modalMapContainer" class="flex-1 w-full h-full z-0"></div>

        <button 
            @click="getCurrentLocation()"
            class="absolute bottom-20 right-6 p-4 bg-white shadow-xl rounded-full text-blue-600 hover:bg-gray-50 z-[10000]"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
        </button>

        <div class="p-4 bg-white border-t z-[10000]">
            <button @click="showModal = false" class="w-full py-3 bg-blue-600 text-white rounded-lg font-bold">
                Konfirmasi Lokasi
            </button>
        </div>
    </div>
</div>