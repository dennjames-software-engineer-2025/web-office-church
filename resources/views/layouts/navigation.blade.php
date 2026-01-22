<nav
    class="fixed inset-y-0 left-0 bg-white border-r shadow-sm overflow-y-auto flex flex-col transition-[width] duration-200"
    :class="sidebarOpen ? 'w-64' : 'w-20'"
>
    {{-- BRAND --}}
    <div class="px-4 py-4 border-b flex items-center gap-3">
        {{-- logo --}}
        <img src="{{ asset('images/logo-rm.png') }}" class="w-10 h-10 rounded" alt="Logo">

        <div x-show="sidebarOpen" class="leading-tight">
            <div class="font-semibold text-gray-800">WEB OFFICE</div>
            <div class="text-xs text-gray-500">Church Management</div>
        </div>
    </div>

    {{-- MENU --}}
    <div class="mt-3 px-2 space-y-1">

        {{-- PENGUMUMAN (Dashboard) --}}
        @can('announcements.view')
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
               {{ (request()->routeIs('dashboard') || request()->routeIs('announcements.*'))
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-gray-700 hover:bg-gray-100' }}"
               :title="sidebarOpen ? '' : 'Pengumuman'">
                <span class="w-6 h-6 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16v12H5.5L4 17.5V4z"/>
                        <path d="M7 8h10M7 11h10"/>
                    </svg>
                </span>
                <span x-show="sidebarOpen" class="truncate">Pengumuman</span>
            </a>
        @else
            {{-- fallback kalau permission belum dibuat/di-assign --}}
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
               {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}"
               :title="sidebarOpen ? '' : 'Pengumuman'">
                <span class="w-6 h-6 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16v12H5.5L4 17.5V4z"/>
                        <path d="M7 8h10M7 11h10"/>
                    </svg>
                </span>
                <span x-show="sidebarOpen" class="truncate">Pengumuman</span>
            </a>
        @endcan

        {{-- NOTULENSI RAPAT --}}
        @can('notulensi.view')
            <a href="{{ route('minutes.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
            {{ request()->routeIs('minutes.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}"
            :title="sidebarOpen ? '' : 'Notulensi Rapat'">
                <span class="w-6 h-6 flex items-center justify-center">
                    {{-- clipboard icon --}}
                    <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5" stroke="currentColor" stroke-width="2">
                        <path d="M9 5h6M9 3h6a2 2 0 0 1 2 2v2H7V5a2 2 0 0 1 2-2z"/>
                        <path d="M7 7h10v14H7z"/>
                        <path d="M9 11h6M9 15h6"/>
                    </svg>
                </span>
                <span x-show="sidebarOpen" class="truncate">Notulensi Rapat</span>
            </a>
        @endcan

        {{-- DOKUMEN --}}
        <a href="{{ route('documents.index') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
           {{ request()->routeIs('documents.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}"
           :title="sidebarOpen ? '' : 'Dokumen'">
            <span class="w-6 h-6 flex items-center justify-center">
                <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h10l6 6v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/>
                    <path d="M14 4v6h6"/>
                </svg>
            </span>
            <span x-show="sidebarOpen" class="truncate">Dokumen</span>
        </a>

        {{-- PENGAJUAN PROPOSAL --}}
        <a href="{{ route('proposals.index') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
           {{ request()->routeIs('proposals.*')
                ? 'bg-blue-50 text-blue-700'
                : 'text-gray-700 hover:bg-gray-100' }}"
           :title="sidebarOpen ? '' : 'Pengajuan Proposal'">
            <span class="w-6 h-6 flex items-center justify-center">
                {{-- doc/check icon --}}
                <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5" stroke="currentColor" stroke-width="2">
                    <path d="M7 3h7l5 5v13a1 1 0 0 1-1 1H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                    <path d="M14 3v5h5"/>
                    <path d="M9 14l2 2 4-4"/>
                </svg>
            </span>
            <span x-show="sidebarOpen" class="truncate">Pengajuan Proposal</span>
        </a>

        {{-- BUAT AKUN USER (super_admin) --}}
        @role('super_admin')
            <a href="{{ route('users.index') }}"
               class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
               {{ request()->routeIs('users.*') && !request()->routeIs('users.pending')
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-gray-700 hover:bg-gray-100' }}"
               :title="sidebarOpen ? '' : 'Buat Akun User'">
                <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H2v-2a4 4 0 014-4h1m6-4a4 4 0 11-8 0 4 4 0 018 0zm6 0a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span x-show="sidebarOpen" class="truncate">Buat Akun User</span>
            </a>
        @endrole

        {{-- USER APPROVAL (kalau ada) --}}
        @can('user.manage')
            <a href="{{ route('users.pending') }}"
               class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
               {{ request()->routeIs('users.pending') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}"
               :title="sidebarOpen ? '' : 'User Approval'">
                <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.66 0 3-1.34 3-3S13.66 5 12 5 9 6.34 9 8s1.34 3 3 3z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 21v-1a7 7 0 00-14 0v1"/>
                </svg>
                <span x-show="sidebarOpen" class="truncate">User Approval</span>
            </a>
        @endcan

        {{-- BIDANG & SIE (super_admin) --}}
        @role('super_admin')
            <a href="{{ route('bidangs.index') }}"
               class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
               {{ (request()->routeIs('bidangs.*') || request()->routeIs('sies.*'))
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-gray-700 hover:bg-gray-100' }}"
               :title="sidebarOpen ? '' : 'Buat Bidang & Sie'">
                <span class="w-6 h-6 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5" stroke="currentColor" stroke-width="2">
                        <path d="M12 3v6M5 21v-2m14 2v-2M5 19h14"/>
                        <path d="M12 9H5a2 2 0 0 0-2 2v4m9-6h7a2 2 0 0 1 2 2v4"/>
                    </svg>
                </span>
                <span x-show="sidebarOpen" class="truncate">Kelola Bidang &amp; Sie</span>
            </a>
        @endrole

        {{-- PROGRAMS (DISABLE sementara via feature flag) --}}
        {{-- @if (config('features.programs'))
            <a href="{{ route('programs.index') }}"
            class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
            {{ request()->routeIs('programs.*')
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-gray-700 hover:bg-gray-100' }}"
            :title="sidebarOpen ? '' : 'Buat Programs'">
                <span class="w-6 h-6 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5" stroke="currentColor" stroke-width="2">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M2 12a10 10 0 1 0 20 0"/>
                    </svg>
                </span>
                <span x-show="sidebarOpen" class="truncate">Buat Programs</span>
            </a>
        @endif --}}

        {{-- MANAJEMEN FILE (sekretaris) --}}
        @can('files.manage')
            <a href="{{ route('folders.index') }}"
               class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
               {{ (request()->routeIs('folders.*') || request()->routeIs('saved-files.*'))
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-gray-700 hover:bg-gray-100' }}"
               :title="sidebarOpen ? '' : 'Manajemen File'">
                <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h5l2 2h9a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                </svg>
                <span x-show="sidebarOpen" class="truncate">Manajemen File</span>
            </a>
        @endcan

        {{-- TEMPLATE --}}
        <a href="{{ route('templates.index') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
           {{ request()->routeIs('templates.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}"
           :title="sidebarOpen ? '' : 'Manajemen Template'">
            <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h5l2 2h9a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
            </svg>
            <span x-show="sidebarOpen" class="truncate">Manajemen Template</span>
        </a>

        {{-- DOKUMEN DIBAGIKAN --}}
        @can('files.view')
            <a href="{{ route('files.shared.index') }}"
               class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
               {{ request()->routeIs('files.shared.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}"
               :title="sidebarOpen ? '' : 'Dokumen Dibagikan'">
                <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12v7a1 1 0 001 1h14a1 1 0 001-1v-7M12 3v12m0 0l-3-3m3 3l3-3" />
                </svg>
                <span x-show="sidebarOpen" class="truncate">Dokumen Dibagikan</span>
            </a>
        @endcan

    </div>

    {{-- USER SECTION --}}
    <div class="mt-auto p-4 border-t bg-gray-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-semibold">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>

            <div x-show="sidebarOpen" class="min-w-0">
                <div class="font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</div>
                <div class="text-xs text-gray-500 truncate">{{ Auth::user()->getRoleNames()->implode(', ') }}</div>
            </div>
        </div>

        <div class="mt-3 space-y-2">
            <a href="{{ route('profile.edit') }}"
               class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm"
               :title="sidebarOpen ? '' : 'Edit Profile'">
                <span>👤</span>
                <span x-show="sidebarOpen">Edit Profile</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm"
                        :title="sidebarOpen ? '' : 'Logout'">
                    <span>⎋</span>
                    <span x-show="sidebarOpen">Logout</span>
                </button>
            </form>
        </div>
    </div>
</nav>
