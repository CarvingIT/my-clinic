<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Add Payment
            </h2>
            <a href="{{ route('payments.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700">
                &larr; Back to Payments
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('payments.store') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                @csrf

                <!-- Left Column: Patient & Family Group Selection (Section 1) -->
                <div class="lg:col-span-6 flex flex-col">
                    <div class="bg-white dark:bg-gray-900 shadow-sm rounded-xl p-6 border border-gray-200/80 dark:border-gray-800 flex-1 flex flex-col justify-between">
                        <div>
                            <label class="block text-base font-bold text-gray-900 dark:text-gray-100 mb-2">
                                1. Select Patient <span class="text-red-500">*</span>
                            </label>
                            <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id', $selectedPatientId) }}">
                            <div class="relative">
                                <div class="relative">
                                    <input type="text" id="patient_search" autocomplete="off" placeholder="Search by name, mobile, or patient ID..."
                                        class="w-full pl-10 pr-4 py-3 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-base font-medium" 
                                        value="{{ $selectedPatient ? $selectedPatient->name . ' (' . $selectedPatient->patient_id . ')' . ($selectedPatient->mobile_phone ? ' - ' . $selectedPatient->mobile_phone : '') : '' }}">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                </div>
                                <div id="patient_results" class="absolute z-30 mt-1.5 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl hidden max-h-64 overflow-y-auto"></div>
                            </div>
                            @error('patient_id') <p class="text-red-600 text-sm mt-1.5">{{ $message }}</p> @enderror

                            <!-- Group Payment Section -->
                            <div id="group_payment_section" class="mt-4 hidden bg-indigo-50/60 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/50 rounded-xl p-4">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                        <span class="bg-indigo-600 text-white text-xs px-2.5 py-0.5 rounded-full font-medium">Family Group</span>
                                        <span id="group_name_label" class="text-indigo-600 dark:text-indigo-400"></span>
                                    </span>
                                    <span class="text-xs text-gray-500">Select members paying</span>
                                </div>
                                <div id="group_members_list" class="divide-y divide-gray-200/60 dark:divide-gray-800 max-h-72 overflow-y-auto pr-1">
                                    <!-- Members list dynamically loaded -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Amount, Payment Method, Additional Details & Action Buttons -->
                <div class="lg:col-span-6 flex flex-col justify-between space-y-4">
                    <div class="space-y-4">
                        <!-- Amount & Payment Method Card -->
                        <div class="bg-white dark:bg-gray-900 shadow-sm rounded-xl p-6 border border-gray-200/80 dark:border-gray-800 space-y-5">
                            <!-- Amount Field -->
                            <div>
                                <label class="block text-base font-bold text-gray-900 dark:text-gray-100 mb-2">
                                    2. Total Amount <span class="text-red-500">*</span>
                                </label>
                                <div class="relative rounded-lg shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-500 dark:text-gray-400 font-semibold text-lg">
                                        ₹
                                    </div>
                                    <input type="number" step="0.01" min="0.01" name="amount" id="amount" value="{{ old('amount') }}" 
                                        placeholder="0.00" 
                                        class="w-full pl-9 pr-4 py-3 text-xl font-bold border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required />
                                </div>
                                <p id="amount_help_text" class="text-xs text-indigo-600 dark:text-indigo-400 mt-1.5 hidden flex items-center gap-1">
                                    <svg class="w-4 h-4 inline flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Calculated automatically from selected family group members.
                                </p>
                                @error('amount') <p class="text-red-600 text-sm mt-1.5">{{ $message }}</p> @enderror
                            </div>

                            <!-- Payment Method Selector -->
                            <div>
                                <label class="block text-base font-bold text-gray-900 dark:text-gray-100 mb-2">
                                    3. Payment Method <span class="text-red-500">*</span>
                                </label>
                                <input type="hidden" name="payment_method" id="payment_method_input" value="{{ old('payment_method', 'cash') }}">
                                <div class="grid grid-cols-3 gap-2.5" id="payment_method_pills">
                                    @foreach([
                                        'cash' => 'Cash',
                                        'card' => 'Card',
                                        'online' => 'Online'
                                    ] as $value => $label)
                                        <button type="button" data-value="{{ $value }}" 
                                            class="payment-pill py-2.5 px-3 text-center text-sm font-semibold rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 transition-all cursor-pointer hover:border-indigo-400">
                                            {{ $label }}
                                        </button>
                                    @endforeach
                                </div>
                                @error('payment_method') <p class="text-red-600 text-sm mt-1.5">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Additional Details (Collapsible) -->
                        <div class="bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200/80 dark:border-gray-800 overflow-hidden">
                            <button type="button" id="toggle_optional_details" class="w-full px-5 py-3 flex justify-between items-center text-left text-xs font-semibold text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                                    Additional Details (Optional: Date, Follow-up, Notes)
                                </span>
                                <svg id="optional_details_chevron" class="w-4 h-4 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <div id="optional_details_content" class="hidden p-5 border-t border-gray-100 dark:border-gray-800 space-y-3.5">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold mb-1 text-gray-600 dark:text-gray-400">Follow-up</label>
                                        <select name="follow_up_id" id="follow_up_id" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-lg text-sm shadow-sm" disabled>
                                            <option value="">Payment without follow-up</option>
                                        </select>
                                        @error('follow_up_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold mb-1 text-gray-600 dark:text-gray-400">Date & Time</label>
                                        <input type="datetime-local" name="paid_at" value="{{ old('paid_at', now()->format('Y-m-d\\TH:i')) }}" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-lg text-sm shadow-sm" required />
                                        @error('paid_at') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold mb-1 text-gray-600 dark:text-gray-400">Notes</label>
                                    <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Optional payment note" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-lg text-sm shadow-sm" />
                                    @error('notes') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons Aligned to Bottom of Section 1 -->
                    <div class="flex items-center gap-3">
                        <button type="submit" class="flex-1 bg-indigo-600 text-white rounded-lg px-5 py-3 text-base font-bold hover:bg-indigo-700 shadow-md transition">
                            Save Payment
                        </button>
                        <a href="{{ route('payments.index') }}" class="px-5 py-3 text-base font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($errors->has('follow_up_id') || $errors->has('paid_at') || $errors->has('notes'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('optional_details_content')?.classList.remove('hidden');
                document.getElementById('optional_details_chevron')?.classList.add('rotate-180');
            });
        </script>
    @endif

    <script>
        const searchInput = document.getElementById('patient_search');
        const patientIdInput = document.getElementById('patient_id');
        const resultsBox = document.getElementById('patient_results');
        const followUpSelect = document.getElementById('follow_up_id');
        const paymentPills = document.querySelectorAll('.payment-pill');
        const paymentMethodInput = document.getElementById('payment_method_input');
        const toggleOptionalBtn = document.getElementById('toggle_optional_details');
        const optionalContent = document.getElementById('optional_details_content');
        const optionalChevron = document.getElementById('optional_details_chevron');
        let searchTimer = null;

        // Payment Method Pills selector
        function setPaymentMethod(val) {
            paymentMethodInput.value = val;
            paymentPills.forEach(pill => {
                if (pill.dataset.value === val) {
                    pill.className = 'payment-pill py-2.5 px-3 text-center text-sm font-bold rounded-lg border-2 border-indigo-600 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 cursor-pointer shadow-sm';
                } else {
                    pill.className = 'payment-pill py-2.5 px-3 text-center text-sm font-semibold rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 cursor-pointer hover:border-indigo-400';
                }
            });
        }

        paymentPills.forEach(pill => {
            pill.addEventListener('click', () => setPaymentMethod(pill.dataset.value));
        });
        setPaymentMethod(paymentMethodInput.value || 'cash');

        // Optional details accordion
        toggleOptionalBtn.addEventListener('click', () => {
            const isHidden = optionalContent.classList.contains('hidden');
            if (isHidden) {
                optionalContent.classList.remove('hidden');
                optionalChevron.classList.add('rotate-180');
            } else {
                optionalContent.classList.add('hidden');
                optionalChevron.classList.remove('rotate-180');
            }
        });

        function renderPatients(items) {
            resultsBox.innerHTML = '';
            if (!items.length) {
                resultsBox.classList.add('hidden');
                return;
            }

            items.forEach((patient) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'w-full text-left px-4 py-3 text-base font-semibold hover:bg-indigo-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700/50 last:border-b-0 transition text-gray-900 dark:text-gray-100';
                button.textContent = `${patient.name} (${patient.patient_id})${patient.mobile_phone ? ' - ' + patient.mobile_phone : ''}`;
                button.addEventListener('click', () => {
                    searchInput.value = button.textContent;
                    patientIdInput.value = patient.id;
                    resultsBox.classList.add('hidden');
                    loadFollowUps(patient.id);
                    loadGroupMembers(patient.id);
                });
                resultsBox.appendChild(button);
            });

            resultsBox.classList.remove('hidden');
        }

        async function loadFollowUps(patientId) {
            followUpSelect.innerHTML = '<option value="">Loading...</option>';
            followUpSelect.disabled = true;

            const response = await fetch(`{{ route('payments.followups') }}?patient_id=${encodeURIComponent(patientId)}`, {
                headers: { 'Accept': 'application/json' }
            });

            const items = await response.json();
            followUpSelect.innerHTML = '<option value="">Payment without follow-up</option>';
            items.forEach((followUp) => {
                const option = document.createElement('option');
                option.value = followUp.id;
                option.textContent = `#${followUp.id} - ${new Date(followUp.created_at).toLocaleString()}`;
                followUpSelect.appendChild(option);
            });
            followUpSelect.disabled = false;
        }

        async function loadGroupMembers(patientId) {
            const container = document.getElementById('group_payment_section');
            const list = document.getElementById('group_members_list');
            const amountInput = document.getElementById('amount');
            const amountHelpText = document.getElementById('amount_help_text');

            container.classList.add('hidden');
            list.innerHTML = '';

            const response = await fetch(`{{ route('payments.group-members') }}?patient_id=${encodeURIComponent(patientId)}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (!data || !data.members || data.members.length <= 1) {
                // Keep standard individual payment flow
                amountInput.readOnly = false;
                amountInput.classList.remove('bg-gray-100', 'dark:bg-gray-800', 'cursor-not-allowed');
                amountHelpText.classList.add('hidden');
                return;
            }

            document.getElementById('group_name_label').textContent = data.group_name;
            container.classList.remove('hidden');

            amountInput.readOnly = true;
            amountInput.classList.add('bg-gray-100', 'dark:bg-gray-800', 'cursor-not-allowed');
            amountHelpText.classList.remove('hidden');

            data.members.forEach((member) => {
                const isPrimary = parseInt(member.id) === parseInt(patientId);

                const row = document.createElement('div');
                row.className = 'py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-200/50 dark:border-gray-800 last:border-b-0';

                // Checkbox and Label
                const leftDiv = document.createElement('div');
                leftDiv.className = 'flex items-center gap-3';
                
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'group_members[]';
                checkbox.value = member.id;
                checkbox.id = `member_check_${member.id}`;
                checkbox.className = 'w-4 h-4 rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-indigo-500';
                if (isPrimary) {
                    checkbox.checked = true;
                }
                leftDiv.appendChild(checkbox);

                const label = document.createElement('label');
                label.htmlFor = `member_check_${member.id}`;
                label.className = 'flex flex-col cursor-pointer';
                
                const nameSpan = document.createElement('span');
                nameSpan.className = 'text-sm font-bold text-gray-900 dark:text-gray-100';
                nameSpan.textContent = member.name + (isPrimary ? ' (Primary)' : '');
                label.appendChild(nameSpan);

                const detailsSpan = document.createElement('span');
                detailsSpan.className = 'text-xs text-gray-500 dark:text-gray-400 font-medium';
                detailsSpan.textContent = `ID: ${member.patient_id} | Dues: ₹${parseFloat(member.due).toFixed(2)}`;
                label.appendChild(detailsSpan);

                leftDiv.appendChild(label);
                row.appendChild(leftDiv);

                // Amount input field
                const rightDiv = document.createElement('div');
                rightDiv.className = 'flex items-center gap-1.5';

                const currencyLabel = document.createElement('span');
                currencyLabel.className = 'text-sm font-semibold text-gray-500';
                currencyLabel.textContent = '₹';
                rightDiv.appendChild(currencyLabel);

                const input = document.createElement('input');
                input.type = 'number';
                input.step = '0.01';
                input.min = '0';
                input.name = `group_amounts[${member.id}]`;
                input.id = `member_amount_${member.id}`;
                input.className = 'w-28 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-lg text-sm font-semibold text-right focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3';
                
                if (isPrimary) {
                    input.disabled = false;
                    input.required = true;
                    input.value = member.due > 0 ? parseFloat(member.due).toFixed(2) : '';
                } else {
                    input.disabled = true;
                    input.value = '';
                }
                
                rightDiv.appendChild(input);
                row.appendChild(rightDiv);

                list.appendChild(row);

                // Listeners
                checkbox.addEventListener('change', () => {
                    if (checkbox.checked) {
                        input.disabled = false;
                        input.required = true;
                        if (!input.value && member.due > 0) {
                            input.value = parseFloat(member.due).toFixed(2);
                        }
                    } else {
                        input.disabled = true;
                        input.required = false;
                        input.value = '';
                    }
                    calculateTotal();
                });

                input.addEventListener('input', () => {
                    calculateTotal();
                });
            });

            calculateTotal();

            function calculateTotal() {
                let total = 0;
                let hasChecked = false;
                data.members.forEach((member) => {
                    const cb = document.getElementById(`member_check_${member.id}`);
                    const inp = document.getElementById(`member_amount_${member.id}`);
                    if (cb && cb.checked && inp && inp.value) {
                        total += parseFloat(inp.value);
                        hasChecked = true;
                    }
                });
                
                amountInput.value = total > 0 ? total.toFixed(2) : (hasChecked ? '0.00' : '');
            }
        }

        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            const term = searchInput.value.trim();
            patientIdInput.value = '';
            followUpSelect.innerHTML = '<option value="">Payment without follow-up</option>';
            followUpSelect.disabled = true;
            
            document.getElementById('group_payment_section').classList.add('hidden');
            document.getElementById('group_members_list').innerHTML = '';
            const amountInput = document.getElementById('amount');
            amountInput.readOnly = false;
            amountInput.classList.remove('bg-gray-100', 'dark:bg-gray-800', 'cursor-not-allowed');
            document.getElementById('amount_help_text').classList.add('hidden');

            if (term.length < 2) {
                resultsBox.classList.add('hidden');
                resultsBox.innerHTML = '';
                return;
            }

            searchTimer = setTimeout(async () => {
                const response = await fetch(`{{ route('payments.patients.search') }}?q=${encodeURIComponent(term)}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const items = await response.json();
                renderPatients(items);
            }, 200);
        });

        document.addEventListener('click', (event) => {
            if (!resultsBox.contains(event.target) && event.target !== searchInput) {
                resultsBox.classList.add('hidden');
            }
        });

        // Initialize if patient is pre-selected
        document.addEventListener('DOMContentLoaded', () => {
            const initialPatientId = patientIdInput.value;
            if (initialPatientId) {
                loadFollowUps(initialPatientId);
                loadGroupMembers(initialPatientId);
            }
        });
    </script>
</x-app-layout>
