<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit User') }}: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-6">
                        @csrf
                        @method('PUT') {{-- Use PUT method for updates --}}

                        {{-- Name --}}
                        <div>
                            <x-input-label for="name" :value="__('Name')" class="dark:text-gray-400"/>
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full dark:!text-gray-900" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        {{-- Email --}}
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full dark:!text-gray-900" :value="old('email', $user->email)" required autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        {{-- Password Note --}}
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Password cannot be changed directly here. Users should use the "Forgot Password" link if needed.') }}
                        </div>


                        {{-- Roles Checkboxes --}}
                        <div>
                            <x-input-label :value="__('Assign Roles')" class="mb-2"/>
                            <div class="space-y-2">
                                @foreach ($roles as $id => $name)
                                    <label for="role_{{ $id }}" class="flex items-center">
                                        <input id="role_{{ $id }}"
                                               name="roles[]"
                                               type="checkbox"
                                               value="{{ $id }}"
                                               class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                               {{-- Check the box if user HAS this role OR if validation failed and it was previously checked --}}
                                               @checked(in_array($id, old('roles', $user->roles->pluck('id')->toArray())))
                                               >
                                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ $name }}</span>
                                    </label>
                                @endforeach
                            </div>
                             <x-input-error class="mt-2" :messages="$errors->get('roles')" />
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <x-input-label for="division_id" :value="__('Assign to Division (Optional - for internal staff)')" class="mb-2 font-semibold"/>
                            <select id="division_id" name="division_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">-- No Division --</option>
                                @foreach ($divisions as $id => $name)
                                    <option value="{{ $id }}" @selected(old('division_id', $user->division_id) == $id)>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('division_id')" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Changing division is typically for internal roles. Students (external customers) usually don't belong to these internal divisions.
                            </p>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Update User') }}</x-primary-button>
                             <a href="{{ route('admin.users.index') }}" class="text-gray-600 dark:text-gray-400 hover:underline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>