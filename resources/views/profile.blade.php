<x-app-layout>

    <div class="pb-20 pt-0 sm:pt-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Page header --}}
            <div class="mb-6 sm:mb-8">
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white tracking-tight">
                    Akun Seting
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola profil, keamanan, dan preferensi akun Anda.
                </p>
            </div>

            {{-- Mobile tab bar (hidden on sm+) --}}
            <div class="flex sm:hidden gap-1 p-1 mb-4 bg-gray-100 dark:bg-gray-800 rounded-xl" role="tablist">
                <a href="#profile-section"
                   class="flex-1 py-2 text-center text-xs font-medium rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
                    Profile
                </a>
                <a href="#password-section"
                   class="flex-1 py-2 text-center text-xs font-medium rounded-lg text-gray-500 dark:text-gray-400 hover:bg-white dark:hover:bg-gray-900 transition-colors">
                    Password
                </a>
                <a href="#alamat"
                   class="flex-1 py-2 text-center text-xs font-medium rounded-lg text-gray-500 dark:text-gray-400 hover:bg-white dark:hover:bg-gray-900 transition-colors">
                    Alamat
                </a>
            </div>

            <div class="flex flex-col sm:flex-row gap-6 lg:gap-8 items-start">

                {{-- Sidebar nav (hidden on mobile) --}}
                <nav class="hidden sm:flex w-48 lg:w-52 flex-shrink-0 flex-col gap-0.5" aria-label="Profile navigation">
                    <a href="#profile-section"
                       class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-800 dark:text-gray-100 bg-gray-100 dark:bg-gray-800 transition-colors">
                        <i class="bi bi-person-fill"></i>
                        Informasi Profile
                    </a>
                    <a href="#password-section"
                       class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <i class="bi bi-person-fill-lock"></i>
                        Password &amp; keamanan
                    </a>
                    <a href="#alamat"
                       class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <i class="bi bi-geo-alt-fill"></i>
                        Alamat
                    </a>
                    <div class="border-t border-gray-200 dark:border-gray-700 my-1.5"></div>
                    {{-- <a href="#delete-section"
                       class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                        </svg>
                        Delete account
                    </a> --}}
                </nav>

                {{-- Content panels --}}
                <div class="flex-1 min-w-0 flex flex-col gap-5">
                    <div id="profile-section"
                         class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-5 sm:p-7 shadow-sm scroll-mt-6">
                        <livewire:profile.update-profile-information-form />
                    </div>
                    <div id="password-section"
                         class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-5 sm:p-7 shadow-sm scroll-mt-6">
                        <livewire:profile.update-password-form />
                    </div>
                    {{-- <div id="delete-section"
                         class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-5 sm:p-7 shadow-sm scroll-mt-6">
                        <livewire:profile.delete-user-form />
                    </div> --}}

                    <div id="alamat">
                        <livewire:services.address-card />
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>