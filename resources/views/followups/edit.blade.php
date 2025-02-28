<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight px-52">
            {{ __('Edit Follow Up') }} - {{ $followup->patient->name }}
        </h2>
    </x-slot>

    <div class="py-12 px-80">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('followups.update', $followup) }}">
                        @csrf
                        @method('PUT') <!-- Important for updates -->
                        <input type="hidden" name="patient_id" value="{{ $followup->patient->id }}" />

                        <!-- Nadi Checkboxes -->
                        {{-- <div class="mb-6">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">{{ __('नाडी') }}
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                                @php
                                    $checkUpInfo = json_decode($followup->check_up_info, true);
                                @endphp

                                @foreach (['वात' => 'वात', 'पित्त' => 'पित्त', 'कफ' => 'कफ', 'सूक्ष्म' => 'सूक्ष्म', 'कठीण' => 'कठीण', 'साम' => 'साम', 'प्राण' => 'प्राण', 'व्यान' => 'व्यान', 'स्थूल' => 'स्थूल', 'तीक्ष्ण' => 'तीक्ष्ण', 'वेग' => 'वेग', 'अनियमित' => 'अनियमित'] as $id => $label)
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

                        </div> --}}

                        <!-- Naadi Textarea -->
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">{{ __('नाडी') }}
                            </h2>

                            <textarea id="nadiInput" name="nadi" rows="4"
                                class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400">{{ old('nadi', isset($checkUpInfo['nadi']) ? trim($checkUpInfo['nadi']) : '') }}
                            </textarea>


                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                                @foreach (['वात', 'पित्त', 'कफ', 'सूक्ष्म', 'कठीण', 'साम', 'वेग', 'प्राण', 'व्यान', 'स्थूल', 'अल्प स्थूल', 'अनियमित', 'तीक्ष्ण', 'वेगवती'] as $nadi)
                                    <button type="button" class="nadi-box bg-gray-200 dark:bg-gray-700 p-2 rounded"
                                        onclick="appendNadi('{{ $nadi }}')">{{ $nadi }}</button>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('nadi')" class="mt-2" />
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
                        <div class="mt-4 mb-4 flex items-start">
                            <div class="flex-1">
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                    {{ __('चिकित्सा') }}
                                </h2>
                                <textarea id="chikitsa" name="chikitsa" rows="4"
                                    class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400">{{ old('chikitsa', isset($checkUpInfo['chikitsa']) ? $checkUpInfo['chikitsa'] : '') }}
                                </textarea>
                                <x-input-error :messages="$errors->get('chikitsa')" class="mt-2" />

                                <div class="mt-4 grid grid-cols-4 gap-4">
                                    <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200"
                                        data-preset="महासुदर्शन, वैदेही, बिभितक, यष्टी, तालीसादी ">
                                        ज्वर
                                    </div>
                                    <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200"
                                        data-preset="वरा, गुग्गुळ, विश्व, अश्वकपी, वत्स, गोक्षुर, गोदंती">
                                        संधिशूल
                                    </div>
                                    <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200"
                                        data-preset="हरीतकी, अमृता, सारिवा ">
                                        अर्श
                                    </div>
                                    <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200"
                                        data-preset="कुटज, मुस्ता, विश्व ">
                                        ग्रहणी
                                    </div>
                                </div>

                                <!-- Numeric Input Boxes -->
                                <div class="flex items-center space-x-4 ml-4 mt-4">
                                    <div class="flex flex-col">
                                        <h2 class="text-l font-semibold text-gray-800 dark:text-white mb-2 mt-9">
                                            {{ __('दिवस') }}
                                        </h2>
                                        <input type="number" name="days" id="days"
                                            value="{{ old('days', isset($checkUpInfo['days']) ? $checkUpInfo['days'] : '') }}"
                                            class="px-2 py-1 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400 w-21" />
                                    </div>
                                    <div class="flex flex-col">
                                        <h2 class="text-l font-semibold text-gray-800 dark:text-white mb-2 mt-9">
                                            {{ __('पुड्या') }}
                                        </h2>
                                        <input type="number" name="packets" id="packets"
                                            value="{{ old('packets', isset($checkUpInfo['packets']) ? $checkUpInfo['packets'] : '') }}"
                                            class="px-2 py-1 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400 w-21" />
                                    </div>
                                </div>

                            </div>


                        </div>


                        <!-- Remaining Input Fields -->
                        @php
                            $checkUpInfo = json_decode($followup->check_up_info, true);
                        @endphp
                        {{-- @foreach (['nidan' => 'निदान', 'upashay' => 'उपशय', 'salla' => 'सल्ला'] as $name => $label)
                            <div class="mt-4">
                                <x-input-label for="{{ $name }}" :value="__($label)" />
                                <x-text-input id="{{ $name }}"
                                    class="px-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"
                                    type="text" name="{{ $name }}"
                                    value="{{ $checkUpInfo[$name] ?? '' }}" />
                                <x-input-error :messages="$errors->get($name)" class="mt-2" />
                            </div>
                        @endforeach --}}


                        <div class="mt-4">
                            <x-input-label for="payment_method" :value="__('messages.Payment Method')" />
                            <select id="payment_method" name="payment_method"
                                class="border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full transition-all duration-300 hover:border-indigo-400">
                                <option value="">Please Select</option>
                                <option value="cash" @if (isset($checkUpInfo['payment_method']) && $checkUpInfo['payment_method'] == 'cash') selected @endif>Cash</option>
                                <option value="card" @if (isset($checkUpInfo['payment_method']) && $checkUpInfo['payment_method'] == 'card') selected @endif>Card</option>
                                <option value="online" @if (isset($checkUpInfo['payment_method']) && $checkUpInfo['payment_method'] == 'online') selected @endif>Online</option>
                            </select>
                            <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                        </div>

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



                        {{-- @foreach (['drawing' => 'Drawing'] as $name => $label)
                            <div class="mt-4">
                                <x-input-label for="{{ $name }}" :value="__($label)" />
                                <x-text-input id="{{ $name }}"
                                    class="px-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"
                                    type="text" name="{{ $name }}"
                                    value="{{ $checkUpInfo[$name] ?? '' }}" />
                                <x-input-error :messages="$errors->get($name)" class="mt-2" />
                            </div>
                        @endforeach --}}

                        {{-- <div class="mt-4">
                            <x-input-label for="branch" :value="__('messages.Branch')" />
                            <select id="branch" name="branch"
                                class="block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full transition-all duration-300 hover:border-indigo-400">
                                <option value="">Select Branch</option>
                                <option value="Baner" @if (isset($checkUpInfo['branch']) && $checkUpInfo['branch'] == 'Baner') selected @endif>Baner</option>
                                <option value="Kothrud" @if (isset($checkUpInfo['branch']) && $checkUpInfo['branch'] == 'Kothrud') selected @endif>Kothrud
                                </option>
                            </select>
                            <x-input-error :messages="$errors->get('branch')" class="mt-2" />
                        </div> --}}

                        {{-- <div class="mt-4">
                            <x-input-label for="doctor" :value="__('messages.Doctor')" />
                            <select id="doctor" name="doctor"
                                class="border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full transition-all duration-300 hover:border-indigo-400">
                                <option value="">Select Doctor</option>
                                <option value="Dr.V.S.Deshpande" @if (isset($checkUpInfo['doctor']) && $checkUpInfo['doctor'] == 'Dr.V.S.Deshpande') selected @endif>
                                    Dr.V.S.Deshpande</option>
                                <option value="Dr.S.V.Gawande " @if (isset($checkUpInfo['doctor']) && $checkUpInfo['doctor'] == 'Dr.S.V.Gawande ') selected @endif>
                                    Dr.S.V.Gawande </option>
                            </select>
                            <x-input-error :messages="$errors->get('doctor')" class="mt-2" />
                        </div> --}}

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

<script>
    function appendNadi(nadi) {
        const input = document.getElementById('nadiInput');

        let currentValue = input.value.trim();

        if (currentValue) {
            if (!currentValue.endsWith(',')) {
                currentValue += ',';
            }
            input.value = currentValue + ' ' + nadi;
        } else {
            input.value = nadi;
        }

        input.focus();
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('chikitsa');
        const presetBoxes = document.querySelectorAll('.preset-box');

        presetBoxes.forEach(box => {
            box.addEventListener('click', () => {
                const presetText = box.dataset.preset;

                let currentValue = textarea.value.trim();


                if (currentValue) {

                    if (!currentValue.endsWith(',')) {
                        currentValue += ',';
                    }
                    textarea.value = currentValue + ' ' + presetText;
                } else {
                    textarea.value = presetText;
                }


                textarea.focus();
            });
        });
    });
</script>


{{-- <script>
    const textarea = document.getElementById('chikitsa');
    const presetBoxes = document.querySelectorAll('.preset-box');

    presetBoxes.forEach(box => {
        box.addEventListener('click', () => {
            const presetText = box.dataset.preset;
            textarea.value += (textarea.value ? ', ' : '') + presetText;
        });
    });
</script> --}}
