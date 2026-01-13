<nav class="fixed inset-y-0 left-0 w-64 bg-white border-r shadow-lg min-h-screen overflow-y-auto">

    {{-- Logo --}}
    <div class="px-6 py-5 border-b">
        <h1 class="text-xl font-semibold text-gray-700">WEB OFFICE</h1>
    </div>

    {{-- Menu --}}
    <div class="mt-4">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
            class="flex items-center px-6 py-3 text-sm font-medium
            {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
            <i class="fas fa-home mr-3"></i> Dashboard
        </a>

        {{-- Dokumen --}}
        <a href="{{ route('documents.index') }}"
            class="flex items-center px-6 py-3 text-sm font-medium
            {{ request()->routeIs('documents.*') ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
            <i class="fas fa-folder mr-3"></i> Dokumen
        </a>

        {{-- CRUD User --}}
        @role('super_admin')
            <a href="{{ route('users.index') }}"
                class="flex items-center px-6 py-3 text-sm font-medium
            {{ request()->routeIs('users.*') && !request()->routeIs('users.pending')
                    ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600'
                    : 'text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-users mr-3"></i> Buat Akun User
            </a>
        @endrole

        {{-- CRUD Template --}}
        <a href="{{ route('templates.index') }}"
            class="flex items-center px-6 py-3 text-sm font-medium
            {{ request()->routeIs('templates.*') ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
            <i class="fas fa-copy mr-3"></i> CRUD Template
        </a>

        {{-- CRUD Bidang & Sie --}}
        <a href="{{ route('bidangs.index') }}"
            class="flex items-center px-6 py-3 text-sm font-medium
            {{ request()->routeIs('bidangs.*') || request()->routeIs('sies.*') ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
            <i class="fas fa-sitemap mr-3"></i> CRUD Bidang & Sie
        </a>

        {{-- Programs --}}
        @can('viewAny', \App\Models\Program::class)
        <a href="{{ route('programs.index') }}"
            class="flex items-center px-6 py-3 text-sm font-medium
            {{ request()->routeIs('programs.*') ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
            <i class="fas fa-tasks mr-3"></i> Programs
        </a>
        @endcan
        {{-- End --}}

        {{-- User Pending --}}
        @can('user.approve_registration')
        <a href="{{ route('users.pending') }}"
            class="flex items-center px-6 py-3 text-sm font-medium
            {{ request()->routeIs('users.pending') ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
            <i class="fas fa-user-clock mr-3"></i> User Pending
        </a>
        @endcan

    </div>

    {{-- User Info --}}
    <div class="absolute bottom-0 w-full px-6 py-5 border-t bg-gray-50">
        <p class="font-medium text-gray-700 mb-2">{{ Auth::user()->name }}</p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-red-600 hover:text-red-800 font-medium">Logout</button>
        </form>
    </div>

</nav>
