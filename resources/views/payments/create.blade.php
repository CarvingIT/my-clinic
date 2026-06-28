<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Add Payment
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg p-4 md:p-6">
                <form method="POST" action="{{ route('payments.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Patient search</label>
                        <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id', $selectedPatientId) }}">
                        <div class="relative">
                            <input type="text" id="patient_search" autocomplete="off" placeholder="Type name, mobile, or patient ID"
                                class="w-full border-gray-300 rounded-md shadow-sm" value="{{ $selectedPatient ? $selectedPatient->name . ' (' . $selectedPatient->patient_id . ')' . ($selectedPatient->mobile_phone ? ' - ' . $selectedPatient->mobile_phone : '') : '' }}">
                            <div id="patient_results" class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg hidden max-h-64 overflow-y-auto"></div>
                        </div>
                        @error('patient_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Follow-up (optional)</label>
                        <select name="follow_up_id" id="follow_up_id" class="w-full border-gray-300 rounded-md shadow-sm" disabled>
                            <option value="">Payment without follow-up</option>
                        </select>
                        @error('follow_up_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Amount</label>
                        <input type="number" step="0.01" min="0.01" name="amount" id="amount" value="{{ old('amount') }}" class="w-full border-gray-300 rounded-md shadow-sm" required />
                        <p id="amount_help_text" class="text-xs text-indigo-600 dark:text-indigo-400 mt-1 hidden">Calculated automatically from selected group members below.</p>
                        @error('amount') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Payment Method</label>
                        <select name="payment_method" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="cash" @selected(old('payment_method') === 'cash')>Cash</option>
                            <option value="online" @selected(old('payment_method') === 'online')>Online</option>
                            <option value="upi" @selected(old('payment_method') === 'upi')>UPI</option>
                            <option value="card" @selected(old('payment_method') === 'card')>Card</option>
                            <option value="bank" @selected(old('payment_method') === 'bank')>Bank</option>
                        </select>
                        @error('payment_method') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Paid At</label>
                        <input type="datetime-local" name="paid_at" value="{{ old('paid_at', now()->format('Y-m-d\\TH:i')) }}" class="w-full border-gray-300 rounded-md shadow-sm" required />
                        @error('paid_at') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Reference No (optional)</label>
                        <input type="text" name="reference_no" value="{{ old('reference_no') }}" class="w-full border-gray-300 rounded-md shadow-sm" />
                        @error('reference_no') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Group Payment Section -->
                    <div id="group_payment_section" class="md:col-span-2 hidden bg-indigo-50/50 dark:bg-gray-800/40 border border-indigo-100 dark:border-gray-800 rounded-lg p-4">
                        <h4 class="text-sm font-bold text-gray-850 dark:text-gray-200 mb-3 flex justify-between items-center">
                            <span>Family/Group Payment: <strong id="group_name_label" class="text-indigo-600 dark:text-indigo-400"></strong></span>
                            <span class="text-xs font-normal text-gray-500">Select group members and enter amounts</span>
                        </h4>
                        <div id="group_members_list" class="divide-y divide-gray-200 dark:divide-gray-800">
                            <!-- Dynamically loaded members list -->
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Notes (optional)</label>
                        <textarea name="notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                        @error('notes') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit" class="bg-indigo-600 text-white rounded-md px-4 py-2 text-sm font-semibold hover:bg-indigo-700">Save Payment</button>
                        <a href="{{ route('payments.index') }}" class="bg-gray-200 rounded-md px-4 py-2 text-sm font-semibold hover:bg-gray-300">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('patient_search');
        const patientIdInput = document.getElementById('patient_id');
        const resultsBox = document.getElementById('patient_results');
        const followUpSelect = document.getElementById('follow_up_id');
        let searchTimer = null;

        function renderPatients(items) {
            resultsBox.innerHTML = '';
            if (!items.length) {
                resultsBox.classList.add('hidden');
                return;
            }

            items.forEach((patient) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'w-full text-left px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 border-b last:border-b-0';
                button.textContent = `${patient.name} (${patient.patient_id})${patient.mobile_phone ? ' - ' + patient.mobile_phone : ''}`;
                button.addEventListener('click', () => {
                    searchInput.value = button.textContent;
                    patientIdInput.value = patient.id;
                    resultsBox.classList.add('hidden');
                    loadFollowUps(patient.id);
                    loadGroupMembers(patient.id); // Load group members dynamically
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
                // Keep original flow if not part of a group
                amountInput.readOnly = false;
                amountHelpText.classList.add('hidden');
                return;
            }

            document.getElementById('group_name_label').textContent = data.group_name;
            container.classList.remove('hidden');

            data.members.forEach((member) => {
                const isPrimary = parseInt(member.id) === parseInt(patientId);

                const row = document.createElement('div');
                row.className = 'py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-105 dark:border-gray-800 last:border-b-0';

                // Checkbox and Label
                const leftDiv = document.createElement('div');
                leftDiv.className = 'flex items-center gap-3';
                
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'group_members[]';
                checkbox.value = member.id;
                checkbox.id = `member_check_${member.id}`;
                checkbox.className = 'rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-indigo-500';
                if (isPrimary) {
                    checkbox.checked = true;
                }
                leftDiv.appendChild(checkbox);

                const label = document.createElement('label');
                label.htmlFor = `member_check_${member.id}`;
                label.className = 'flex flex-col cursor-pointer';
                
                const nameSpan = document.createElement('span');
                nameSpan.className = 'text-sm font-semibold text-gray-900 dark:text-gray-100';
                nameSpan.textContent = member.name + (isPrimary ? ' (Primary)' : '');
                label.appendChild(nameSpan);

                const detailsSpan = document.createElement('span');
                detailsSpan.className = 'text-xs text-gray-500 dark:text-gray-400';
                detailsSpan.textContent = `ID: ${member.patient_id} | Outstanding Dues: ₹${parseFloat(member.due).toFixed(2)}`;
                label.appendChild(detailsSpan);

                leftDiv.appendChild(label);
                row.appendChild(leftDiv);

                // Amount input field
                const rightDiv = document.createElement('div');
                rightDiv.className = 'flex items-center gap-2';

                const currencyLabel = document.createElement('span');
                currencyLabel.className = 'text-sm text-gray-500';
                currencyLabel.textContent = '₹';
                rightDiv.appendChild(currencyLabel);

                const input = document.createElement('input');
                input.type = 'number';
                input.step = '0.01';
                input.min = '0';
                input.name = `group_amounts[${member.id}]`;
                input.id = `member_amount_${member.id}`;
                input.className = 'w-32 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm text-right focus:border-indigo-500 focus:ring-indigo-500';
                
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
                
                if (hasChecked) {
                    amountInput.value = total.toFixed(2);
                    amountInput.readOnly = true;
                    amountHelpText.classList.remove('hidden');
                } else {
                    amountInput.value = '';
                    amountInput.readOnly = false;
                    amountHelpText.classList.add('hidden');
                }
            }
        }

        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            const term = searchInput.value.trim();
            patientIdInput.value = '';
            followUpSelect.innerHTML = '<option value="">Payment without follow-up</option>';
            followUpSelect.disabled = true;
            
            // Hide group payment section when search terms are modified
            document.getElementById('group_payment_section').classList.add('hidden');
            document.getElementById('group_members_list').innerHTML = '';
            document.getElementById('amount').readOnly = false;
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
