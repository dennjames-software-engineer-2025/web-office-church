<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body
    class="font-sans antialiased bg-gray-100"
    x-data="{
        sidebarOpen: JSON.parse(localStorage.getItem('sidebarOpen') ?? 'true'),
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            localStorage.setItem('sidebarOpen', JSON.stringify(this.sidebarOpen));
        }
    }"
>
    @include('layouts.navigation')

    {{-- CONTENT WRAPPER --}}
    <div
        class="min-h-screen flex flex-col"
        :class="sidebarOpen ? 'ml-64' : 'ml-20'"
    >
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex items-center gap-3">
                    {{-- tombol burger pindah ke header biar enak --}}
                    <button type="button"
                        class="inline-flex items-center justify-center w-10 h-10 rounded-lg border bg-white hover:bg-gray-50"
                        @click="toggleSidebar()"
                        aria-label="Toggle sidebar"
                    >
                        ☰
                    </button>

                    <div class="flex-1">
                        {{ $header }}
                    </div>
                </div>
            </header>
        @endisset

        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>
    </div>

    {{-- MODAL TOGGLE (yang kamu sudah pakai di Bidang/Sie) --}}
    <script>
        document.querySelectorAll("[data-modal-toggle]").forEach(btn => {
            btn.addEventListener("click", function () {
                const id = this.getAttribute("data-modal-target");
                document.getElementById(id)?.classList.remove("hidden");
            });
        });

        document.querySelectorAll("[data-modal-hide]").forEach(btn => {
            btn.addEventListener("click", function () {
                const id = this.getAttribute("data-modal-hide");
                document.getElementById(id)?.classList.add("hidden");
            });
        });
    </script>

    {{-- SIDEBAR TOGGLE SCRIPT --}}
    <script>
        (function () {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('contentWrapper');
            const toggleBtn = document.getElementById('sidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');

            if (!sidebar || !content || !toggleBtn || !overlay) return;

            // Apply initial state (desktop)
            function applyDesktopState() {
                const collapsed = document.documentElement.classList.contains('sidebar-collapsed');

                // Sidebar width
                sidebar.classList.toggle('w-20', collapsed);
                sidebar.classList.toggle('w-64', !collapsed);

                // Content margin (desktop only)
                content.classList.toggle('lg:ml-20', collapsed);
                content.classList.toggle('lg:ml-64', !collapsed);

                // Hide/show text labels
                sidebar.querySelectorAll('[data-nav-label]').forEach(el => {
                    el.classList.toggle('hidden', collapsed);
                });

                // Center icons when collapsed
                sidebar.querySelectorAll('[data-nav-item]').forEach(a => {
                    a.classList.toggle('justify-center', collapsed);
                    a.classList.toggle('px-6', !collapsed);
                    a.classList.toggle('px-3', collapsed);
                });

                // Hide big logo text when collapsed
                sidebar.querySelectorAll('[data-logo-text]').forEach(el => {
                    el.classList.toggle('hidden', collapsed);
                });
            }

            // Mobile open/close
            function openMobile() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            }
            function closeMobile() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }

            // Toggle handler:
            // - On mobile: open/close drawer
            // - On desktop: collapse/expand
            toggleBtn.addEventListener('click', () => {
                const isMobile = window.matchMedia('(max-width: 1023px)').matches;

                if (isMobile) {
                    const isHidden = sidebar.classList.contains('-translate-x-full');
                    if (isHidden) openMobile(); else closeMobile();
                    return;
                }

                // desktop collapse
                const collapsed = document.documentElement.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebar_collapsed', collapsed ? '1' : '0');
                applyDesktopState();
            });

            overlay.addEventListener('click', closeMobile);

            // Init:
            // - Mobile: sidebar hidden by default
            // - Desktop: apply collapsed state from localStorage
            function init() {
                const isMobile = window.matchMedia('(max-width: 1023px)').matches;

                if (isMobile) {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                } else {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.add('hidden');
                    applyDesktopState();
                }
            }

            window.addEventListener('resize', init);
            init();
        })();
    </script>

    {{-- SweetAlert Notif --}}
    @if(session('status'))
    <script>
        Swal.fire({
            icon: 'success',
            title: @json(session('status')),
            timer: 2000,
            showConfirmButton: false
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: "Terjadi Kesalahan",
            text: @json(session('error')),
        });
    </script>
    @endif
</body>
</html>
