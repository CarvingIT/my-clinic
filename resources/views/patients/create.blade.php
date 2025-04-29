<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.add_new_patient') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-lg p-6 border border-gray-300">
                <form method="POST" action="{{ route('patients.store') }}" class="space-y-6">
                    @csrf

                    <!-- Form Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Patient ID -->
                        {{-- <div>
                            <x-input-label for="patient_id" :value="__('messages.Patient ID')" />
                            <x-text-input id="patient_id"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="text" name="patient_id" autocomplete="off" />
                            <x-input-error :messages="$errors->get('patient_id')" class="mt-1" />
                        </div> --}}

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('messages.name')" />
                            <x-text-input id="name"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="text" name="name" :value="old('name')" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <!-- Birthdate Field -->
                        <div>
                            <x-input-label for="birthdate" :value="__('Birthdate')" />
                            <x-text-input id="birthdate"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="date" name="birthdate"
                                value="{{ old('birthdate', $patient->birthdate ?? '') }}"
                                oninput="calculateAgeFromBirthdate()" />
                            <x-input-error :messages="$errors->get('birthdate')" class="mt-1" />
                        </div>

                        <!-- Age Field -->
                        <div>
                            <x-input-label for="age" :value="__('Age')" />
                            <x-text-input id="age"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2 reverse-transliteration"
                                type="text" name="age" placeholder="Enter Age (If no birthdate)"
                                oninput="calculateBirthdateFromAge()" />
                        </div>




                        <!-- Mobile Phone -->
                        <div>
                            <x-input-label for="mobile_phone" :value="__('messages.mobile_phone')" />
                            <x-text-input id="mobile_phone"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2 reverse-transliteration"
                                type="text" name="mobile_phone" :value="old('mobile_phone')" required />
                            <x-input-error :messages="$errors->get('mobile_phone')" class="mt-1" />
                        </div>

                        <!-- Email ID -->
                        <div>
                            <x-input-label for="email_id" :value="__('messages.Email ID')" />
                            <x-text-input id="email_id"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="email" name="email_id" :value="old('email_id')" />
                            <x-input-error :messages="$errors->get('email_id')" class="mt-1" />
                        </div>

                        <!-- Gender -->
                        <div>
                            <x-input-label for="gender" :value="__('messages.Gender')" />
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center space-x-1">
                                    <input type="radio" id="male" name="gender" value="M"
                                        class="border-gray-400 focus:ring-0 focus:border-gray-500">
                                    <span>Male</span>
                                </label>
                                <label class="flex items-center space-x-1">
                                    <input type="radio" id="female" name="gender" value="F"
                                        class="border-gray-400 focus:ring-0 focus:border-gray-500">
                                    <span>Female</span>
                                </label>
                                <label class="flex items-center space-x-1">
                                    <input type="radio" id="other" name="gender" value="O"
                                        class="border-gray-400 focus:ring-0 focus:border-gray-500">
                                    <span>Other</span>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('gender')" class="mt-1" />
                        </div>


                        <!-- Address -->
                        <div>
                            <x-input-label for="address" :value="__('messages.address')" />
                            <x-text-input id="address"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="text" name="address" :value="old('address')" required />
                            <x-input-error :messages="$errors->get('address')" class="mt-1" />
                        </div>
                        <!-- Occupation -->
                        <div>
                            <x-input-label for="job" :value="__('messages.occupation')" />
                            <x-text-input id="job"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="text" name="occupation" :value="old('occupation')" />
                            <x-input-error :messages="$errors->get('occupation')" class="mt-1" />
                        </div>
                        <!-- Reference -->
                        <div>
                            <x-input-label for="reference" :value="__('messages.reference')" />
                            <x-text-input id="reference"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="text" name="reference" :value="old('reference')" />
                            <x-input-error :messages="$errors->get('reference')" class="mt-1" />
                        </div>


                        <!-- Height -->
                        <div>
                            <x-input-label for="height" :value="__('messages.Height')" />
                            <x-text-input id="height"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2 reverse-transliteration"
                                type="text" step="0.01" name="height" :value="old('height')" />
                            <x-input-error :messages="$errors->get('height')" class="mt-1" />
                        </div>

                        <!-- Weight -->
                        <div>
                            <x-input-label for="weight" :value="__('messages.Weight')" />
                            <x-text-input id="weight"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2 reverse-transliteration"
                                type="text" step="0.01" name="weight" :value="old('weight')" />
                            <x-input-error :messages="$errors->get('weight')" class="mt-1" />
                        </div>
                        <!-- Vishesh -->
                        <div>
                            <x-input-label for="vishesh" :value="__('messages.Vishesh')" />
                            <textarea id="vishesh" name="vishesh"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                            ></textarea>
                            {{--
                            <x-text-input id="vishesh"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="text" name="vishesh" :value="old('vishesh')" />
                            --}}
                            <x-input-error :messages="$errors->get('vishesh')" class="mt-1" />
                        </div>


                        <!-- Occupation -->
                        {{-- <div>
                            <x-input-label for="occupation" :value="__('messages.occupation')" />
                            <x-text-input id="occupation"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="text" name="occupation" :value="old('occupation')" />
                            <x-input-error :messages="$errors->get('occupation')" class="mt-1" />
                        </div> --}}

                        <!-- Remark -->
                        {{-- <div>
                            <x-input-label for="remark" :value="__('messages.remark')" />
                            <x-text-input id="remark"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="text" name="remark" :value="old('remark')" />
                            <x-input-error :messages="$errors->get('remark')" class="mt-1" />
                        </div> --}}

                        <!-- Balance -->
                        {{-- <div>
                            <x-input-label for="balance" :value="__('messages.Balance')" />
                            <x-text-input id="balance"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="number" name="balance" :value="old('balance')" />
                            <x-input-error :messages="$errors->get('balance')" class="mt-1" />
                        </div> --}}
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-4 pt-4 border-t border-gray-200 mt-4">
                        <a href="{{ route('patients.index') }}">
                            <x-secondary-button class="text-gray-700 hover:bg-gray-100">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                        </a>
                        <x-primary-button class="bg-blue-600 hover:bg-blue-700 text-white">
                            {{ __('Add Patient') }}
                        </x-primary-button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function calculateAgeFromBirthdate() {
        let birthdate = document.getElementById('birthdate').value;
        if (birthdate) {
            let birthYear = new Date(birthdate).getFullYear();
            let currentYear = new Date().getFullYear();
            let age = currentYear - birthYear;
            document.getElementById('age').value = age; // Auto-fill age
        }
    }

    function calculateBirthdateFromAge() {
        let age = document.getElementById('age').value;
        if (age) {
            let currentYear = new Date().getFullYear();
            let birthYear = currentYear - age;
            let birthdate = `${birthYear}-01-01`; // Default to Jan 1st
            document.getElementById('birthdate').value = birthdate;
        }
    }
</script>


