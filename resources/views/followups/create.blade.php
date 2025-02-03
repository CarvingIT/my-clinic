<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight px-52">
            {{ __('messages.Add Follow Up') }} - {{ $patient->name }}
        </h2>
    </x-slot>

    <div class="py-12 px-80">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('followups.store') }}">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ $patient->id }}" />

                        <!-- Nadi Checkboxes -->
                        {{-- <div class="mb-6">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">{{ __('नाडी') }}</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach (['वात' => 'वात', 'पित्त' => 'पित्त', 'कफ' => 'कफ', 'सूक्ष्म' => 'सूक्ष्म', 'कठिन' => 'कठिन', 'साम' => 'साम', 'प्राण' => 'प्राण', 'व्यान' => 'व्यान', 'स्थूल' => 'स्थूल', 'तीक्ष्ण' => 'तीक्ष्ण', 'वेग' => 'वेग', 'अनियमित' => 'अनियमित'] as $id => $label)
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" id="{{ $id }}" name="{{ $id }}" value="1" class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md transition-all duration-300 hover:border-indigo-400" />
                                        <x-input-label for="{{ $id }}" :value="__($label)" class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                    </div>
                                @endforeach
                            </div>
                         </div> --}}

                        {{-- <!-- Nadi Input Area -->
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">{{ __('नाडी') }}
                            </h2>
                            <input type="text" id="nadiInput" name="nadi"
                                class="w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                placeholder="Type or select nadi..." />

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                                @foreach (['वात', 'पित्त', 'कफ', 'सूक्ष्म', 'कठिन', 'साम', 'प्राण', 'व्यान', 'स्थूल', 'तीक्ष्ण', 'वेग', 'अनियमित'] as $nadi)
                                    <button type="button" class="nadi-box bg-gray-200 dark:bg-gray-700 p-2 rounded"
                                        onclick="appendNadi('{{ $nadi }}')">{{ $nadi }}</button>
                                @endforeach
                            </div>
                        </div> --}}

                        <!-- Naadi Textarea -->
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">{{ __('नाडी') }}
                            </h2>
                            <textarea id="nadiInput" name="nadi" rows="4"
                                class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"></textarea>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                                @foreach (['वात', 'पित्त', 'कफ', 'सूक्ष्म', 'कठिन', 'साम', 'वेग', 'प्राण', 'व्यान', 'स्थूल', 'अल्प स्थूल','अनियमित', 'तीक्ष्ण', 'वेगवती'] as $nadi)
                                    <button type="button" class="nadi-box bg-gray-200 dark:bg-gray-700 p-2 rounded"
                                        onclick="appendNadi('{{ $nadi }}')">{{ $nadi }}</button>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />
                        </div>

                        <!-- Diagnosis Textarea -->
                        <div class="mt-4 mb-4">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">{{ __('लक्षणे') }}
                            </h2>
                            <textarea id="lakshane" name="diagnosis" rows="4"
                                class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"></textarea>
                            <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />
                        </div>


                        <!-- Chikitsa Textarea -->
                        <div class="mt-4 mb-4 flex items-start">
                            <div class="flex-1">
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                    {{ __('चिकित्सा') }}</h2>
                                <textarea id="chikitsa" name="chikitsa" rows="4"
                                    class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"></textarea>
                                <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />

                                <div class="mt-4 grid grid-cols-4 gap-4">
                                    <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200"
                                        data-preset="महासुदर्शन, संजीवनी वटी, अग्नि तुंडी वटी">
                                        ज्वर
                                    </div>
                                    <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200"
                                        data-preset="वण, गुगगुंळ, हरितकी">
                                        संधिशूल
                                    </div>
                                    <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200"
                                        data-preset="कुटज, मुक्ता, बिल्व">
                                        अर्श
                                    </div>
                                    <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200"
                                        data-preset="विश्व, अभ्र, शंखभस्म, वन्ग, अमृता, सारिवा">
                                        ग्रहणी
                                    </div>
                                </div>
                            </div>

                            <!-- Numeric Input Boxes -->
                            <div class="flex items-center space-x-4 ml-4 mt-4">
                                <div class="flex flex-col">
                                    <h2 class="text-l font-semibold text-gray-800 dark:text-white mb-2 mt-9">
                                        {{ __('दिवस') }} </h2>
                                    <input type="number" name="days" id="days" placeholder=""
                                        class="px-2 py-1 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400 w-21" />
                                </div>
                                <div class="flex flex-col">
                                    <h2 class="text-l font-semibold text-gray-800 dark:text-white mb-2 mt-9">
                                        {{ __('पुड्या') }} </h2>
                                    <input type="number" name="packets" id="packets" placeholder=""
                                        class="px-2 py-1 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400 w-21" />
                                </div>
                            </div>


                        </div>







