<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Create Patient Group
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg p-4 md:p-6">
                <form method="POST" action="{{ route('groups.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Group Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Sharma Family"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Description (Optional)</label>
                        <textarea name="description" rows="3" placeholder="Additional notes about the group or family"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                        @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-800 pt-6">
                        <h3 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-2">Group Members</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Search and add patients who belong to this group/family.</p>

                        <!-- Patient Search Box -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Search Patient</label>
                            <div class="relative">
                                <input type="text" id="patient_search" autocomplete="off" placeholder="Type patient name, mobile, or ID..."
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <div id="patient_results" class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
                            </div>
                        </div>

                        <!-- Selected Patients List -->
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4 border border-gray-200 dark:border-gray-800">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex justify-between items-center">
                                Selected Patients
                                <span id="member_count" class="bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300 text-xs px-2 py-0.5 rounded-full font-bold">0 members</span>
                            </h4>
                            
                            <div id="no_members_placeholder" class="text-sm text-gray-500 dark:text-gray-400 italic text-center py-4">
                                No patients added to this group yet. Use the search field above to find and add patients.
                            </div>

                            <ul id="selected_patients_list" class="divide-y divide-gray-200 dark:divide-gray-800 hidden">
                                <!-- Dynamically added members go here -->
                            </ul>
                        </div>
                    </div>

                    <div class="flex gap-2 pt-4">
                        <button type="submit" class="bg-indigo-600 text-white rounded-md px-4 py-2 text-sm font-semibold hover:bg-indigo-700 shadow transition duration-150">Create Group</button>
                        <a href="{{ route('payments.index', ['tab' => 'groups']) }}" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md px-4 py-2 text-sm font-semibold hover:bg-gray-300 dark:hover:bg-gray-600 transition duration-150">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('patient_search');
        const resultsBox = document.getElementById('patient_results');
        const selectedList = document.getElementById('selected_patients_list');
        const noMembersPlaceholder = document.getElementById('no_members_placeholder');
        const memberCountBadge = document.getElementById('member_count');
        
        // Track unique patient IDs added to avoid duplicates
        const selectedPatientIds = new Set();
        let searchTimer = null;

        function addPatientToGroup(patient) {
            if (selectedPatientIds.has(patient.id)) {
                return; // already added
            }

            selectedPatientIds.add(patient.id);

            // Create list item
            const li = document.createElement('li');
            li.id = `member_row_${patient.id}`;
            li.className = 'py-3 flex justify-between items-center gap-4';

            // Hidden input for form submission
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'patient_ids[]';
            hiddenInput.value = patient.id;
            li.appendChild(hiddenInput);

            // Member Info
            const infoDiv = document.createElement('div');
            infoDiv.className = 'flex flex-col';
            infoDiv.innerHTML = `
                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">${patient.name}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">ID: ${patient.patient_id} ${patient.mobile_phone ? '| Mob: ' + patient.mobile_phone : ''}</span>
            `;
            li.appendChild(infoDiv);

            // Remove Button
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-semibold';
            removeBtn.textContent = 'Remove';
            removeBtn.addEventListener('click', () => {
                removePatientFromGroup(patient.id);
            });
            li.appendChild(removeBtn);

            selectedList.appendChild(li);
            updateUIState();
        }

        function removePatientFromGroup(patientId) {
            selectedPatientIds.delete(patientId);
            const row = document.getElementById(`member_row_${patientId}`);
            if (row) {
                row.remove();
            }
            updateUIState();
        }

        function updateUIState() {
            const count = selectedPatientIds.size;
            memberCountBadge.textContent = `${count} ${count === 1 ? 'member' : 'members'}`;
            
            if (count > 0) {
                selectedList.classList.remove('hidden');
                noMembersPlaceholder.classList.add('hidden');
            } else {
                selectedList.classList.add('hidden');
                noMembersPlaceholder.classList.remove('hidden');
            }
        }

        function renderSearchPatients(items) {
            resultsBox.innerHTML = '';
            if (!items.length) {
                resultsBox.classList.add('hidden');
                return;
            }

            items.forEach((patient) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'w-full text-left px-4 py-2.5 hover:bg-gray-100 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-800 last:border-b-0 text-sm flex justify-between items-center';
                
                const infoText = document.createElement('span');
                infoText.textContent = `${patient.name} (${patient.patient_id})`;
                infoText.className = 'font-medium text-gray-800 dark:text-gray-200';
                button.appendChild(infoText);

                if (patient.mobile_phone) {
                    const mobileSpan = document.createElement('span');
                    mobileSpan.textContent = patient.mobile_phone;
                    mobileSpan.className = 'text-xs text-gray-500 dark:text-gray-400';
                    button.appendChild(mobileSpan);
                }

                button.addEventListener('click', () => {
                    addPatientToGroup(patient);
                    searchInput.value = '';
                    resultsBox.classList.add('hidden');
                });
                resultsBox.appendChild(button);
            });

            resultsBox.classList.remove('hidden');
        }

        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            const term = searchInput.value.trim();

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
                renderSearchPatients(items);
            }, 200);
        });

        document.addEventListener('click', (event) => {
            if (!resultsBox.contains(event.target) && event.target !== searchInput) {
                resultsBox.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
