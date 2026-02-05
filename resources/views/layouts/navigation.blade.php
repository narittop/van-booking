<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <!-- <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg> -->
                           <img src="{{ asset('image/logorus.png') }}" alt="Logo" class="h-12 w-8">
                        <span class="ml-2 text-lg font-semibold text-gray-800">ระบบขอใช้รถราชการ มทร.สุวรรณภูมิ</span>
                     
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @php
                        $viewMode = session('super_admin_view_mode', 'admin');
                        $isSuperAdmin = Auth::user()->isSuperAdmin();
                    @endphp

                    @if($isSuperAdmin)
                        {{-- Show menus based on selected view mode --}}
                        @if($viewMode === 'admin')
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                แดชบอร์ด
                            </x-nav-link>
                            <x-nav-link :href="route('admin.bookings')" :active="request()->routeIs('admin.bookings*')">
                                จัดการคำขอ
                            </x-nav-link>
                            <x-nav-link :href="route('admin.vans.index')" :active="request()->routeIs('admin.vans*')">
                                จัดการรถตู้
                            </x-nav-link>
                            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users*')">
                                จัดการสิทธิ์
                            </x-nav-link>
                            <x-nav-link :href="route('director.bookings')" :active="request()->routeIs('director.bookings*')">
                                อนุมัติคำขอ
                            </x-nav-link>
                        @elseif($viewMode === 'director')
                            <x-nav-link :href="route('director.dashboard')" :active="request()->routeIs('director.dashboard')">
                                แดชบอร์ด
                            </x-nav-link>
                            <x-nav-link :href="route('director.bookings')" :active="request()->routeIs('director.bookings*')">
                                อนุมัติคำขอ
                            </x-nav-link>
                        @endif
                        <span class="border-l border-gray-300 h-6 self-center mx-2"></span>
                        <x-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.index')">
                            การใช้รถของฉัน
                        </x-nav-link>
                        <x-nav-link :href="route('bookings.create')" :active="request()->routeIs('bookings.create')">
                            ขอใช้รถ
                        </x-nav-link>
                    @elseif(Auth::user()->isAdmin())
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            แดชบอร์ด
                        </x-nav-link>
                        <x-nav-link :href="route('admin.bookings')" :active="request()->routeIs('admin.bookings*')">
                            จัดการคำขอ
                        </x-nav-link>
                        <x-nav-link :href="route('admin.vans.index')" :active="request()->routeIs('admin.vans*')">
                            จัดการรถตู้
                        </x-nav-link>
                        <span class="border-l border-gray-300 h-6 self-center mx-2"></span>
                        <x-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.index')">
                            การใช้รถของฉัน
                        </x-nav-link>
                        <x-nav-link :href="route('bookings.create')" :active="request()->routeIs('bookings.create')">
                            ขอใช้รถ
                        </x-nav-link>
                    @elseif(Auth::user()->isDirector())
                        <x-nav-link :href="route('director.dashboard')" :active="request()->routeIs('director.dashboard')">
                            แดชบอร์ด
                        </x-nav-link>
                        <x-nav-link :href="route('director.bookings')" :active="request()->routeIs('director.bookings*')">
                            อนุมัติคำขอ
                        </x-nav-link>
                        <span class="border-l border-gray-300 h-6 self-center mx-2"></span>
                        <x-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.index')">
                            การใช้รถของฉัน
                        </x-nav-link>
                        <x-nav-link :href="route('bookings.create')" :active="request()->routeIs('bookings.create')">
                            ขอใช้รถ
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.index')">
                            การใช้รถของฉัน
                        </x-nav-link>
                        <x-nav-link :href="route('bookings.create')" :active="request()->routeIs('bookings.create')">
                            ขอใช้รถ
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center">
                                @if(isset($navHrdPerson) && $navHrdPerson && $navHrdPerson->person_picture)
                                    <img class="h-8 w-8 rounded-full object-cover border-2 border-gray-200 mr-2" 
                                         src="https://hrd.rmutsb.ac.th/upload/his/person/photo/{{ $navHrdPerson->person_picture }}" 
                                         alt="{{ Auth::user()->name }}"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <span class="hidden h-8 w-8 rounded-full bg-indigo-100 items-center justify-center mr-2">
                                        <span class="text-sm font-medium text-indigo-600">{{ mb_substr(Auth::user()->name, 0, 1) }}</span>
                                    </span>
                                @elseif(Auth::user()->isAdmin())
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-indigo-100 rounded-full mr-2">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full mr-2">
                                        <span class="text-sm font-medium text-gray-600">{{ mb_substr(Auth::user()->name, 0, 1) }}</span>
                                    </span>
                                @endif
                                {{ Auth::user()->name }}
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            โปรไฟล์
                        </x-dropdown-link>

                        {{-- Role Switcher for Super Admin --}}
                        @if(Auth::user()->isSuperAdmin())
                            <div class="border-t border-gray-100 my-1"></div>
                            <div class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wider">สลับมุมมอง</div>
                            <x-dropdown-link :href="route('switch.view.mode', 'admin')" class="{{ session('super_admin_view_mode', 'admin') === 'admin' ? 'bg-indigo-50 text-indigo-700' : '' }}">
                                🛡️ Admin
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('switch.view.mode', 'director')" class="{{ session('super_admin_view_mode', 'admin') === 'director' ? 'bg-indigo-50 text-indigo-700' : '' }}">
                                👔 ผู้อำนวยการ
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('switch.view.mode', 'user')" class="{{ session('super_admin_view_mode', 'admin') === 'user' ? 'bg-indigo-50 text-indigo-700' : '' }}">
                                👤 ผู้ใช้ทั่วไป
                            </x-dropdown-link>
                            <div class="border-t border-gray-100 my-1"></div>
                        @endif

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                ออกจากระบบ
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(Auth::user()->isAdmin())
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    แดชบอร์ด
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.bookings')" :active="request()->routeIs('admin.bookings*')">
                    จัดการคำขอ
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.vans.index')" :active="request()->routeIs('admin.vans*')">
                    จัดการรถตู้
                </x-responsive-nav-link>
                @if(Auth::user()->isSuperAdmin())
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users*')">
                    จัดการสิทธิ์
                </x-responsive-nav-link>
                @endif
                <div class="border-t border-gray-200 my-2"></div>
                <x-responsive-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.index')">
                    การจองของฉัน
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('bookings.create')" :active="request()->routeIs('bookings.create')">
                    ขอจองรถ
                </x-responsive-nav-link>
            @elseif(Auth::user()->isDirector())
                <x-responsive-nav-link :href="route('director.dashboard')" :active="request()->routeIs('director.dashboard')">
                    แดชบอร์ด
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('director.bookings')" :active="request()->routeIs('director.bookings*')">
                    อนุมัติคำขอ
                </x-responsive-nav-link>
                <div class="border-t border-gray-200 my-2"></div>
                <x-responsive-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.index')">
                    การจองของฉัน
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('bookings.create')" :active="request()->routeIs('bookings.create')">
                    ขอจองรถ
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.index')">
                    การจองของฉัน
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('bookings.create')" :active="request()->routeIs('bookings.create')">
                    ขอจองรถ
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    โปรไฟล์
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        ออกจากระบบ
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

