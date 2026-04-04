<div class="p-4 bg-white rounded-md sm:rounded-lg shadow-sm sm:shadow-md">
    {{-- List Antrian --}}
    <div class="space-y-2">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-300 pb-2">
            <div>
                <h1 class="text-lg sm:text-xl font-semibold text-gray-800">Antrian</h1>
                <p class="text-sm sm:text-md text-gray-500">4 Antrian</p>
            </div>

           <div class="flex items-center justify-center p-2 rounded-md bg-green-400">
                <i class="bi bi-list-ol text-lg sm:text-xl leading-none"></i>
            </div>
        </div>

        <div class="grid grid-cols-5 gap-3 pt-2">
            <input 
                type="text"
                placeholder="Masukkan alamat..."
                class="col-span-3 w-full px-4 py-2.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-400"
            >

            <select
                class="col-span-2 w-full px-3 py-2.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none cursor-pointer"
            >
                <option selected disabled>Pilih...</option>
                <option value="1">Option 1</option>
                <option value="2">Option 2</option>
            </select>
        </div>

    </div>
</div>
