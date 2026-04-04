<div class="space-y-4 rounded-2xl overflow-hidden">

    {{-- Images --}}
    <div 
        x-data="{ 
            activeSlide: 0, 
            slides: [0, 1], 
            touchStartX: 0,
            touchEndX: 0,
            scrollTo(index) {
                this.activeSlide = index;
                this.$refs.slider.scrollTo({
                    left: this.$refs.slider.offsetWidth * index,
                    behavior: 'smooth'
                });
            },
            handleSwipe() {
                // Jika geser ke kiri (swipe left)
                if (this.touchStartX - this.touchEndX > 50) {
                    if (this.activeSlide < this.slides.length - 1) {
                        this.scrollTo(this.activeSlide + 1);
                    }
                }
                // Jika geser ke kanan (swipe right)
                if (this.touchEndX - this.touchStartX > 50) {
                    if (this.activeSlide > 0) {
                        this.scrollTo(this.activeSlide - 1);
                    }
                }
            }
        }" 
        class="relative w-full overflow-hidden group"
    >
        <div 
            x-ref="slider"
            @touchstart="touchStartX = $event.changedTouches[0].screenX"
            @touchend="touchEndX = $event.changedTouches[0].screenX; handleSwipe()"
            class="flex overflow-x-hidden snap-x snap-mandatory scroll-smooth"
        >
            <div class="min-w-full snap-center">
                <img src="{{ asset('assets/icons/empty_image.webp') }}" class="w-full h-64 object-contain pointer-events-none">
            </div>
            <div class="min-w-full snap-center">
                <img src="{{ asset('assets/icons/empty_image.webp') }}" class="w-full h-64 object-contain pointer-events-none">
            </div>
        </div>

        <button 
            @click="scrollTo(activeSlide === 0 ? slides.length - 1 : activeSlide - 1)"
            class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 p-2 rounded-full text-white transition opacity-0 group-hover:opacity-100"
        >
            &#10094;
        </button>
        
        <button 
            @click="scrollTo(activeSlide === slides.length - 1 ? 0 : activeSlide + 1)"
            class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 p-2 rounded-full text-white transition opacity-0 group-hover:opacity-100"
        >
            &#10095;
        </button>

        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-2">
            <template x-for="(slide, index) in slides" :key="index">
                <button 
                    @click="scrollTo(index)"
                    :class="activeSlide === index ? 'bg-white w-4' : 'bg-white/50 w-2'"
                    class="h-2 rounded-full transition-all duration-300"
                ></button>
            </template>
        </div>
    </div>

    <div class="px-4">
        <div
            class="bg-white rounded py-4 px-4 pb-2 space-y-4"
        >
            {{-- Judul --}}
            <div class="border-b border-gray-300 pb-2">
                <div class="flex justify-between items-center">
                    <h1 class="font-semibold text-lg sm:text-xl text-gray-800 mb-1">Jasa Servis AC</h1>
                    <i class="bi bi-bookmark text-base sm:text-2xl"></i>
                </div>
                <div class="flex items-center gap-2">
                    <div class="text-yellow-400 text-sm">
                        <span>4.8</span>
                        <i class="bi bi-star-fill"></i>
                        <span class="text-yellow-600">(100)</span>
                    </div>
                    <span class="text-xs text-gray-400">|</span>
                    <span class="text-gray-500 font-semibold">
                        100 menggunakan jasa
                    </span>
                </div>
            </div>

            {{-- Description --}}
            <div class="border-b border-gray-300 pb-1">
                <div class="flex items-center justify-between">
                    <h1 class="font-semibold text-lg sm:text-xl text-gray-800">Deskripsi</h1>
                    <i class="bi bi-chevron-down"></i>
                </div>
            </div>
        </div>
    </div>
</div>