{{--
                        <!-- Treatment Section -->
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">{{ __('चिकित्सा') }}
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6 mt-2">
                                @foreach (['अर्श' => 'अर्श', 'ग्रहणी' => 'ग्रहणी', 'ज्वर/प्रतिश्याय' => 'ज्वर/प्रतिश्याय'] as $id => $label)
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" id="{{ $id }}" name="{{ $id }}"
                                            value="1"
                                            class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md transition-all duration-300 hover:border-indigo-400" />
                                        <x-input-label for="{{ $id }}" :value="__($label)"
                                            class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                    </div>
                                @endforeach
                            </div>

                            <!-- Select Input for Chikitsa combos-->
                            <div class="mt-4">
                                <x-input-label for="chikitsa_combo" :value="__('Add Combo')" class="mb-1" />
                                <select id="chikitsa_combo" name="chikitsa_combo"
                                    class="block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full transition-all duration-300 hover:border-indigo-400">
                                    <option value="">Please Select</option>
                                    <option value="कटज, मुस्ता, विश्व महासुदर्शन, तालिसादी, बहेडी">कटज, मुस्ता, विश्व
                                        महासुदर्शन, तालिसादी, बहेडी</option>
                                    <option value="विभीतकी, यष्टी, कुटकी, हरी, निंब, सारिवा, कुटकी">विभीतकी, यष्टी,
                                        कुटकी, हरी, निंब, सारिवा, कुटकी</option>
                                    <option value="यष्टी, कुटकी, हरी,">यष्टी, कुटकी, हरी</option>
                                </select>
                                <x-input-error :messages="$errors->get('chikitsa_combo')" class="mt-2" />
                            </div>

                        </div>


                        <!-- Remaining Input Fields -->
                        @foreach (['nidan' => 'निदान', 'upashay' => 'उपशय', 'salla' => 'सल्ला'] as $name => $label)
                            <div class="mt-4">
                                <x-input-label for="{{ $name }}" :value="__($label)" />
                                <x-text-input id="{{ $name }}"
                                    class="px-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"
                                    type="text" name="{{ $name }}" />
                                <x-input-error :messages="$errors->get($name)" class="mt-2" />
                            </div>
                        @endforeach


                        @foreach (['amount' => 'Amount', 'balance' => 'Balance'] as $name => $label)
                            <div class="mt-4">
                                <x-input-label for="{{ $name }}" :value="__($label)" />
                                <x-text-input id="{{ $name }}"
                                    class="px-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"
                                    type="number" name="{{ $name }}" />
                                <x-input-error :messages="$errors->get($name)" class="mt-2" />
                            </div>
                        @endforeach

                        <div class="mt-4">
                            <x-input-label for="payment_method" :value="__('messages.Payment Method')" />
                            <select id="payment_method" name="payment_method"
                                class="border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full transition-all duration-300 hover:border-indigo-400">
                                <option value="">Please Select</option>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="online">Online</option>
                            </select>
                            <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                        </div>

                        @foreach (['drawing' => 'Drawing'] as $name => $label)
                            <div class="mt-4">
                                <x-input-label for="{{ $name }}" :value="__($label)" />
                                <x-text-input id="{{ $name }}"
                                    class="px-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"
                                    type="text" name="{{ $name }}" />
                                <x-input-error :messages="$errors->get($name)" class="mt-2" />
                            </div>
                        @endforeach

                        <div class="mt-4">
                            <x-input-label for="branch" :value="__('messages.Branch')" />
                            <select id="branch" name="branch"
                                class="block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full transition-all duration-300 hover:border-indigo-400">
                                <option value="">Select Branch</option>
                                <option value="Baner">Baner</option>
                                <option value="Kothrud">Kothrud</option>
                            </select>
                            <x-input-error :messages="$errors->get('branch')" class="mt-2" />
                        </div>


                        <div class="mt-4">
                            <x-input-label for="doctor" :value="__('messages.Doctor')" />
                            <select id="doctor" name="doctor"
                                class="border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full transition-all duration-300 hover:border-indigo-400">
                                <option value="">Select Doctor</option>
                                <option value="Dr.V.S.Deshpande">Dr.V.S.Deshpande</option>
                                <option value="Dr.S.V.Gawande ">Dr.S.V.Gawande </option>
                            </select>
                            <x-input-error :messages="$errors->get('doctor')" class="mt-2" />
                        </div> --}}

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

<script>
    function appendNadi(nadi) {
        const input = document.getElementById('nadiInput');
        input.value += (input.value ? ', ' : '') + nadi;
        input.focus();
    }
</script>

<script>
    const textarea = document.getElementById('chikitsa');
    const presetBoxes = document.querySelectorAll('.preset-box');

    presetBoxes.forEach(box => {
        box.addEventListener('click', () => {
            const presetText = box.dataset.preset;
            textarea.value += (textarea.value ? ', ' : '') + presetText;
        });
    });
</script>
