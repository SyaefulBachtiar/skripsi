<div
    class="w-full"
    wire:ignore
    x-data="{
        map: null,
        initMap() {
            const mapId  = 'map-{{ str_replace('.', '_', $lat) }}-{{ str_replace('.', '_', $lng) }}';
            const lat    = {{ $lat }};
            const lng    = {{ $lng }};
            const name   = @js($customerName);

            const container = document.getElementById(mapId);
            if (!container || container._leaflet_id) return;

            this.map = L.map(mapId).setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(this.map);

            // Ganti ini: Tanpa pembungkus DIV, langsung Icon
            const biIcon = L.divIcon({
                html: `<i class='bi bi-geo-alt-fill' style='font-size: 32px; color: #2563eb; text-shadow: 0 0 3px white;'></i>`,
                className: 'custom-leaflet-icon', // Pakai nama class bebas agar style default Leaflet hilang
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32],
            });

            L.marker([lat, lng], { icon: biIcon })
                .addTo(this.map)
                .bindPopup(`<div style='text-align:center;padding:2px 4px;'><i class='bi bi-house-door-fill' style='color:#2563eb;font-size:14px'></i><span style='font-size:13px;font-weight:600;margin-left:5px'>${name}</span></div>`);

            // Observer tetap dipertahankan agar map terload sempurna saat di-expand
            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting) {
                    setTimeout(() => {
                        this.map.invalidateSize();
                    }, 300);
                }
            });
            observer.observe(container);

            window.addEventListener('resize', () => this.map.invalidateSize());
        }
    }"
    x-init="initMap()"
>
    <div
        id="map-{{ str_replace('.', '_', $lat) }}-{{ str_replace('.', '_', $lng) }}"
        class="w-full h-[300px] rounded-xl border border-gray-200 dark:border-gray-700 shadow-inner z-0"
    ></div>

    <style>
        /* Menghilangkan background putih dan border bawaan Leaflet */
        .custom-leaflet-icon {
            background: transparent !important;
            border: none !important;
        }
    </style>
</div>