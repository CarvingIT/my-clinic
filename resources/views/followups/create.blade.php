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



                        <!-- Naadi Textarea -->
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">{{ __('नाडी') }}
                            </h2>
                            <textarea id="nadiInput" name="nadi" rows="4"
                                class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"></textarea>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                                @foreach (['वात', 'पित्त', 'कफ', 'सूक्ष्म', 'कठीण', 'साम', 'वेग', 'प्राण', 'व्यान', 'स्थूल', 'अल्प स्थूल', 'अनियमित', 'तीक्ष्ण', 'वेगवती'] as $nadi)
                                    <button type="button" class="nadi-box bg-gray-200 dark:bg-gray-700 p-2 rounded"
                                        onclick="appendNadi('{{ $nadi }}')">{{ $nadi }}</button>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />
                        </div>

                        <!-- Diagnosis Textarea -->
                        <div class="mt-4 mb-4">
                            <div class="flex items-center space-x-2">
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-1">
                                    {{ __('लक्षणे') }}
                                </h2>
                                <div class="space-x-2">
                                    <!-- Up Arrow Button -->
                                    <button type="button" onclick="insertArrow('↑')"
                                        class="mb-1 w-12 h-7 px-2 py-1 items-center justify-center bg-gray-200 dark:bg-gray-700 rounded-md shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                        ↑
                                    </button>
                                    <!-- Down Arrow Button -->
                                    <button type="button" onclick="insertArrow('↓')"
                                        class="w-12 h-7 px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded-md shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                        ↓
                                    </button>
                                </div>
                            </div>

                            <textarea id="lakshane" name="diagnosis" rows="4"
                                class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"></textarea>
                            <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />
                        </div>


                        <!-- Chikitsa Textarea -->
                        <div class="mt-4 mb-4 flex flex-col">
                            <div class="flex-1">
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                    {{ __('चिकित्सा') }}</h2>
                                <textarea id="chikitsa" name="chikitsa" rows="4"
                                    class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"></textarea>
                                <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />

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
                            </div>

                            <!-- Numeric Input Boxes (Now Below) -->
                            <div class="flex items-center space-x-4 mt-6">
                                <div class="flex flex-col">
                                    <h2 class="text-l font-semibold text-gray-800 dark:text-white mb-2">
                                        {{ __('दिवस') }} </h2>
                                    <input type="number" name="days" id="days" placeholder=""
                                        class="px-2 py-1 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400 w-21" />
                                </div>
                                <div class="flex flex-col">
                                    <h2 class="text-l font-semibold text-gray-800 dark:text-white mb-2">
                                        {{ __('पुड्या') }} </h2>
                                    <input type="number" name="packets" id="packets" placeholder=""
                                        class="px-2 py-1 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400 w-21" />
                                </div>
                            </div>
                        </div>



                        <div class="mt-4">
                            <label for="payment_method"
                                class="text-l font-semibold text-gray-700 dark:text-white mb-4">{{ __('messages.Payment Method') }}
                            </label>
                            <select id="payment_method" name="payment_method"
                                class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400 text-sm">
                                <option value="">Please Select</option>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="online">Online</option>
                            </select>
                            <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <label for="amount" class="text-l font-semibold text-gray-700 dark:text-white mb-4">
                                {{ __('messages.Amount Paid') }}
                            </label>
                            <x-text-input id="amount"
                                class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                                       focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600
                                       rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400 text-sm"
                                type="number" name="amount" required />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <label for="balance"
                                class="text-l font-semibold text-gray-700 dark:text-white mb-4">{{ __('messages.Balance Due') }}
                            </label>
                            <x-text-input id="balance"
                                class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                                       focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600
                                       rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400 text-sm"
                                type="number" name="balance" required />
                            <x-input-error :messages="$errors->get('balance')" class="mt-2" />
                        </div>


                        {{-- @foreach (['amount' => 'Amount', 'balance' => 'Balance'] as $name => $label)
                            <div class="mt-4">
                                <x-input-label for="{{ $name }}" :value="__($label)" />
                                <x-text-input id="{{ $name }}"
                                    class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400 text-sm"
                                    type="number" name="{{ $name }}" />
                                <x-input-error :messages="$errors->get($name)" class="mt-2" />
                            </div>
                        @endforeach --}}










                        {{--
                        <!-- Treatment Section -->



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

<script>
    function insertArrow(arrow) {
        let textarea = document.getElementById("lakshane");
        let start = textarea.selectionStart;
        let end = textarea.selectionEnd;

        // Insert the arrow at the cursor position
        let text = textarea.value;
        textarea.value = text.substring(0, start) + arrow + text.substring(end);

        // Move cursor after inserted arrow
        textarea.selectionStart = textarea.selectionEnd = start + arrow.length;

        // Focus back on textarea
        textarea.focus();
    }
</script>
