<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Avatar Upload Section --}}
        <div class="mt-6">
            <x-input-label for="avatar" :value="__('Profile Picture')" />

            {{-- Display Current Avatar --}}
            <div class="mt-2 mb-2">
                @if ($user->profile?->avatar_path)
                    <img src="{{ Storage::url($user->profile->avatar_path) }}" alt="{{ $user->name }}" class="rounded-full h-40 w-40 object-cover">
                @else
                    {{-- Optional: Display a default avatar --}}
                    <span class="inline-block h-20 w-20 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                    <svg class="h-full w-full text-gray-300 dark:text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    </span>
                @endif
            </div>

            <input id="avatar" name="avatar" type="file" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">PNG, JPG, GIF, WEBP (MAX. 2MB).</p>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        {{-- Bio --}}
        <div>
            <x-input-label for="bio" :value="__('Bio')" />
            <textarea id="bio" name="bio" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('bio', $user->profile?->bio) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        {{-- Phone Number --}}
        <div>
            <x-input-label for="phone_number" :value="__('Phone Number')" />
            <x-text-input id="phone_number" name="phone_number" type="tel" class="mt-1 block w-full" :value="old('phone_number', $user->profile?->phone_number)" />
            <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
        </div>

        {{-- Address Fields --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="address_line_1" :value="__('Address Line 1')" />
                <x-text-input id="address_line_1" name="address_line_1" type="text" class="mt-1 block w-full" :value="old('address_line_1', $user->profile?->address_line_1)" />
                <x-input-error class="mt-2" :messages="$errors->get('address_line_1')" />
            </div>
            <div>
                <x-input-label for="address_line_2" :value="__('Address Line 2 (Optional)')" />
                <x-text-input id="address_line_2" name="address_line_2" type="text" class="mt-1 block w-full" :value="old('address_line_2', $user->profile?->address_line_2)" />
                <x-input-error class="mt-2" :messages="$errors->get('address_line_2')" />
            </div>
            <div>
                <x-input-label for="city" :value="__('City')" />
                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $user->profile?->city)" />
                <x-input-error class="mt-2" :messages="$errors->get('city')" />
            </div>
            <div>
                <x-input-label for="state" :value="__('State / Province')" />
                <x-text-input id="state" name="state" type="text" class="mt-1 block w-full" :value="old('state', $user->profile?->state)" />
                <x-input-error class="mt-2" :messages="$errors->get('state')" />
            </div>
            <div>
                <x-input-label for="postal_code" :value="__('Postal / Zip Code')" />
                <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full" :value="old('postal_code', $user->profile?->postal_code)" />
                <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
            </div>
            <div>
                <x-input-label for="country" :value="__('Country')" />
                <x-text-input id="country" name="country" type="text" class="mt-1 block w-full" :value="old('country', $user->profile?->country)" />
                <x-input-error class="mt-2" :messages="$errors->get('country')" />
            </div>
        </div>

        {{-- Role Specific Section --}}
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-300 dark:text-gray-700">
                {{ __('Role Specific Information') }}
            </h3>

            @hasrole('Student')
                <div class="mt-4 space-y-4">
                    {{-- Student ID --}}
                    <div>
                        <x-input-label for="student_id_number" :value="__('Student ID')" />
                        <x-text-input id="student_id_number" name="student_id_number" type="text" class="mt-1 block w-full" :value="old('student_id_number', $user->studentDetail?->student_id_number)" :disabled="!is_null($user->studentDetail?->student_id_number)" /> {{-- Optional: Disable if already set? --}}
                        <x-input-error class="mt-2" :messages="$errors->get('student_id_number')" />
                    </div>
                    {{-- Enrollment Date --}}
                    <div>
                        <x-input-label for="enrollment_date" :value="__('Enrollment Date')" />
                        <x-text-input id="enrollment_date" name="enrollment_date" type="date" class="mt-1 block w-full" :value="old('enrollment_date', $user->studentDetail?->enrollment_date)" />
                        <x-input-error class="mt-2" :messages="$errors->get('enrollment_date')" />
                    </div>
                    {{-- Major --}}
                    <div>
                        <x-input-label for="major" :value="__('Major / Program')" />
                        <x-text-input id="major" name="major" type="text" class="mt-1 block w-full" :value="old('major', $user->studentDetail?->major)" />
                        <x-input-error class="mt-2" :messages="$errors->get('major')" />
                    </div>
                </div>
            @endhasrole

            @hasrole('Teacher')
                <div class="mt-4 space-y-4">
                    {{-- Employee ID --}}
                    <div>
                        <x-input-label for="employee_id_number" :value="__('Employee ID')" />
                        <x-text-input id="employee_id_number" name="employee_id_number" type="text" class="mt-1 block w-full" :value="old('employee_id_number', $user->teacherDetail?->employee_id_number)" :disabled="!is_null($user->teacherDetail?->employee_id_number)" /> {{-- Optional: Disable if already set? --}}
                        <x-input-error class="mt-2" :messages="$errors->get('employee_id_number')" />
                    </div>
                    {{-- Qualification --}}
                    <div>
                        <x-input-label for="qualification" :value="__('Qualification')" />
                        <x-text-input id="qualification" name="qualification" type="text" class="mt-1 block w-full" :value="old('qualification', $user->teacherDetail?->qualification)" />
                        <x-input-error class="mt-2" :messages="$errors->get('qualification')" />
                    </div>
                    {{-- Department --}}
                    <div>
                        <x-input-label for="department" :value="__('Department')" />
                        <x-text-input id="department" name="department" type="text" class="mt-1 block w-full" :value="old('department', $user->teacherDetail?->department)" />
                        <x-input-error class="mt-2" :messages="$errors->get('department')" />
                    </div>

                    {{-- Teacher Skills/Specializations --}}
                    @if(!empty($allSkills) && $allSkills->count() > 0) {{-- Check if $allSkills is passed and not empty --}}
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <x-input-label :value="__('My Skills/Specializations')" class="mb-2 font-semibold"/>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach ($allSkills as $skill)
                                <label for="skill_{{ $skill->id }}" class="flex items-center">
                                    <input id="skill_{{ $skill->id }}"
                                        name="skills[]" {{-- Name as array for multiple selections --}}
                                        type="checkbox"
                                        value="{{ $skill->id }}"
                                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                        {{-- Check the box if this skill ID is in $userSkillIds OR in old input --}}
                                        @checked(in_array($skill->id, old('skills', $userSkillIds ?? [])))
                                        >
                                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ $skill->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('skills')" />
                        <x-input-error class="mt-2" :messages="$errors->get('skills.*')" />
                    </div>
                    @endif
                </div>
            @endhasrole

            {{-- Message if no role-specific fields shown --}}
            @if(!$user->hasRole('Student') && !$user->hasRole('Teacher'))
                <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('No role-specific information available for your account type.') }}
                </p>
            @endif
        </div>
        
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
