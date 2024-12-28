<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.add_new_patient') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg p-8 border border-gray-200">
                <form method="POST" action="{{ route('patients.store') }}">
                    @csrf

                    <!-- Form Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Patient ID -->
                        <div class="mt-4">
                            <x-input-label for="patient_id" :value="__('Patient ID')" />
                            <input id="patient_id" class="block mt-1 w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition duration-200 appearance-none px-4 py-2 placeholder:text-gray-400 placeholder:italic dark:bg-gray-700 dark:text-gray-100"  type="text" name="patient_id"   autocomplete="off" placeholder="Enter Patient ID"/>
                              <x-input-error :messages="$errors->get('patient_id')" class="mt-2" />
                         </div>

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('messages.name')" />
                            <x-text-input id="name"
                                class="block mt-1 w-full border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Birthdate -->
                        <div>
                            <x-input-label for="birthdate" :value="__('Birthdate')" />
                            <x-text-input id="birthdate"
                                class="block mt-1 w-full border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                type="date" name="birthdate" :value="old('birthdate')" />
                            <x-input-error :messages="$errors->get('birthdate')" class="mt-2" />
                        </div>

                        <!-- Gender -->
                        <div>
                            <x-input-label for="gender" :value="__('Gender')" />
                            <select id="gender" name="gender"
                                class="block mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Please Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                        </div>

                        <!-- Mobile Phone -->
                        <div>
                            <x-input-label for="mobile_phone" :value="__('messages.mobile_phone')" />
                            <x-text-input id="mobile_phone"
                                class="block mt-1 w-full border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                type="text" name="mobile_phone" :value="old('mobile_phone')" required />
                            <x-input-error :messages="$errors->get('mobile_phone')" class="mt-2" />
                        </div>

                        <!-- Email ID -->
                        <div>
                            <x-input-label for="email_id" :value="__('Email ID')" />
                            <x-text-input id="email_id"
                                class="block mt-1 w-full border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                type="email" name="email_id" :value="old('email_id')" />
                            <x-input-error :messages="$errors->get('email_id')" class="mt-2" />
                        </div>

                        <!-- Address -->
                        <div>
                            <x-input-label for="address" :value="__('messages.address')" />
                            <x-text-input id="address"
                                class="block mt-1 w-full border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                type="text" name="address" :value="old('address')" required />
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <!-- Vishesh -->
                        <div>
                            <x-input-label for="vishesh" :value="__('Vishesh')" />
                            <x-text-input id="vishesh"
                                class="block mt-1 w-full border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                type="text" name="vishesh" :value="old('vishesh')" />
                            <x-input-error :messages="$errors->get('vishesh')" class="mt-2" />
                        </div>

                        <!-- Occupation -->
                        <div>
                            <x-input-label for="occupation" :value="__('messages.occupation')" />
                            <x-text-input id="occupation"
                                class="block mt-1 w-full border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                type="text" name="occupation" :value="old('occupation')" required />
                            <x-input-error :messages="$errors->get('occupation')" class="mt-2" />
                        </div>

                        <!-- Remark -->
                        <div>
                            <x-input-label for="remark" :value="__('messages.remark')" />
                            <x-text-input id="remark"
                                class="block mt-1 w-full border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                type="text" name="remark" :value="old('remark')" />
                            <x-input-error :messages="$errors->get('remark')" class="mt-2" />
                        </div>

                        <!-- Balance -->
                        <div>
                            <x-input-label for="balance" :value="__('Balance')" />
                            <x-text-input id="balance"
                                class="block mt-1 w-full border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                type="number" name="balance" :value="old('balance')" />
                            <x-input-error :messages="$errors->get('balance')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end mt-6">
                        <a href="{{ route('patients.index') }}" class="mr-4">
                            <x-secondary-button class="text-gray-600 hover:text-gray-900">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                        </a>
                        <x-primary-button class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                            {{ __('Add Patient') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
