<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Patient') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('patients.update', $patient->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('messages.name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                :value="$patient->name" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Address -->
                        <div class="mt-4">
                            <x-input-label for="address" :value="__('messages.address')" />
                            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address"
                                :value="$patient->address" required />
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <!-- Occupation -->
                        <div class="mt-4">
                            <x-input-label for="occupation" :value="__('messages.occupation')" />
                            <x-text-input id="occupation" class="block mt-1 w-full" type="text" name="occupation"
                                :value="$patient->occupation" required />
                            <x-input-error :messages="$errors->get('occupation')" class="mt-2" />
                        </div>

                        <!-- Mobile Phone -->
                        <div class="mt-4">
                            <x-input-label for="mobile_phone" :value="__('messages.mobile_phone')" />
                            <x-text-input id="mobile_phone" class="block mt-1 w-full" type="text" name="mobile_phone"
                                :value="$patient->mobile_phone" required />
                            <x-input-error :messages="$errors->get('mobile_phone')" class="mt-2" />
                        </div>

                        <!-- Remark -->
                        <div class="mt-4">
                            <x-input-label for="remark" :value="__('messages.remark')" />
                            <x-text-input id="remark" class="block mt-1 w-full" type="text" name="remark"
                                :value="$patient->remark" />
                            <x-input-error :messages="$errors->get('remark')" class="mt-2" />
                        </div>

                        <!-- Update Button -->
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Update Patient') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
