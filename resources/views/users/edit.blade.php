<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-700">
                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name"
                                class="block mt-1 w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none py-1 px-3 text-base"
                                type="text" name="name" :value="$user->name" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500" />
                        </div>

                        <!-- Email Address -->
                        <div class="mb-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email"
                                class="block mt-1 w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none py-1 px-3 text-base"
                                type="email" name="email" :value="$user->email" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password"
                                class="block mt-1 w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none py-1 px-3 text-base"
                                type="password" name="password" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="password_confirmation"
                                class="block mt-1 w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none py-1 px-3 text-base"
                                type="password" name="password_confirmation" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500" />
                        </div>

                        <!-- Roles -->
                        <div class="mb-4">
                            <x-input-label for="roles" :value="__('Roles')" />
                            <select id="roles" name="roles[]" multiple
                                class="block mt-1 w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none py-1 px-3 text-base">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}"
                                        {{ in_array($role->name, $user->roles()->pluck('name')->toArray()) ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('roles')" class="mt-2 text-red-500" />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end mt-4">

                            <!-- Cancel Button -->
                            <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-900">
                                <x-secondary-button class="ml-4">
                                    {{ __('Cancel') }}
                                </x-secondary-button>
                            </a>

                            <x-primary-button
                                class="bg-blue-500 hover:bg-blue-600 text-white py-1 px-5 rounded-md focus:ring focus:ring-blue-300 focus:outline-none text-base ml-4">
                                {{ __('Update User') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
