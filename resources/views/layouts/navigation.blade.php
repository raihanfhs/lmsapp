<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @hasrole('Admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Admin Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('Manage Users') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.divisions.index')" :active="request()->routeIs('admin.divisions.*')"> {{-- <-- ADD THIS --}}
                            {{ __('Manage Divisions') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.skills.index')" :active="request()->routeIs('admin.skills.*')"> {{-- <-- ADD THIS --}}
                            {{ __('Manage Skills') }}
                        </x-nav-link>
                        {{-- Add other admin links later --}}
                    @endhasrole

                    @hasrole('Pengelola')
                        <x-nav-link :href="route('pengelola.dashboard')" :active="request()->routeIs('pengelola.dashboard')">
                            {{ __('Pengelola Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('pengelola.teacher.engagement')" :active="request()->routeIs('pengelola.teacher.engagement')">
                            {{ __('Teacher Engagement') }}
                        </x-nav-link>
                        <x-nav-link :href="route('pengelola.courses.index')" :active="request()->routeIs('pengelola.courses.*')">
                            {{ __('Manage Courses') }}
                        </x-nav-link>
                        <x-nav-link :href="route('pengelola.learning-paths.index')" :active="request()->routeIs('pengelola.learning-paths.*')">
                            {{ __('Learning Paths') }}
                        </x-nav-link>
                        {{-- You might add "Assign Teachers" here too, or access it via the courses list --}}
                    @endhasrole

                    {{-- Chief --}}
                    @if (Auth::user()->role == 'chief')
                        <x-nav-link :href="route('chief.dashboard')" :active="request()->routeIs('chief.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @endif

                    @hasrole('Teacher')
                        <x-nav-link :href="route('teacher.dashboard')" :active="request()->routeIs('teacher.dashboard')">
                            {{ __('Teacher Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('teacher.courses.index')" :active="request()->routeIs('teacher.courses.*')">
                            {{ __('My Courses') }}
                        </x-nav-link>
                        {{-- Add other teacher links later --}}
                    @endhasrole

                    @hasrole('Student')
                        <x-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')">
                            {{ __('Student Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.courses.index')" :active="request()->routeIs('student.courses.*')">
                            {{ __('Enrolled Courses') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.courses.browse')" :active="request()->routeIs('student.courses.browse')"> {{-- <-- ADD THIS --}}
                            {{ __('Browse Courses') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.learning-paths.index')" :active="request()->routeIs('student.learning-paths.index')">
                            {{ __('Learning Paths') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.quiz_attempts.history')" :active="request()->routeIs('student.quiz_attempts.history')">
                            {{ __('Quiz History') }}
                        </x-nav-link>
                    @endhasrole
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = ! open" class="relative p-2 text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($unreadNotifications->count() > 0)
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">
                                {{ $unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <div x-show="open"
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute z-50 mt-2 w-80 rounded-md shadow-lg end-0 origin-top-right"
                        style="display: none;">
                        <div class="rounded-md ring-1 ring-black ring-opacity-5 bg-white">
                            <div class="p-4 border-b">
                                <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
                            </div>
                            <div class="py-1">
                                @forelse ($unreadNotifications as $notification)
                                    <a href="{{ route('notifications.markAsRead', $notification->id) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100">
                                        <p class="font-medium">{{ $notification->data['message'] ?? 'New notification.' }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</p>
                                    </a>
                                @empty
                                    <div class="px-4 py-3 text-sm text-gray-500">
                                        No unread notifications.
                                    </div>
                                @endforelse
                                @if($unreadNotifications->count() > 0)
                                <div class="border-t border-gray-200 px-4 py-2">
                                    <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
                                        @csrf
                                        <button type="submit" class="text-sm text-blue-600 hover:underline w-full text-center">
                                            Mark all as read
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            @if (Auth::user()->profile?->avatar_path)
                                <img class="h-8 w-8 rounded-full object-cover me-2" src="{{ Storage::url(Auth::user()->profile->avatar_path) }}" alt="{{ Auth::user()->name }}">
                            @else
                                {{-- Optional: Default tiny icon if no avatar --}}
                                <span class="inline-block h-8 w-8 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700 me-2">
                                    <svg class="h-full w-full text-gray-300 dark:text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </span>
                            @endif
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
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
            @hasrole('Admin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('Admin Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    {{ __('Manage Users') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.divisions.index')" :active="request()->routeIs('admin.divisions.*')"> {{-- <-- ADD THIS --}}
                    {{ __('Manage Divisions') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.skills.index')" :active="request()->routeIs('admin.skills.*')"> {{-- <-- ADD THIS --}}
                    {{ __('Manage Skills') }}
                </x-responsive-nav-link>
            @endhasrole
            @hasrole('Pengelola')
                <x-responsive-nav-link :href="route('pengelola.dashboard')" :active="request()->routeIs('pengelola.dashboard')">
                    {{ __('Pengelola Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('pengelola.courses.index')" :active="request()->routeIs('pengelola.courses.*')">
                    {{ __('Manage Courses') }}
                </x-responsive-nav-link>
            @endhasrole
            @hasrole('Teacher')
                <x-responsive-nav-link :href="route('teacher.dashboard')" :active="request()->routeIs('teacher.dashboard')">
                    {{ __('Teacher Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('teacher.courses.index')" :active="request()->routeIs('teacher.courses.*')">
                    {{ __('My Courses') }}
                </x-responsive-nav-link>
            @endhasrole
            @hasrole('Student')
                <x-responsive-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')">
                    {{ __('Student Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.courses.index')" :active="request()->routeIs('student.courses.*')">
                    {{ __('Enrolled Courses') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.courses.browse')" :active="request()->routeIs('student.courses.browse')"> {{-- <-- ADD THIS --}}
                    {{ __('Browse Courses') }}
                </x-responsive-nav-link>
            @endhasrole 
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
