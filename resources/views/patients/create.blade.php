<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- {{ __('messages.add_new_patient') }} --}}
            {{ __('Add Follow Up') }} - {{ $patient->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('patients.store') }}">
                        @csrf

                        <!-- Name -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('messages.name')" />
                            <x-text-input id="name" class="block mt-1 w-full border border-gray-300 rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" name="name"
                                :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Address -->
                        <div class="mb-4">
                            <x-input-label for="address" :value="__('messages.address')" />
                            <x-text-input id="address" class="block mt-1 w-full border border-gray-300 rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" name="address"
                                :value="old('address')" required />
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <!-- Occupation -->
                        <div class="mb-4">
                            <x-input-label for="occupation" :value="__('messages.occupation')" />
                            <x-text-input id="occupation" class="block mt-1 w-full border border-gray-300 rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" name="occupation"
                                :value="old('occupation')" required />
                            <x-input-error :messages="$errors->get('occupation')" class="mt-2" />
                        </div>

                        <!-- Mobile Phone -->
                        <div class="mb-4">
                            <x-input-label for="mobile_phone" :value="__('messages.mobile_phone')" />
                            <x-text-input id="mobile_phone" class="block mt-1 w-full border border-gray-300 rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" name="mobile_phone"
                                :value="old('mobile_phone')" required />
                            <x-input-error :messages="$errors->get('mobile_phone')" class="mt-2" />
                        </div>

                        <!-- Remark -->
                        <div class="mb-4">
                            <x-input-label for="remark" :value="__('messages.remark')" />
                            <x-text-input id="remark" class="block mt-1 w-full border border-gray-300 rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" name="remark"
                                :value="old('remark')" />
                            <x-input-error :messages="$errors->get('remark')" class="mt-2" />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                                {{ __('Add Patient') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
