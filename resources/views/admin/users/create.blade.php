<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                        @csrf

                        {{-- Name --}}
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full dark:!text-gray-900" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        {{-- Email --}}
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full dark:!text-gray-900" :value="old('email')" required autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        {{-- Password --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="password" :value="__('Password')" />
                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full dark:!text-gray-900" required autocomplete="new-password" />
                                <x-input-error class="mt-2" :messages="$errors->get('password')" />
                            </div>

                            {{-- Confirm Password --}}
                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                                <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                            </div>
                        </div>

                        {{-- Roles Checkboxes --}}
                        <div>
                            <x-input-label :value="__('Assign Roles')" />
                            <div class="mt-2 space-y-2">
                                @foreach ($roles as $id => $name)
                                    <label for="role_{{ $id }}" class="flex items-center">
                                        <input id="role_{{ $id }}" name="roles[]" type="checkbox" value="{{ $id }}" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ $name }}</span>
                                    </label>
                                @endforeach
                            </div>
                             <x-input-error class="mt-2" :messages="$errors->get('roles')" />
                        </div>
                        
                        {{-- Assign Division --}}
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <x-input-label for="division_id" :value="__('Assign to Division (Optional - for internal staff)')" class="mb-2 font-semibold"/>
                            <select id="division_id" name="division_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">-- No Division --</option> {{-- Allows not assigning a division --}}
                                @foreach ($divisions as $id => $name)
                                    <option value="{{ $id }}" @selected(old('division_id') == $id)>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('division_id')" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Assigning a division is typically for internal roles like Pengelola or Teacher. Students (external customers) usually don't belong to these internal divisions.
                            </p>
                        </div>

                        {{-- Activation Preference --}}
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700"> {{-- Added a top border for separation --}}
                            <x-input-label :value="__('Account Activation')" class="mb-2 font-semibold"/>
                            <div class="space-y-2">
                                <label for="activate_now" class="flex items-center">
                                    <input id="activate_now" name="activation_preference" type="radio" value="activate_now" @checked(old('activation_preference', 'activate_now') === 'activate_now') class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Activate account immediately (email verified)') }}</span>
                                </label>
                                <label for="send_verification" class="flex items-center">
                                    <input id="send_verification" name="activation_preference" type="radio" value="send_verification" @checked(old('activation_preference') === 'send_verification') class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Require email verification by user') }}</span>
                                </label>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('activation_preference')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Create User') }}</x-primary-button>
                             <a href="{{ route('admin.users.index') }}" class="text-gray-600 dark:text-gray-400 hover:underline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>