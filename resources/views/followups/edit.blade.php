<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight px-5">
            {{ __('Edit Follow Up') }} - {{ $followup->patient->name }}

            <span class="text-gray-600 text-sm">
                @if ($followup->patient->birthdate || $followup->patient->gender)
                    {{ $followup->patient->birthdate?->age ?? __('') }}/{{ $followup->patient->gender ?? __('') }}
                @endif
                @if ($followup->patient->height)
                    | {{ __('messages.Height') }}: {{ $followup->patient->height }} cm
                @endif
                @if ($followup->patient->weight)
                    | {{ __('messages.Weight') }}: {{ $followup->patient->weight }} kg
                @endif
                @if ($followup->patient->height && $followup->patient->weight)
                    @php
                        $heightInMeters = $followup->patient->height / 100;
                        $bmi = $followup->patient->weight / ($heightInMeters * $heightInMeters);
                        $bmiCategory = match (true) {
                            $bmi < 18.5 => 'Underweight',
                            $bmi >= 18.5 && $bmi < 25 => 'Healthy Weight',
                            $bmi >= 25 && $bmi < 30 => 'Overweight',
                            default => 'Obese',
                        };
                    @endphp
                    | {{ __('BMI') }}: {{ number_format($bmi, 2) }}
                @endif
                @if (isset($totalDueAll))
                    | {{ __('messages.Total Outstanding Balance') }}: ₹{{ number_format($totalDueAll, 2) }}
                @endif
                <div class="my-1"></div>
                @if ($followup->patient->vishesh)
                    {{ __('messages.Vishesh') }}:
                    {!! str_replace(['<p>', '</p>', '<div>', '</div>'], '', $followup->patient->vishesh) !!}
                @endif
            </span>
        </h2>
    </x-slot>

    <div class="py-12 px-4 sm:px-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Follow-up Edit Form (Left Column) -->
                        <div class="lg:col-span-2">
                            <form method="POST" action="{{ route('followups.update', $followup) }}" id="followUpForm">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="patient_id" value="{{ $followup->patient->id }}" />

                                @php
                                    $checkUpInfo = json_decode($followup->check_up_info, true);
                                @endphp

                                <!-- Naadi Textarea -->
                                <div class="mb-6">
                                    <div class="justify-between flex items-center">
                                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                            {{ __('नाडी') }}
                                        </h2>
                                        <button type="button" onclick="toggleNadiModal(true)"
                                            class="bg-gray-500 text-white px-4 py-1 rounded hover:bg-gray-600 transition text-lg">
                                            +
                                        </button>
                                    </div>

                                    <textarea id="nadiInput" name="nadi" rows="4"
                                        class="tinymce-editor px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400">{{ old('nadi', isset($checkUpInfo['nadi']) ? trim($checkUpInfo['nadi']) : '') }}</textarea>

                                    <div id="nadiPresets" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-4 mt-4">
                                        @foreach (['वात', 'पित्त', 'कफ', 'सूक्ष्म', 'कठीण', 'साम', 'वेग', 'प्राण', 'व्यान', 'स्थूल', 'अल्प स्थूल', 'अनियमित', 'तीक्ष्ण', 'वेगवती'] as $nadi)
                                            <div class="relative">
                                                <button type="button"
                                                    class="nadi-box bg-gray-200 dark:bg-gray-700 p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition w-full text-left"
                                                    onclick="appendNadi('{{ $nadi }}')">{{ $nadi }}</button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <x-input-error :messages="$errors->get('nadi')" class="mt-2" />
                                </div>

                                <!-- Lakshane Textarea -->
                                <div class="mt-4 mb-4">
                                    <div class="flex items-center justify-between space-x-2">
                                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-1">
                                            {{ __('लक्षणे') }}
                                        </h2>
                                        <button type="button" onclick="openLakshaneModal()"
                                            class="bg-gray-500 text-white px-4 py-1 rounded hover:bg-gray-600 transition">
                                            +
                                        </button>
                                    </div>

                                    <textarea id="lakshane" name="diagnosis" rows="4"
                                        class="tinymce-editor px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400">{{ old('diagnosis', $followup->diagnosis) }}</textarea>
                                    <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />

                                    <div class="mt-4 grid grid-cols-7 gap-2">
                                        <button type="button" onclick="insertArrow('↑')"
                                            class="w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            ↑
                                        </button>
                                        <button type="button" onclick="insertArrow('↓')"
                                            class="w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            ↓
                                        </button>
                                        <button type="button" onclick="insertText('मल- ')"
                                            class="w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            मल
                                        </button>
                                        <button type="button" onclick="insertText('मूत्र - ')"
                                            class="w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            मूत्र
                                        </button>
                                        <button type="button" onclick="insertText('जिव्हा - ')"
                                            class="w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            जिव्हा
                                        </button>
                                        <button type="button" onclick="insertText('निद्रा - ')"
                                            class="w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            निद्रा
                                        </button>
                                        <button type="button" onclick="insertText('क्षुधा - ')"
                                            class="w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            क्षुधा
                                        </button>
                                    </div>
                                </div>

                                <!-- Nidan Input -->
                                <div class="mt-4 mb-4">
                                    <div class="flex items-center justify-between space-x-2">
                                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-1">
                                            {{ __('messages.diagnosis') }}
                                        </h2>
                                    </div>
                                    <input type="text" name="nidan"
                                        class="tinymce-editor002 px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"
                                        value="{{ old('nidan', isset($checkUpInfo['nidan']) ? $checkUpInfo['nidan'] : '') }}" />
                                </div>

                                <!-- Chikitsa Textarea -->
                                <div class="mt-6 mb-4 flex flex-col">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between space-x-2">
                                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                                {{ __('चिकित्सा') }}
                                            </h2>
                                            <button type="button" onclick="showChikitsaModal()"
                                                class="w-10 h-10 rounded bg-gray-500 text-white text-xl font-bold hover:bg-gray-600 transition">
                                                +
                                            </button>
                                        </div>

                                        <textarea id="chikitsa" name="chikitsa" rows="4"
                                            class="tinymce-editor px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400">{{ old('chikitsa', isset($checkUpInfo['chikitsa']) ? $checkUpInfo['chikitsa'] : '') }}</textarea>
                                        <x-input-error :messages="$errors->get('chikitsa')" class="mt-2" />

                                        @php
                                            // Fetch the latest follow-up's 'chikitsa' if available
                                            $latestFollowUp = $followup; // For edit, use current follow-up
                                            $previousChikitsa = isset($checkUpInfo['chikitsa']) ? $checkUpInfo['chikitsa'] : '';
                                        @endphp

                                        <div class="mt-4 grid grid-cols-5 gap-4">
                                            <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                                                data-preset="महासुदर्शन, वैदेही, बिभितक, यष्टी, तालीसादी">
                                                ज्वर
                                            </div>
                                            <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                                                data-preset="वरा, गुग्गुळ, विश्व, अश्वकपी, वत्स, गोक्षुर, गोदंती">
                                                संधिशूल
                                            </div>
                                            <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                                                data-preset="हरीतकी, अमृता, सारिवा">
                                                अर्श
                                            </div>
                                            <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                                                data-preset="कुटज, मुस्ता, विश्व">
                                                ग्रहणी
                                            </div>
                                            <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                                                data-preset="{{ $previousChikitsa }}">
                                                चिकित्सा यथा पूर्व
                                            </div>
                                        </div>
                                        <div id="chikitsaPresets" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2 mt-4"></div>
                                    </div>

                                    <!-- Vishesh Textarea -->
                                    <div class="mt-4 mb-4">
                                        <div class="flex items-center justify-between space-x-2">
                                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-1">
                                                {{ __('messages.Vishesh') }}
                                            </h2>
                                        </div>
                                        <textarea name="vishesh"
                                            class="tinymce-editor002 px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400">{{ old('vishesh', $followup->patient->vishesh) }}</textarea>
                                    </div>

                                    <!-- Numeric Input Boxes + Payment Method -->
                                    <div class="flex flex-wrap md:flex-nowrap items-start justify-center gap-10 mt-6">
                                        <div class="flex flex-col">
                                            <h2 class="text-md font-semibold text-gray-800 dark:text-white mb-1">
                                                {{ __('दिवस') }}
                                            </h2>
                                            <input type="text" name="days" id="days"
                                                value="{{ old('days', isset($checkUpInfo['days']) ? $checkUpInfo['days'] : '') }}"
                                                class="reverse-transliteration py-1 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400 w-24" />
                                        </div>
                                        <div class="flex flex-col">
                                            <h2 class="text-md font-semibold text-gray-800 dark:text-white mb-1">
                                                {{ __('पुड्या') }}
                                            </h2>
                                            <input type="text" name="packets" id="packets"
                                                value="{{ old('packets', isset($checkUpInfo['packets']) ? $checkUpInfo['packets'] : '') }}"
                                                class="reverse-transliteration py-1 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400 w-24" />
                                        </div>
                                        <div class="flex flex-col pl-2">
                                            <label for="total_due"
                                                class="text-md font-semibold text-gray-600 dark:text-gray-300 mb-1 block">
                                                {{ __('messages.Total Due') }}
                                            </label>
                                            <x-text-input id="total_due"
                                                class="px-3 py-1 block w-full border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-white rounded-lg shadow-md text-md"
                                                type="number" name="total_due"
                                                value="{{ old('total_due', isset($totalDueAll) ? $totalDueAll : 0) }}"
                                                readonly />
                                        </div>
                                        <div class="flex flex-col pl-2">
                                            <label for="payment_method"
                                                class="text-l font-semibold text-gray-700 dark:text-white mb-2">
                                                {{ __('messages.Payment Method') }}
                                            </label>
                                            <div class="flex items-center space-x-2">
                                                <label class="flex items-center space-x-1">
                                                    <input type="radio" name="payment_method" value="cash"
                                                        @if (old('payment_method', isset($checkUpInfo['payment_method']) ? $checkUpInfo['payment_method'] : '') == 'cash') checked @endif />
                                                    <span>Cash</span>
                                                </label>
                                                <label class="flex items-center space-x-1">
                                                    <input type="radio" name="payment_method" value="card"
                                                        @if (old('payment_method', isset($checkUpInfo['payment_method']) ? $checkUpInfo['payment_method'] : '') == 'card') checked @endif />
                                                    <span>Card</span>
                                                </label>
                                                <label class="flex items-center space-x-1">
                                                    <input type="radio" name="payment_method" value="online"
                                                        @if (old('payment_method', isset($checkUpInfo['payment_method']) ? $checkUpInfo['payment_method'] : '') == 'online') checked @endif />
                                                    <span>Online</span>
                                                </label>
                                            </div>
                                            <x-input-error :messages="$errors->get('payment_method')" class="mt-1" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Amount Billed and Paid -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <label for="amount_billed"
                                            class="text-md font-semibold text-gray-700 dark:text-white mb-1 block">
                                            {{ __('messages.Amount Billed') }}
                                        </label>
                                        <x-text-input id="amount_billed"
                                            class="reverse-transliteration px-2 py-1 block w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-md text-md"
                                            type="text" name="amount_billed"
                                            value="{{ old('amount_billed', $followup->amount_billed ?? 0) }}" required />
                                    </div>
                                    <div>
                                        <label for="amount_paid"
                                            class="text-md font-semibold text-gray-700 dark:text-white mb-1 block">
                                            {{ __('messages.Amount Paid') }}
                                        </label>
                                        <x-text-input id="amount_paid"
                                            class="reverse-transliteration px-2 py-1 block w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-md text-md"
                                            type="text" name="amount_paid"
                                            value="{{ old('amount_paid', $followup->amount_paid ?? 0) }}" required />
                                    </div>
                                </div>

                                <!-- Total Due Calculation Script -->
                                <script>
                                    function calculateTotalDue() {
                                        let allDues = parseFloat({{ isset($totalDueAll) ? $totalDueAll : 0 }}) || 0;
                                        let amountBilled = parseFloat(document.getElementById('amount_billed').value) || 0;
                                        let amountPaid = parseFloat(document.getElementById('amount_paid').value) || 0;

                                        let totalDue = allDues + amountBilled - amountPaid;
                                        document.getElementById('total_due').value = totalDue.toFixed(2);
                                    }

                                    window.onload = function() {
                                        calculateTotalDue();
                                        document.getElementById('amount_billed').addEventListener('input', calculateTotalDue);
                                        document.getElementById('amount_paid').addEventListener('input', calculateTotalDue);
                                    };
                                </script>

                                <!-- Modals -->
                                <div id="nadiModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                                    <div class="bg-white dark:bg-gray-800 p-6 rounded-md shadow-lg w-full max-w-md">
                                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">नवीन नाडी जोडा</h2>
                                        <input type="text" id="modalNadiInput" placeholder="उदा. वेगवती"
                                            class="w-full px-3 py-2 border rounded" />
                                        <div class="mt-4 flex justify-end space-x-2">
                                            <button type="button" onclick="toggleNadiModal(false)"
                                                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 rounded">Cancel</button>
                                            <button type="button" onclick="saveModalNadi()"
                                                class="px-4 py-2 bg-blue-500 text-white hover:bg-blue-600 rounded">Save</button>
                                        </div>
                                    </div>
                                </div>

                                <div id="lakshaneModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
                                    <div class="bg-white dark:bg-gray-800 p-4 rounded shadow w-full max-w-md">
                                        <h3 class="text-lg font-semibold mb-2 dark:text-white">नवीन लक्षणे जोडा</h3>
                                        <input type="text" id="newLakshaneInput"
                                            class="w-full border px-2 py-1 rounded mb-3 dark:bg-gray-900 dark:text-white"
                                            placeholder="उदाहरण: अजीर्ण" />
                                        <div class="flex justify-end space-x-2">
                                            <button type="button" onclick="closeLakshaneModal()"
                                                class="px-3 py-1 bg-gray-300 hover:bg-gray-400 rounded">Cancel</button>
                                            <button type="button" onclick="addLakshanePreset()"
                                                class="px-3 py-1 bg-blue-500 text-white hover:bg-blue-600 rounded">Add</button>
                                        </div>
                                    </div>
                                </div>

                                <div id="chikitsaModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                                    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-md w-full max-w-sm">
                                        <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-white">नवीन चिकित्सा प्रीसेट</h3>
                                        <input type="text" id="chikitsaPresetTitle" placeholder="उदा. ताप / ज्वर (title)"
                                            class="w-full px-3 py-2 border rounded mb-3 dark:bg-gray-700 dark:text-white" />
                                        <textarea id="chikitsaPresetValue" rows="2" placeholder="उदा. महासुदर्शन, वैदेही... (value)"
                                            class="w-full px-3 py-2 border rounded mb-4 dark:bg-gray-700 dark:text-white"></textarea>
                                        <div class="flex justify-end space-x-2">
                                            <button type="button" onclick="hideChikitsaModal()"
                                                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded dark:bg-gray-600 dark:hover:bg-gray-500 text-black dark:text-white">Cancel</button>
                                            <button type="button" onclick="addChikitsaPreset()"
                                                class="px-4 py-2 bg-blue-500 text-white hover:bg-blue-600 rounded">Add</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex items-center justify-end mt-4">
                                    <x-primary-button class="ms-4">
                                        {{ __('Update Follow Up') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>

                        <!-- Previous Follow-ups (Right Column) -->
                        <div class="relative min-h-screen">
                            <div class="absolute top-[62px] right-10 w-[375px] max-h-[calc(100vh-250px)] overflow-y-auto p-4 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 scrollbar-thin z-10 mb-3 md:static md:w-full md:mx-0 md:my-4">
                                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
                                    {{ __('Previous Follow-ups') }}
                                </h3>

                                @if ($followUps->count() > 0)
                                    <div class="space-y-4">
                                        @foreach ($followUps as $followUp)
                                            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-md shadow-sm border border-gray-300 dark:border-gray-600">
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $followUp->created_at->format('d M Y, h:i A') }}
                                                </p>
                                                @php $checkUpInfo = json_decode($followUp->check_up_info, true); @endphp
                                                <div class="text-sm leading-relaxed text-gray-700 dark:text-gray-300 [p]:m-0 [p]:text-sm [h1]:text-base [h2]:text-sm">
                                                    <strong>नाडी:</strong>
                                                    {!! str_replace(['<p>', '</p>', '<div>', '</div>'], '', $checkUpInfo['nadi'] ?? '-') !!}
                                                    <div class="my-0.5"></div>
                                                    <strong>{{ __('लक्षणे') }}:</strong>
                                                    {!! str_replace(['<p>', '</p>', '<div>', '</div>'], '', $followUp->diagnosis ?? '-') !!}
                                                    <div class="my-0.5"></div>
                                                    <strong>{{ __('चिकित्सा') }}:</strong>
                                                    {!! str_replace(['<p>', '</p>', '<div>', '</div>'], '', $checkUpInfo['chikitsa'] ?? '-') !!}
                                                    <div class="my-0.5"></div>
                                                    @if (!empty($checkUpInfo['days']))
                                                        <strong>{{ __('दिवस') }}:</strong>
                                                        {{ $checkUpInfo['days'] }}
                                                        <div class="my-0.5"></div>
                                                    @endif
                                                    @if (!empty($checkUpInfo['packets']))
                                                        <strong>{{ __('पुड्या') }}:</strong>
                                                        {{ $checkUpInfo['packets'] }}
                                                        <div class="my-0.5"></div>
                                                    @endif
                                                    @php
                                                        $amountPaid = $followUp->amount_paid ?? 0;
                                                    @endphp
                                                    <strong>{{ __('दिलेली रक्कम') }}:</strong>
                                                    ₹{{ number_format($amountPaid, 2) }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-600 dark:text-gray-400">
                                        {{ __('No previous follow-ups.') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nadi Script -->
    <script>
        const storageKey = "customNadiPresets";

        function appendNadi(nadi) {
            const editor = tinymce.get('nadiInput');
            if (!editor) {
                console.error("TinyMCE editor with ID 'nadiInput' not found.");
                return;
            }

            editor.focus();

            const selection = editor.selection;
            const rng = selection.getRng();
            const container = rng.startContainer;

            const fullText = editor.getContent({ format: 'text' });
            const cursorPos = rng.startOffset;
            const nodeText = container.textContent || '';

            const beforeText = nodeText.substring(0, cursorPos);
            const afterText = nodeText.substring(cursorPos);

            const needsSpaceBefore = beforeText.trim().length > 0 && !beforeText.trim().endsWith(' ');
            const needsSpaceAfter = afterText.trim().length > 0 && !afterText.trim().startsWith(' ');

            let insertText = '';
            if (needsSpaceBefore) insertText += ' ';
            insertText += nadi;
            if (needsSpaceAfter) insertText += ' ';

            selection.setContent(insertText);
            editor.selection.collapse(false);
            editor.focus();
        }

        function createPresetElement(text, isCustom = true) {
            const presetDiv = document.createElement('div');
            presetDiv.className = "relative";

            const button = document.createElement('button');
            button.type = 'button';
            button.className = "nadi-box bg-gray-200 dark:bg-gray-700 p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-500 transition w-full text-left pr-6";
            button.innerText = text;
            button.onclick = () => appendNadi(text);

            presetDiv.appendChild(button);

            if (isCustom) {
                const removeBtn = document.createElement('span');
                removeBtn.innerHTML = '❌';
                removeBtn.className = "absolute top-1 right-1 text-red-600 cursor-pointer text-xs";
                removeBtn.onclick = () => {
                    if (confirm(`Are you sure you want to delete "${text}"?`)) {
                        presetDiv.remove();
                        removePresetFromStorage(text);
                    }
                };
                presetDiv.appendChild(removeBtn);
            }

            document.getElementById('nadiPresets').appendChild(presetDiv);
        }

        function savePresetToStorage(value) {
            const stored = JSON.parse(localStorage.getItem(storageKey)) || [];
            if (!stored.includes(value)) {
                stored.push(value);
                localStorage.setItem(storageKey, JSON.stringify(stored));
            }
        }

        function removePresetFromStorage(value) {
            const stored = JSON.parse(localStorage.getItem(storageKey)) || [];
            const updated = stored.filter(item => item !== value);
            localStorage.setItem(storageKey, JSON.stringify(updated));
        }

        function loadCustomPresets() {
            const stored = JSON.parse(localStorage.getItem(storageKey)) || [];
            stored.forEach(preset => createPresetElement(preset, true));
        }

        function toggleNadiModal(show) {
            const modal = document.getElementById('nadiModal');
            const input = document.getElementById('modalNadiInput');
            modal.classList.toggle('hidden', !show);
            if (show) {
                input.value = '';
                input.focus();
            }
        }

        function saveModalNadi() {
            const input = document.getElementById('modalNadiInput');
            const value = input.value.trim();
            if (!value) return;

            createPresetElement(value, true);
            savePresetToStorage(value);
            toggleNadiModal(false);
        }

        document.addEventListener('DOMContentLoaded', loadCustomPresets);
    </script>

    <!-- Chikitsa Script -->
    <script>
        const chikitsaStorageKey = "customChikitsaPresets";

        function insertChikitsaText(text) {
            const editor = tinymce.get('chikitsa');
            if (!editor) return;

            editor.focus();

            const rng = editor.selection.getRng();
            const startContainer = rng.startContainer;
            const startOffset = rng.startOffset;

            let precedingChar = '';
            if (startContainer.nodeType === 3 && startOffset > 0) {
                precedingChar = startContainer.data.substring(startOffset - 1, startOffset);
            }

            const needsSpace = precedingChar && !precedingChar.match(/\s/);
            const cleanText = text.replace(/,/g, '');
            const insertText = (needsSpace ? ' ' : '') + cleanText;

            editor.selection.setContent(insertText);
        }

        function createChikitsaPreset(title, value, isCustom = true) {
            const wrapper = document.createElement('div');
            wrapper.className = 'relative';

            const btn = document.createElement('div');
            btn.className = 'border p-2 rounded cursor-pointer bg-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition';
            btn.textContent = title;
            btn.onclick = () => insertChikitsaText(value);
            wrapper.appendChild(btn);

            if (isCustom) {
                const removeBtn = document.createElement('span');
                removeBtn.innerHTML = '❌';
                removeBtn.className = 'absolute top-1 right-1 text-red-600 cursor-pointer text-xs';
                removeBtn.onclick = () => {
                    if (confirm(`"${title}" प्रीसेट काढायचे आहे का?`)) {
                        wrapper.remove();
                        removeChikitsaFromStorage(title);
                    }
                };
                wrapper.appendChild(removeBtn);
            }

            document.getElementById('chikitsaPresets').appendChild(wrapper);
        }

        function showChikitsaModal() {
            document.getElementById('chikitsaModal').classList.remove('hidden');
        }

        function hideChikitsaModal() {
            document.getElementById('chikitsaModal').classList.add('hidden');
            document.getElementById('chikitsaPresetTitle').value = '';
            document.getElementById('chikitsaPresetValue').value = '';
        }

        function addChikitsaPreset() {
            const title = document.getElementById('chikitsaPresetTitle').value.trim();
            const value = document.getElementById('chikitsaPresetValue').value.trim();
            if (!title || !value) return alert("कृपया title आणि value दोन्ही भरा.");

            createChikitsaPreset(title, value, true);
            saveChikitsaToStorage({ title, value });
            hideChikitsaModal();
        }

        function saveChikitsaToStorage(preset) {
            const stored = JSON.parse(localStorage.getItem(chikitsaStorageKey)) || [];
            const exists = stored.find(p => p.title === preset.title);
            if (!exists) {
                stored.push(preset);
                localStorage.setItem(chikitsaStorageKey, JSON.stringify(stored));
            }
        }

        function removeChikitsaFromStorage(title) {
            let stored = JSON.parse(localStorage.getItem(chikitsaStorageKey)) || [];
            stored = stored.filter(p => p.title !== title);
            localStorage.setItem(chikitsaStorageKey, JSON.stringify(stored));
        }

        function loadChikitsaPresets() {
            const stored = JSON.parse(localStorage.getItem(chikitsaStorageKey)) || [];
            stored.forEach(preset => createChikitsaPreset(preset.title, preset.value, true));
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadChikitsaPresets();
            document.querySelectorAll('.preset-box').forEach(box => {
                const value = box.dataset.preset;
                box.addEventListener('click', () => insertChikitsaText(value));
            });
        });
    </script>

    <!-- Lakshane Script -->
    <script>
        const lakshaneKey = 'customLakshanePresets';

        function insertText(text) {
            const editor = tinymce.get('lakshane');
            if (!editor) {
                console.error("TinyMCE editor with ID 'lakshane' not found.");
                return;
            }

            editor.focus();

            const selection = editor.selection;
            const rng = selection.getRng();
            const container = rng.startContainer;

            const cursorPos = rng.startOffset;
            const nodeText = container.textContent || '';
            const beforeText = nodeText.substring(0, cursorPos);
            const afterText = nodeText.substring(cursorPos);

            const needsSpaceBefore = beforeText.length > 0 && !beforeText.endsWith(' ');
            const needsSpaceAfter = afterText.length > 0 && !afterText.startsWith(' ');

            let insert = '';
            if (needsSpaceBefore) insert += ' ';
            insert += text;
            if (needsSpaceAfter) insert += ' ';

            selection.setContent(insert);
            editor.selection.collapse(false);
            editor.focus();
        }

        function insertArrow(arrow) {
            const editor = tinymce.get('lakshane');
            if (!editor) {
                console.error("TinyMCE editor with ID 'lakshane' not found.");
                return;
            }

            editor.focus();
            editor.insertContent(arrow);
            editor.selection.collapse(false);
            editor.focus();
        }

        function createLakshaneButton(text, isCustom = true) {
            const container = document.querySelector('.grid-cols-7');
            const wrapper = document.createElement('div');
            wrapper.className = 'relative';

            const button = document.createElement('button');
            button.type = 'button';
            button.className = "w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition text-left pr-6";
            button.innerText = text;
            button.onclick = () => insertText(text);

            wrapper.appendChild(button);

            if (isCustom) {
                const remove = document.createElement('span');
                remove.innerHTML = '❌';
                remove.className = 'absolute top-1 right-1 text-red-600 cursor-pointer text-xs';
                remove.onclick = () => {
                    if (confirm(`"${text}" काढायचे आहे का?`)) {
                        wrapper.remove();
                        const stored = JSON.parse(localStorage.getItem(lakshaneKey)) || [];
                        const updated = stored.filter(p => p !== text);
                        localStorage.setItem(lakshaneKey, JSON.stringify(updated));
                    }
                };
                wrapper.appendChild(remove);
            }

            container.appendChild(wrapper);
        }

        function openLakshaneModal() {
            document.getElementById('lakshaneModal').classList.remove('hidden');
        }

        function closeLakshaneModal() {
            document.getElementById('lakshaneModal').classList.add('hidden');
        }

        function addLakshanePreset() {
            const input = document.getElementById('newLakshaneInput');
            const value = input.value.trim();
            if (!value) return;

            createLakshaneButton(value, true);

            const stored = JSON.parse(localStorage.getItem(lakshaneKey)) || [];
            if (!stored.includes(value)) {
                stored.push(value);
                localStorage.setItem(lakshaneKey, JSON.stringify(stored));
            }

            input.value = '';
            closeLakshaneModal();
        }

        function loadLakshanePresets() {
            const stored = JSON.parse(localStorage.getItem(lakshaneKey)) || [];
            stored.forEach(p => createLakshaneButton(p, true));
        }

        document.addEventListener('DOMContentLoaded', loadLakshanePresets);
    </script>
</x-app-layout>
