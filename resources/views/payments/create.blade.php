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
                                class="w-full border-gray-300 rounded-md shadow-sm" value="">
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
                        <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" class="w-full border-gray-300 rounded-md shadow-sm" required />
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

        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            const term = searchInput.value.trim();
            patientIdInput.value = '';
            followUpSelect.innerHTML = '<option value="">Payment without follow-up</option>';
            followUpSelect.disabled = true;

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
    </script>
</x-app-layout>
