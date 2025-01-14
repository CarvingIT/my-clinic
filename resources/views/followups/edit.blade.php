<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Follow Up') }} - {{ $followup->patient->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('followups.update', $followup) }}">
                        @csrf
                        @method('PUT') <!-- Important for updates -->
                        <input type="hidden" name="patient_id" value="{{ $followup->patient->id }}" />

                        <!-- Nadi Checkboxes -->
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">{{ __('नाडी') }}
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                                @php
                                    $checkUpInfo = json_decode($followup->check_up_info, true);
                                @endphp

                                @foreach (['वात' => 'वात', 'पित्त' => 'पित्त', 'कफ' => 'कफ', 'सूक्ष्म' => 'सूक्ष्म', 'कठिन' => 'कठिन', 'साम' => 'साम', 'प्राण' => 'प्राण', 'व्यान' => 'व्यान', 'स्थूल' => 'स्थूल', 'तीक्ष्ण' => 'तीक्ष्ण', 'वेग' => 'वेग', 'अनियमित' => 'अनियमित'] as $id => $label)
                                    <div class="flex items-center">
                                        <input type="checkbox" id="{{ $id }}" name="{{ $id }}"
                                            value="1"
                                            {{ isset($checkUpInfo[$id]) && $checkUpInfo[$id] ? 'checked' : '' }}
                                            class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md transition-all duration-300 hover:border-indigo-400" />
                                        <x-input-label for="{{ $id }}" :value="__($label)"
                                            class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                    </div>
                                @endforeach
                            </div>

                        </div>

                        <!-- Diagnosis Textarea -->
                        <div class="mt-4 mb-4">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">{{ __('लक्षणे') }}
                            </h2>
                            <textarea id="lakshane" name="diagnosis" rows="4"
                                class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"> {{ $followup->diagnosis }}</textarea>
                            <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />
                        </div>

                        <!-- Treatment Section -->
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">{{ __('चिकित्सा') }}</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6 mt-2">
                                @foreach(['अर्श' => 'अर्श', 'ग्रहणी' => 'ग्रहणी', 'ज्वर/प्रतिश्याय' => 'ज्वर/प्रतिश्याय'] as $id => $label)
                                    <div class="flex items-center gap-2">
                                          <input type="hidden" name="{{ $id }}" value="0">
                                        <input type="checkbox" id="{{ $id }}" name="{{ $id }}" value="1" {{ isset($checkUpInfo[$id]) && $checkUpInfo[$id] == 1 ? 'checked' : '' }} class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md transition-all duration-300 hover:border-indigo-400" />
                                        <x-input-label for="{{ $id }}" :value="__($label)" class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                    </div>
                                @endforeach
                             </div>
                               <!-- Select Input for Chikitsa combos-->
                               <div class="mt-4">
                                    <x-input-label for="chikitsa_combo" :value="__('Add Combo')" class="mb-1" />
                                   <select id="chikitsa_combo" name="chikitsa_combo" class="block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full transition-all duration-300 hover:border-indigo-400">
                                           <option value="">Please Select</option>
                                           <option value="कटज, मुस्ता, विश्व महासुदर्शन, तालिसादी, बहेडी"  @if(isset($checkUpInfo['chikitsa_combo']) && $checkUpInfo['chikitsa_combo'] == 'कटज, मुस्ता, विश्व महासुदर्शन, तालिसादी, बहेडी' ) selected @endif >कटज, मुस्ता, विश्व महासुदर्शन, तालिसादी, बहेडी</option>
                                           <option value="विभीतकी, यष्टी, कुटकी, हरी, निंब, सारिवा, कुटकी"  @if(isset($checkUpInfo['chikitsa_combo']) && $checkUpInfo['chikitsa_combo'] == 'विभीतकी, यष्टी, कुटकी, हरी, निंब, सारिवा, कुटकी') selected @endif>विभीतकी, यष्टी, कुटकी, हरी, निंब, सारिवा, कुटकी</option>
                                          <option value="यष्टी, कुटकी, हरी,"  @if(isset($checkUpInfo['chikitsa_combo']) && $checkUpInfo['chikitsa_combo'] == 'यष्टी, कुटकी, हरी,') selected @endif >यष्टी, कुटकी, हरी</option>
                                     </select>
                                     <x-input-error :messages="$errors->get('chikitsa_combo')" class="mt-2" />
                                </div>

                        </div>


                        <!-- Remaining Input Fields -->
                        @php
                            $checkUpInfo = json_decode($followup->check_up_info, true);
                        @endphp
                        @foreach (['nidan' => 'निदान', 'upashay' => 'उपशय', 'salla' => 'सल्ला'] as $name => $label)
                            <div class="mt-4">
                                <x-input-label for="{{ $name }}" :value="__($label)" />
                                <x-text-input id="{{ $name }}"
                                    class="px-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"
                                    type="text" name="{{ $name }}"
                                    value="{{ $checkUpInfo[$name] ?? '' }}" />
                                <x-input-error :messages="$errors->get($name)" class="mt-2" />
                            </div>
                        @endforeach


                        @foreach (['amount' => 'Amount', 'balance' => 'Balance'] as $name => $label)
                            <div class="mt-4">
                                <x-input-label for="{{ $name }}" :value="__($label)" />
                                <x-text-input id="{{ $name }}"
                                    class="px-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"
                                    type="number" name="{{ $name }}"
                                    value="{{ $checkUpInfo[$name] ?? '' }}" />
                                <x-input-error :messages="$errors->get($name)" class="mt-2" />
                            </div>
                        @endforeach

                        <div class="mt-4">
                            <x-input-label for="payment_method" :value="__('Payment Method')" />
                            <select id="payment_method" name="payment_method"
                                class="border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full transition-all duration-300 hover:border-indigo-400">
                                <option value="">Please Select</option>
                                <option value="cash" @if (isset($checkUpInfo['payment_method']) && $checkUpInfo['payment_method'] == 'cash') selected @endif>Cash</option>
                                <option value="card" @if (isset($checkUpInfo['payment_method']) && $checkUpInfo['payment_method'] == 'card') selected @endif>Card</option>
                                <option value="online" @if (isset($checkUpInfo['payment_method']) && $checkUpInfo['payment_method'] == 'online') selected @endif>Online</option>
                            </select>
                            <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                        </div>

                        @foreach (['certificate' => 'Certificate', 'drawing' => 'Drawing'] as $name => $label)
                            <div class="mt-4">
                                <x-input-label for="{{ $name }}" :value="__($label)" />
                                <x-text-input id="{{ $name }}"
                                    class="px-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"
                                    type="text" name="{{ $name }}"
                                    value="{{ $checkUpInfo[$name] ?? '' }}" />
                                <x-input-error :messages="$errors->get($name)" class="mt-2" />
                            </div>
                        @endforeach

                        <div class="mt-4">
                            <x-input-label for="branch" :value="__('Branch')" />
                            <select id="branch" name="branch"
                                class="block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full transition-all duration-300 hover:border-indigo-400">
                                <option value="">Select Branch</option>
                                <option value="Baner" @if (isset($checkUpInfo['branch']) && $checkUpInfo['branch'] == 'Baner') selected @endif>Baner</option>
                                <option value="Kothrud" @if (isset($checkUpInfo['branch']) && $checkUpInfo['branch'] == 'Kothrud') selected @endif>Kothrud
                                </option>
                            </select>
                            <x-input-error :messages="$errors->get('branch')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="doctor" :value="__('Doctor')" />
                            <select id="doctor" name="doctor"
                                class="border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full transition-all duration-300 hover:border-indigo-400">
                                <option value="">Select Doctor</option>
                                <option value="Dr.V.S.Deshpande" @if (isset($checkUpInfo['doctor']) && $checkUpInfo['doctor'] == 'Dr.V.S.Deshpande') selected @endif>
                                    Dr.V.S.Deshpande</option>
                                <option value="Dr.S.V.Gawande " @if (isset($checkUpInfo['doctor']) && $checkUpInfo['doctor'] == 'Dr.S.V.Gawande ') selected @endif>
                                    Dr.S.V.Gawande </option>
                            </select>
                            <x-input-error :messages="$errors->get('doctor')" class="mt-2" />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Update Follow Up') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
