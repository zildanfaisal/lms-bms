<aside :class="sidebarCollapsed ? 'w-20' : 'w-64'" class="hidden md:block bg-gradient-to-b from-purple-700 via-purple-600 to-blue-400 text-white shadow-lg transition-all duration-200">
    <div class="h-full flex flex-col">
        <div class="flex items-center justify-between p-4 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    <a href="{{ route('dashboard') }}" class="block">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-8 rounded-full object-cover" />
                    </a>
                </div>

                <div x-show="!sidebarCollapsed" class="leading-tight">
                    <div class="text-sm font-semibold">Learning Management</div>
                    <div class="text-sm font-semibold">System Bimasakti</div>
                </div>
            </div>

            <button @click.prevent="sidebarCollapsed = !sidebarCollapsed" class="text-white/80 p-1 rounded hover:bg-white/10">
                <svg x-show="!sidebarCollapsed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 12L6 6V18Z" /></svg>
                <svg x-show="sidebarCollapsed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 12L18 18V6Z" /></svg>
            </button>
        </div>

        <nav class="p-4 flex-1 overflow-auto">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}" title="Dashboard" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">🏠</span>
                        <span x-show="!sidebarCollapsed" class="ms-2">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="#" title="Manajemen Periode" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">📅</span>
                        <span x-show="!sidebarCollapsed" class="ms-2">Struktural</span>
                    </a>
                </li>
            </ul>
        </nav>

        {{-- <div class="p-4 border-t border-white/10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Logout" class="w-full text-left px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center" :class="sidebarCollapsed ? '' : 'justify-start'">
                    <span x-show="!sidebarCollapsed">Logout</span>
                    <span x-show="sidebarCollapsed">🚪</span>
                </button>
            </form>
        </div> --}}
    </div>
</aside>

{{-- Mobile off-canvas sidebar (opens when mobileOpen = true) --}}
<div x-cloak x-show="mobileOpen" class="md:hidden fixed inset-0 z-40">
    <!-- backdrop -->
    <div x-show="mobileOpen" x-transition.opacity class="fixed inset-0 bg-black/50" @click="mobileOpen = false"></div>

    <!-- panel -->
    <aside x-show="mobileOpen" x-transition class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-purple-700 via-purple-600 to-blue-400 text-white shadow-lg overflow-auto">
        <div class="h-full flex flex-col">
            <div class="flex items-center justify-between p-4 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <a href="{{ route('dashboard') }}" class="block">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-8 rounded-full object-cover" />
                        </a>
                    </div>
                    <div class="leading-tight">
                        <div class="text-sm font-semibold">Learning Management</div>
                        <div class="text-sm font-semibold">System Bimasakti</div>
                    </div>
                </div>

                <button @click.prevent="mobileOpen = false" class="text-white/80 p-1 rounded hover:bg-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
            </div>

            <nav class="p-4 flex-1">
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10">
                            <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">🏠</span>
                            <span class="ms-2">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10">
                            <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">📅</span>
                            <span class="ms-2">Manajemen Periode</span>
                        </a>
                    </li>
                </ul>
            </nav>

            {{-- <div class="p-4 border-t border-white/10">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20">Logout</button>
                </form>
            </div> --}}
        </div>
    </aside>
</div>
