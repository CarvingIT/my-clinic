<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add Follow Up') }} - {{ $patient->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('followups.store') }}">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{$patient->id}}" />

                        <!-- Nadi Checkboxes -->
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">{{ __('नाडी') }}</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach(['vata' => 'वात', 'pitta' => 'पित्त', 'kapha' => 'कफ', 'sukshma' => 'सूक्ष्म', 'kothin' => 'कठीण', 'sam' => 'साम', 'prana' => 'प्राण', 'vyana' => 'व्यान', 'udana' => 'उदान', 'apana' => 'अपान', 'samana' => 'समान', 'rasa' => 'रस', 'rakta' => 'रक्त', 'ambakshaya' => 'अन्नक्षय'] as $id => $label)
                                    <div class="flex items-center">
                                        <input type="checkbox" id="{{ $id }}" name="{{ $id }}" value="1" class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md transition-all duration-300 hover:border-indigo-400" />
                                        <x-input-label for="{{ $id }}" :value="__($label)" class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Diagnosis Textarea -->
                        <div class="mt-4">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">{{ __('लक्षणे') }}</h2>
                            <textarea id="lakshane" name="diagnosis" rows="4" class="block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"></textarea>
                            <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />
                        </div>

                        <!-- Treatment Section -->
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">{{ __('चिकित्सा') }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                            @foreach(['arsh' => 'अर्श', 'grahni' => 'ग्रहणी', 'jwar' => 'ज्वर/प्रतिश्याय'] as $id => $label)
                                <div class="flex items-center">
                                    <input type="checkbox" id="{{ $id }}" name="{{ $id }}" value="1" class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md transition-all duration-300 hover:border-indigo-400" />
                                    <x-input-label for="{{ $id }}" :value="__($label)" class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                </div>
                            @endforeach
                        </div>

                        <!-- Text Inputs for Treatment Details -->
                        @foreach(['treatment' => 'Treatment', 'nidan' => 'निदान', 'upashay' => 'उपशय', 'salla' => 'सल्ला'] as $name => $label)
                            <div class="mt-4">
                                <x-input-label for="{{ $name }}" :value="__($label)" />
                                <x-text-input id="{{ $name }}" class="block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400" type="text" name="{{ $name }}" />
                                <x-input-error :messages="$errors->get($name)" class="mt-2" />
                            </div>
                        @endforeach

                        <!-- Amount and Balance Inputs -->
                        @foreach(['amount' => 'Amount', 'balance' => 'Balance'] as $name => $label)
                            <div class="mt-4">
                                <x-input-label for="{{ $name }}" :value="__($label)" />
                                <x-text-input id="{{ $name }}" class="block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400" type="number" name="{{ $name }}" />
                                <x-input-error :messages="$errors->get($name)" class="mt-2" />
                            </div>
                        @endforeach

                        <!-- Payment Method Dropdown -->
                        <div class="mt-4">
                            <x-input-label for="payment_method" :value="__('Payment Method')" />
                            <select id="payment_method" name="payment_method" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full transition-all duration-300 hover:border-indigo-400">
                                <option value="">Please Select</option>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="online">Online</option>
                            </select>
                            <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                        </div>

                        <!-- Certificate and Drawing Inputs -->
                        @foreach(['certificate' => 'Certificate', 'drawing' => 'Drawing'] as $name => $label)
                            <div class="mt-4">
                                <x-input-label for="{{ $name }}" :value="__($label)" />
                                <x-text-input id="{{ $name }}" class="block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400" type="text" name="{{ $name }}" />
                                <x-input-error :messages="$errors->get($name)" class="mt-2" />
                            </div>
                        @endforeach

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Add Follow Up') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
