<div class="w-64 bg-white border-r shadow-lg min-h-screen hidden md:block">
    <div class="p-6 border-b">
        <h1 class="text-lg font-bold text-gray-700">WEB OFFICE</h1>
    </div>

    <nav class="mt-4">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
            class="flex items-center px-6 py-3 text-sm font-medium 
                {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
            <i class="fas fa-home mr-3"></i> Dashboard
        </a>

        {{-- Admin Group --}}
        @role('super_admin')
        <div class="mt-6 px-6 text-xs text-gray-400 uppercase">Admin</div>

        <a href="{{ route('users.index') }}"
            class="flex items-center px-6 py-3 text-sm font-medium 
                {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
            <i class="fas fa-users mr-3"></i> Manajemen User
        </a>
        @endrole

        {{-- Struktur Organisasi --}}
        <div class="mt-6 px-6 text-xs text-gray-400 uppercase">Struktur Organisasi</div>

        <a href="{{ route('bidangs.index') }}"
            class="flex items-center px-6 py-3 text-sm font-medium 
                {{ request()->routeIs('bidang.*') || request()->routeIs('sie.*') ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
            <i class="fas fa-sitemap mr-3"></i> Bidang & Sie
        </a>

        {{-- Dokumen --}}
        <div class="mt-6 px-6 text-xs text-gray-400 uppercase">Dokumen</div>

        <a href="{{ route('documents.index') }}"
            class="flex items-center px-6 py-3 text-sm font-medium 
                {{ request()->routeIs('documents.*') ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
            <i class="fas fa-folder mr-3"></i> Dokumen
        </a>

        <a href="{{ route('templates.index') }}"
            class="flex items-center px-6 py-3 text-sm font-medium 
                {{ request()->routeIs('templates.*') ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
            <i class="fas fa-copy mr-3"></i> Template
        </a>

    </nav>
</div>
