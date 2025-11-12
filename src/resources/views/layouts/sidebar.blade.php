<aside :class="sidebarCollapsed ? 'w-20' : 'w-64'" class="hidden md:block bg-gradient-to-b from-purple-700 via-purple-600 to-blue-400 text-white shadow-lg transition-all duration-200">
    <div class="h-full flex flex-col">
        <div class="flex items-center justify-between p-4 border-b border-white/10">
            <div class="flex items-center gap-3 flex-1">
                <div class="flex-shrink-0">
                    <a href="{{ route('dashboard') }}" class="block">
                        <img src="{{ asset('images/logo_bms.png') }}" alt="Logo" class="h-12 w-12 object-contain" />
                    </a>
                </div>

                <div x-show="!sidebarCollapsed" class="leading-tight flex-1">
                    <div class="text-sm font-semibold">Bimasakti Learning</div>
                    <div class="text-sm font-semibold">Management System</div>
                </div>
            </div>
        </div>

        @php
            $user = request()->user();
            $isManager = in_array($user?->karyawan?->jabatan?->nama_jabatan, ['Manager', 'Senior Manager']);
        @endphp
        <nav class="p-4 flex-1 overflow-auto">
            <ul class="space-y-2">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}" title="Dashboard" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z" />
                                <path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z" />
                            </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" class="ms-2">Dashboard</span>
                    </a>
                </li>
                <!-- Role & Permission -->
                @can('manage roles & permissions')
                <li x-data="{ open: false }" class="relative">
                    <button @click.prevent="open = !open" :aria-expanded="open.toString()" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" class="ms-2 flex-1 text-start">Role & Permission</span>
                        <!-- chevron shown only when sidebar not collapsed -->
                        <svg x-show="!sidebarCollapsed" :class="open ? 'rotate-90' : ''" class="h-4 w-4 transform transition-transform text-white/80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- submenu (desktop) -->
                    <ul x-cloak x-show="open" x-transition class="mt-2 space-y-1 ps-10" style="display:none;">
                        <li>
                            <a href="{{ route('roles.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Role</a>
                        </li>
                        <li>
                            <a href="{{ route('permissions.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Permission</a>
                        </li>
                        <li>
                            <a href="{{ route('users.roles.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">User Roles</a>
                        </li>
                    </ul>
                </li>
                @endcan
                @role('Admin|Super Admin')
                <!-- Learning Plan-->
                @if($isManager)
                <li x-data="{ open: false }" class="relative">
                    <button @click.prevent="open = !open" :aria-expanded="open.toString()" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path fill-rule="evenodd" d="M7.502 6h7.128A3.375 3.375 0 0 1 18 9.375v9.375a3 3 0 0 0 3-3V6.108c0-1.505-1.125-2.811-2.664-2.94a48.972 48.972 0 0 0-.673-.05A3 3 0 0 0 15 1.5h-1.5a3 3 0 0 0-2.663 1.618c-.225.015-.45.032-.673.05C8.662 3.295 7.554 4.542 7.502 6ZM13.5 3A1.5 1.5 0 0 0 12 4.5h4.5A1.5 1.5 0 0 0 15 3h-1.5Z" clip-rule="evenodd" />
                                <path fill-rule="evenodd" d="M3 9.375C3 8.339 3.84 7.5 4.875 7.5h9.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 0 1 3 20.625V9.375ZM6 12a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75H6.75a.75.75 0 0 1-.75-.75V12Zm2.25 0a.75.75 0 0 1 .75-.75h3.75a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1-.75-.75ZM6 15a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75H6.75a.75.75 0 0 1-.75-.75V15Zm2.25 0a.75.75 0 0 1 .75-.75h3.75a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1-.75-.75ZM6 18a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75H6.75a.75.75 0 0 1-.75-.75V18Zm2.25 0a.75.75 0 0 1 .75-.75h3.75a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" class="ms-2 flex-1 text-start">Learning Plan</span>
                        <svg x-show="!sidebarCollapsed" :class="open ? 'rotate-90' : ''" class="h-4 w-4 transform transition-transform text-white/80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <ul x-cloak x-show="open" x-transition class="mt-2 space-y-1 ps-10" style="display:none;">
                        <li>
                            <a href="{{ route('learning.plans.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Plan Recommendations</a>
                        </li>
                        @role('Super Admin')
                        <li>
                            <a href="{{ route('learning.reviews.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Review Recommendations</a>
                        </li>
                        <li>
                            <a href="{{ route('learning.plans.history.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">History Recommendations</a>
                        </li>
                        @endrole
                    </ul>
                </li>
                @endif
                @endrole
                <!-- Learning -->
                <li x-data="{ open: false }" class="relative">
                    <button @click.prevent="open = !open" :aria-expanded="open.toString()" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path d="M11.25 4.533A9.707 9.707 0 0 0 6 3a9.735 9.735 0 0 0-3.25.555.75.75 0 0 0-.5.707v14.25a.75.75 0 0 0 1 .707A8.237 8.237 0 0 1 6 18.75c1.995 0 3.823.707 5.25 1.886V4.533ZM12.75 20.636A8.214 8.214 0 0 1 18 18.75c.966 0 1.89.166 2.75.47a.75.75 0 0 0 1-.708V4.262a.75.75 0 0 0-.5-.707A9.735 9.735 0 0 0 18 3a9.707 9.707 0 0 0-5.25 1.533v16.103Z" />
                            </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" class="ms-2 flex-1 text-start">Learning</span>
                        <svg x-show="!sidebarCollapsed" :class="open ? 'rotate-90' : ''" class="h-4 w-4 transform transition-transform text-white/80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <ul x-cloak x-show="open" x-transition class="mt-2 space-y-1 ps-10" style="display:none;">
                        <li>
                            <a href="{{ route('learning.logs.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">My Learning</a>
                        </li>
                        @can('view any learning log')
                        <li>
                            <a href="{{ route('learning.approvals.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Approvals</a>
                        </li>
                        @endcan
                        @can('view any learning platform')
                        <li>
                            <a href="{{ route('learning.platforms.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Platforms</a>
                        </li>
                        @endcan
                        @can('view any learning target')
                        <li>
                            <a href="{{ route('learning.targets.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Targets</a>
                        </li>
                        @endcan
                        @role('Super Admin|Admin')
                        <li>
                            <a href="{{ route('learning.reports.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Reports</a>
                        </li>
                        @endrole
                    </ul>
                </li>
                <!-- Struktural -->
                <li x-data="{ open: false }" class="relative">
                    <button @click.prevent="open = !open" :aria-expanded="open.toString()" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path d="M21 6.375c0 2.692-4.03 4.875-9 4.875S3 9.067 3 6.375 7.03 1.5 12 1.5s9 2.183 9 4.875Z" />
                                <path d="M12 12.75c2.685 0 5.19-.586 7.078-1.609a8.283 8.283 0 0 0 1.897-1.384c.016.121.025.244.025.368C21 12.817 16.97 15 12 15s-9-2.183-9-4.875c0-.124.009-.247.025-.368a8.285 8.285 0 0 0 1.897 1.384C6.809 12.164 9.315 12.75 12 12.75Z" />
                                <path d="M12 16.5c2.685 0 5.19-.586 7.078-1.609a8.282 8.282 0 0 0 1.897-1.384c.016.121.025.244.025.368 0 2.692-4.03 4.875-9 4.875s-9-2.183-9-4.875c0-.124.009-.247.025-.368a8.284 8.284 0 0 0 1.897 1.384C6.809 15.914 9.315 16.5 12 16.5Z" />
                                <path d="M12 20.25c2.685 0 5.19-.586 7.078-1.609a8.282 8.282 0 0 0 1.897-1.384c.016.121.025.244.025.368 0 2.692-4.03 4.875-9 4.875s-9-2.183-9-4.875c0-.124.009-.247.025-.368a8.284 8.284 0 0 0 1.897 1.384C6.809 19.664 9.315 20.25 12 20.25Z" />
                            </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" class="ms-2 flex-1 text-start">Struktural</span>
                        <!-- chevron shown only when sidebar not collapsed -->
                        <svg x-show="!sidebarCollapsed" :class="open ? 'rotate-90' : ''" class="h-4 w-4 transform transition-transform text-white/80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- submenu (desktop) -->
                    <ul x-cloak x-show="open" x-transition class="mt-2 space-y-1 ps-10" style="display:none;">
                        <li>
                            <a href="{{ route('direktorat.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Direktorat</a>
                        </li>
                        <li>
                            <a href="{{ route('divisi.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Divisi</a>
                        </li>
                        <li>
                            <a href="{{ route('unit.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Unit</a>
                        </li>
                    </ul>
                </li>
                <!-- Karyawan -->
                 <li x-data="{ open: false }" class="relative">
                    <button @click.prevent="open = !open" :aria-expanded="open.toString()" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM15.75 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM2.25 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM6.31 15.117A6.745 6.745 0 0 1 12 12a6.745 6.745 0 0 1 6.709 7.498.75.75 0 0 1-.372.568A12.696 12.696 0 0 1 12 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 0 1-.372-.568 6.787 6.787 0 0 1 1.019-4.38Z" clip-rule="evenodd" />
                                <path d="M5.082 14.254a8.287 8.287 0 0 0-1.308 5.135 9.687 9.687 0 0 1-1.764-.44l-.115-.04a.563.563 0 0 1-.373-.487l-.01-.121a3.75 3.75 0 0 1 3.57-4.047ZM20.226 19.389a8.287 8.287 0 0 0-1.308-5.135 3.75 3.75 0 0 1 3.57 4.047l-.01.121a.563.563 0 0 1-.373.486l-.115.04c-.567.2-1.156.349-1.764.441Z" />
                            </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" class="ms-2 flex-1 text-start">Karyawan</span>
                        <!-- chevron shown only when sidebar not collapsed -->
                        <svg x-show="!sidebarCollapsed" :class="open ? 'rotate-90' : ''" class="h-4 w-4 transform transition-transform text-white/80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- submenu (desktop) -->
                    <ul x-cloak x-show="open" x-transition class="mt-2 space-y-1 ps-10" style="display:none;">
                        <li>
                            <a href="{{ route('jabatan.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Jabatan</a>
                        </li>
                        <li>
                            <a href="{{ route('posisi.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Posisi</a>
                        </li>
                        <li>
                            <a href="{{ route('karyawan.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Karyawan</a>
                        </li>
                        <li>
                            <a href="#" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Riwayat Karyawan</a>
                        </li>
                    </ul>
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
                           <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 w-12 object-contain" />
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
                            <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z" />
                                <path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z" />
                            </svg>
                            </span>
                            <span class="ms-2">Dashboard</span>
                        </a>
                    </li>
                    <li x-data="{ open: false }">
                        <button @click.prevent="open = !open" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10">
                            <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path d="M21 6.375c0 2.692-4.03 4.875-9 4.875S3 9.067 3 6.375 7.03 1.5 12 1.5s9 2.183 9 4.875Z" />
                                <path d="M12 12.75c2.685 0 5.19-.586 7.078-1.609a8.283 8.283 0 0 0 1.897-1.384c.016.121.025.244.025.368C21 12.817 16.97 15 12 15s-9-2.183-9-4.875c0-.124.009-.247.025-.368a8.285 8.285 0 0 0 1.897 1.384C6.809 12.164 9.315 12.75 12 12.75Z" />
                                <path d="M12 16.5c2.685 0 5.19-.586 7.078-1.609a8.282 8.282 0 0 0 1.897-1.384c.016.121.025.244.025.368 0 2.692-4.03 4.875-9 4.875s-9-2.183-9-4.875c0-.124.009-.247.025-.368a8.284 8.284 0 0 0 1.897 1.384C6.809 15.914 9.315 16.5 12 16.5Z" />
                                <path d="M12 20.25c2.685 0 5.19-.586 7.078-1.609a8.282 8.282 0 0 0 1.897-1.384c.016.121.025.244.025.368 0 2.692-4.03 4.875-9 4.875s-9-2.183-9-4.875c0-.124.009-.247.025-.368a8.284 8.284 0 0 0 1.897 1.384C6.809 19.664 9.315 20.25 12 20.25Z" />
                            </svg>
                            </span>
                            <span class="ms-2 flex-1 text-start">Struktural</span>
                            <svg :class="open ? 'rotate-90' : ''" class="h-4 w-4 transform transition-transform text-white/80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <ul x-cloak x-show="open" x-transition class="mt-2 space-y-1 ps-6" style="display:none;">
                            <li>
                                <a href="{{ route('direktorat.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10">Direktorat</a>
                            </li>
                            <li>
                                <a href="{{ route('divisi.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10">Divisi</a>
                            </li>
                            <li>
                                <a href="{{ route('unit.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10">Unit</a>
                            </li>
                        </ul>

                        <button @click.prevent="open = !open" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10">
                            <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM15.75 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM2.25 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM6.31 15.117A6.745 6.745 0 0 1 12 12a6.745 6.745 0 0 1 6.709 7.498.75.75 0 0 1-.372.568A12.696 12.696 0 0 1 12 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 0 1-.372-.568 6.787 6.787 0 0 1 1.019-4.38Z" clip-rule="evenodd" />
                                <path d="M5.082 14.254a8.287 8.287 0 0 0-1.308 5.135 9.687 9.687 0 0 1-1.764-.44l-.115-.04a.563.563 0 0 1-.373-.487l-.01-.121a3.75 3.75 0 0 1 3.57-4.047ZM20.226 19.389a8.287 8.287 0 0 0-1.308-5.135 3.75 3.75 0 0 1 3.57 4.047l-.01.121a.563.563 0 0 1-.373.486l-.115.04c-.567.2-1.156.349-1.764.441Z" />
                            </svg>
                            </span>
                            <span class="ms-2 flex-1 text-start">Karyawan</span>
                            <svg :class="open ? 'rotate-90' : ''" class="h-4 w-4 transform transition-transform text-white/80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <ul x-cloak x-show="open" x-transition class="mt-2 space-y-1 ps-6" style="display:none;">
                            <li>
                                <a href="{{ route('jabatan.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Jabatan</a>
                            </li>
                            <li>
                                <a href="{{ route('posisi.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Posisi</a>
                            </li>
                            <li>
                                <a href="{{ route('karyawan.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Karyawan</a>
                            </li>
                            <li>
                                <a href="#" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Riwayat Karyawan</a>
                            </li>
                        </ul>
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
