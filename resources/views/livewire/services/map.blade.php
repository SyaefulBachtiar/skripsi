<div
    class="w-full"
    wire:ignore
    x-data="{
        map: null,
        initMap() {
            if (this.map) return;

            // Pastikan DOM selesai digambar penuh oleh browser sebelum Leaflet di-load
            this.$nextTick(() => {
                const container = this.$refs.mapProgresContainer;
                if (!container) return;

                const lat  = {{ $lat ?? -6.3227 }};
                const lng  = {{ $lng ?? 107.3376 }};
                const name = @js($customerName);

                // Inisialisasi map langsung tembak ke x-ref container
                this.map = L.map(container).setView([lat, lng], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap'
                }).addTo(this.map);

                // Custom Icon Tanpa pembungkus DIV
                const biIcon = L.divIcon({
                    html: `<i class='bi bi-geo-alt-fill' style='font-size: 32px; color: #2563eb; text-shadow: 0 0 3px white;'></i>`,
                    className: 'custom-leaflet-icon',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -32],
                });

                L.marker([lat, lng], { icon: biIcon })
                    .addTo(this.map)
                    .bindPopup(`<div style='text-align:center;padding:2px 4px;'><i class='bi bi-house-door-fill' style='color:#2563eb;font-size:14px'></i><span style='font-size:13px;font-weight:600;margin-left:5px'>${name}</span></div>`);

                // KUNCI UTAMA VPS: Paksa refresh ukuran peta secara berkala di awal agar tidak abu-abu/blank
                let count = 0;
                let interval = setInterval(() => {
                    this.map.invalidateSize();
                    count++;
                    if (count > 5) clearInterval(interval);
                }, 300);

                // Observer cadangan jika map dimuat di dalam tab collapsible yang tersembunyi
                const observer = new IntersectionObserver((entries) => {
                    if (entries[0].isIntersecting) {
                        setTimeout(() => {
                            this.map.invalidateSize();
                        }, 200);
                    }
                });
                observer.observe(container);

                window.addEventListener('resize', () => this.map.invalidateSize());
            });
        }
    }"
    x-init="initMap()"
>
    {{-- PERBAIKAN: Ganti id dinamis menjadi x-ref yang stabil, dan kunci tinggi aslinya lewat inline style --}}
    <div
        x-ref="mapProgresContainer"
        style="height: 300px;"
        class="w-full h-[300px] rounded-xl border border-gray-200 dark:border-gray-700 shadow-inner z-0"
    ></div>

    <style>
        /* Menghilangkan background putih dan border kotak bawaan Leaflet */
        .custom-leaflet-icon {
            background: transparent !important;
            border: none !important;
        }
    </style>
</div>