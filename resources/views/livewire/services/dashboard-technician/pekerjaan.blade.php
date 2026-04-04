<div class="p-4 rounded-lg sm:rounded-xl bg-white shadow-sm sm:shadow-md space-y-3">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-lg sm:text-xl font-semibold text-gray-800">Pekerjaan</h1>
        <div class="px-2 py-1 bg-blue-600 rounded-md">
            <i class="bi bi-activity text-white"></i>
        </div>
    </div>

    {{-- List Work --}}
    <div class="space-y-2">

        {{-- Selesai --}}
        <div class="flex items-center justify-between bg-green-300/60 px-2 py-1 rounded-lg text-md">
            <span>Selesai</span>
            <span>14</span>
        </div>

        {{-- Progress --}}
        <div class="flex items-center justify-between bg-orange-300/60 px-2 py-1 rounded-lg text-md">
            <span>Progres</span>
            <span>14</span>
        </div>

        {{-- Waiting --}}
        <div class="flex items-center justify-between bg-yellow-300/60 px-2 py-1 rounded-lg text-md">
            <span>Menunggu</span>
            <span>14</span>
        </div>

    </div>
</div>
