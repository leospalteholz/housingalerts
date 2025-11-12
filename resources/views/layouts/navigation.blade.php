@php
    $subscriber = auth('subscriber')->user();
    $adminUser = auth()->user();
    $displayUser = $subscriber ?: $adminUser;
@endphp

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ $subscriber ? route('subscriber.dashboard') : orgRoute('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    @if($subscriber)
                        <x-nav-link :href="route('subscriber.dashboard')" :active="request()->routeIs('subscriber.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="orgRoute('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        @if($adminUser && $adminUser->is_superuser)
                            <x-nav-link :href="route('organizations.index')" :active="request()->routeIs('organizations.*')">
                                {{ __('Organizations') }}
                            </x-nav-link>
                        @endif
                        @if($adminUser && $adminUser->is_admin)
                            <x-nav-link :href="orgRoute('hearings.index')" :active="request()->routeIs('hearings.*')">
                                {{ __('Hearings') }}
                            </x-nav-link>
                            <x-nav-link :href="orgRoute('hearing-votes.index')" :active="request()->routeIs('hearing-votes.*')">
                                {{ __('Votes') }}
                            </x-nav-link>
                            <x-nav-link :href="orgRoute('users.index')" :active="request()->routeIs('users.*')">
                                {{ __('Users') }}
                            </x-nav-link>
                            <x-nav-link :href="orgRoute('subscribers.index')" :active="request()->routeIs('subscribers.*')">
                                {{ __('Subscribers') }}
                            </x-nav-link>
                            <x-nav-link :href="orgRoute('regions.index')" :active="request()->routeIs('regions.*')">
                                {{ __('Regions') }}
                            </x-nav-link>
                            <x-nav-link :href="orgRoute('councillors.index')" :active="request()->routeIs('councillors.*')">
                                {{ __('Councillors') }}
                            </x-nav-link>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            @if($displayUser)
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ $displayUser->name ?? $displayUser->email }}</div>

                            <div class="ml-1">
                                <x-icon name="chevron-down" class="fill-current h-4 w-4" />
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if(!$subscriber)
                            <x-dropdown-link :href="orgRoute('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                        @endif

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
            @endif

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <span :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex">
                        <x-icon name="menu" class="h-6 w-6" />
                    </span>
                    <span :class="{'hidden': ! open, 'inline-flex': open }" class="hidden">
                        <x-icon name="x" class="h-6 w-6" />
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if($subscriber)
                <x-responsive-nav-link :href="route('subscriber.dashboard')" :active="request()->routeIs('subscriber.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="orgRoute('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                @if($adminUser && $adminUser->is_superuser)
                    <x-responsive-nav-link :href="route('organizations.index')" :active="request()->routeIs('organizations.*')">
                        {{ __('Organizations') }}
                    </x-responsive-nav-link>
                @endif
                @if($adminUser && $adminUser->is_admin)
                    <x-responsive-nav-link :href="orgRoute('hearings.index')" :active="request()->routeIs('hearings.*')">
                        {{ __('Hearings') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="orgRoute('hearing-votes.index')" :active="request()->routeIs('hearing-votes.*')">
                        {{ __('Votes') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="orgRoute('users.index')" :active="request()->routeIs('users.*')">
                        {{ __('Users') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="orgRoute('subscribers.index')" :active="request()->routeIs('subscribers.*')">
                        {{ __('Subscribers') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="orgRoute('regions.index')" :active="request()->routeIs('regions.*')">
                        {{ __('Regions') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="orgRoute('councillors.index')" :active="request()->routeIs('councillors.*')">
                        {{ __('Councillors') }}
                    </x-responsive-nav-link>
                @endif
            @endif
        </div>

        <!-- Responsive Settings Options -->
        @if($displayUser)
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ $displayUser->name ?? $displayUser->email }}</div>
                <div class="font-medium text-sm text-gray-500">{{ $displayUser->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                @if(!$subscriber)
                    <x-responsive-nav-link :href="orgRoute('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                @endif

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
        @endif
    </div>
</nav>
