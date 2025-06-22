<x-app-layout>
    @php
        $patient_count = \App\Models\Patient::count();
    @endphp
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('messages.patients') }} ({{ $patient_count }})
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-xl">
                <div class="p-5 text-gray-900">
                    <!-- Action Section -->
                    <div class="flex justify-between items-center mb-6">
                        {{-- <!-- Add New Patient Button -->
                        <a href="{{ route('patients.create') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                            {{ __('messages.add_new_patient') }}
                        </a> --}}


                        <!-- Add New Patient and Import Buttons -->
                        <div class="flex gap-2">
                            <!-- Add Patient Button -->
                            <a href="{{ route('patients.create') }}"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                                {{ __('messages.add_new_patient') }}
                            </a>

                            <!-- Import Patient Button + Modal -->
                            <div x-data="{ openImportModal: false }">
                                <!-- Trigger Button -->
                                @if (Auth::check() && Auth::user()->hasRole('admin', 'doctor'))
                                    <button @click="openImportModal = true"
                                        class="bg-violet-600 hover:bg-violet-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                                        {{ __('Import Patient') }}
                                    </button>
                                @endif

                                <!-- Modal Overlay -->
                                <div x-show="openImportModal" x-cloak x-transition.opacity
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">

                                    <!-- Modal Content -->
                                    <div @click.away="openImportModal = false"
                                        class="relative w-full max-w-lg mx-4 bg-white rounded-2xl shadow-2xl border border-blue-100 transition-all">

                                        <!-- Modal Header -->
                                        <div
                                            class="flex items-center justify-between px-6 py-4 bg-blue-50 border-b border-blue-100 rounded-t-2xl">
                                            <h3 class="text-lg font-bold text-blue-800">
                                                {{ __('Import Patient Data') }}
                                            </h3>
                                            <button @click="openImportModal = false"
                                                class="text-blue-800 text-2xl font-bold hover:text-red-500 transition">
                                                &times;
                                            </button>
                                        </div>

                                        <!-- Modal Body -->
                                        <div class="px-6 py-5">
                                            <form method="POST" action="{{ route('patients.import_json') }}"
                                                enctype="multipart/form-data">
                                                @csrf

                                                <!-- File Input -->
                                                <div class="mb-5">
                                                    <x-input-label for="file" :value="__('Select JSON File')"
                                                        class="text-blue-800" />
                                                    <input type="file" id="file" name="file" accept=".json"
                                                        class="mt-2 block w-full border border-blue-300 rounded-lg px-4 py-2 text-sm text-blue-900 bg-blue-50 placeholder-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                        required />
                                                    <x-input-error :messages="$errors->get('file')" class="mt-2 text-red-500" />
                                                </div>

                                                <!-- Action Buttons -->
                                                <div class="flex justify-end gap-2 mt-6">
                                                    <button type="button" @click="openImportModal = false"
                                                        class="bg-white hover:bg-blue-100 text-blue-700 font-semibold py-2 px-4 rounded-lg border border-blue-300 transition">
                                                        {{ __('Cancel') }}
                                                    </button>
                                                    <button type="submit"
                                                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg shadow transition">
                                                        {{ __('Import') }}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Search Form -->
                        <form method="GET" action="{{ route('patients.index') }}" class="flex gap-2 items-center">
                            <div class="relative">
                                <!-- Search Input -->
                                <input type="text" name="search" placeholder="{{ __('messages.search') }}"
                                    value="{{ request('search') }}"
                                    class="border border-gray-300 rounded-lg px-4 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-10">

                                <!-- Clear Search Link -->
                                <a href="{{ route('patients.index') }}"
                                    class=" text-2xl text-gray-700 hover:text-gray-800 focus:outline-none {{ request('search') ? '' : 'pointer-events-none opacity-50' }}">
                                    ×
                                </a>
                            </div>

                            <!-- Search Button -->
                            <button type="submit"
                                class="bg-gray-300 hover:bg-gray-400 text-black font-semibold px-4 py-2 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                                {{ __('messages.search') }}
                            </button>
                        </form>
                    </div>

                    <!-- Success Message -->
                    {{-- @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-5">
                            {{ session('success') }}
                        </div>
                    @endif --}}

                    {{-- Error Message --}}
                    @if (session('error'))
                        <div id="error-alert"
                            class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-5 relative">
                            <p class="font-bold">{{ session('error') }}</p>
                            <button onclick="document.getElementById('error-alert').style.display = 'none';"
                                class="absolute top-1 right-2 text-red-600 text-xl leading-none hover:text-red-800"
                                title="Dismiss">
                                &times;
                            </button>
                        </div>
                    @endif



                    <!-- Success Message -->
                    @if (session('success'))
                        <div id="successAlert"
                            class="relative bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-5">
                            <!-- Dismiss button -->
                            <button onclick="document.getElementById('successAlert').style.display='none'"
                                class="absolute top-2 right-3 text-green-700 hover:text-green-900 text-xl font-bold focus:outline-none"
                                aria-label="Close">&times;</button>

                            <p class="font-bold">{{ session('success') }}</p>

                            @if (session('import_details'))
                                <div class="mt-2">
                                    <p><strong>Total Patients Added:</strong>
                                        {{ session('import_details.total_patients_affected') }}</p>

                                    @if (!empty(session('import_details.created_patients')))
                                        <p><strong>Created Patients:</strong></p>
                                        <ul class="list-disc ml-5">
                                            @foreach (session('import_details.created_patients') as $patient)
                                                <li>{{ $patient['name'] }} (ID: {{ $patient['patient_id'] }})</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    @if (!empty(session('import_details.restored_patients')))
                                        <p><strong>Restored Patients:</strong></p>
                                        <ul class="list-disc ml-5">
                                            @foreach (session('import_details.restored_patients') as $patient)
                                                <li>{{ $patient['name'] }} (ID: {{ $patient['patient_id'] }})</li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    @if (!empty(session('import_details.updated_patients')))
                                        <p><strong>Updated Patients:</strong></p>
                                        <ul class="list-disc ml-5">
                                            @foreach (session('import_details.updated_patients') as $patient)
                                                <li>{{ $patient['name'] }} (ID: {{ $patient['patient_id'] }})</li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    @if (!empty(session('import_details.total_follow_ups')))
                                        <p><strong>Follow-Ups Added:</strong></p>
                                        <ul class="list-disc ml-5">
                                            @foreach (session('import_details.total_follow_ups') as $followUp)
                                                @if ($followUp['follow_ups_added'] > 0)
                                                    <li>{{ $followUp['name'] }} (ID: {{ $followUp['patient_id'] }}):
                                                        {{ $followUp['follow_ups_added'] }} follow-up(s)</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif


                    <!-- Table Section -->
                    <div class="max-w-full overflow-x-auto">
                        <table class="table-fixed border-collapse border">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th scope="col"
                                        class="px-4 py-3    text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ __('Id') }}
                                    </th>
                                    <th scope="col"
                                        class="px-4 py-3    text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ __('messages.name') }}
                                    </th>
                                    <th scope="col"
                                        class="px-4 py-3    text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ __('messages.mobile_phone') }}
                                    </th>
                                    <th scope="col"
                                        class="px-4 py-3    text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ __('messages.address') }}
                                    </th>
                                    <th scope="col"
                                        class="px-4 py-3    text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ __('messages.Vishesh') }}
                                    </th>
                                    <th scope="col"
                                        class="px-4 py-3    text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ __('messages.Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($patients as $patient)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-4 py-4 align-top    break-normal whitespace-normal"
                                            style="vertical-align: top;">
                                            {{ $patient->patient_id }}
                                        </td>
                                        <td class="px-4 py-4 align-top   break-normal whitespace-normal"
                                            style="vertical-align: top;">
                                            <a href="{{ route('patients.show', $patient->id) }}"
                                                class="text-blue-600 hover:text-blue-800 font-medium">
                                                {{ $patient->name }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-4 align-top    break-normal whitespace-normal"
                                            style="vertical-align: top;">
                                            {{ $patient->mobile_phone }}
                                        </td>
                                        <td class="px-4 py-4 align-top    break-normal whitespace-normal"
                                            style="vertical-align: top;">
                                            {{ $patient->address }}
                                        </td>
                                        <td class="px-4 py-4 align-top    break-normal whitespace-normal"
                                            style="vertical-align: top;">
                                            {!! $patient->vishesh !!}
                                        </td>
                                        <td class="px-4 py-4 align-top    break-normal whitespace-normal text-right text-sm font-medium flex gap-4"
                                            style="vertical-align: top;">

                                            <!-- Export Patient Button with Modal -->
                                            {{-- <div x-data="{ openExportPatientModal: false }">
                                                @if (Auth::check() && (Auth::user()->hasRole('doctor') || Auth::user()->hasRole('admin')))
                                                    <button @click="openExportPatientModal = true"
                                                        title="Export Patient"
                                                        class="text-purple-600 hover:text-purple-800 transition duration-200 text-l">
                                                        <i class="fas fa-file-export"></i>
                                                    </button>
                                                @endif

                                                <!-- Modal -->
                                                <div x-show="openExportPatientModal" x-cloak x-transition.opacity
                                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                                                    <div @click.away="openExportPatientModal = false"
                                                        class="relative w-full max-w-lg mx-4 bg-white rounded-2xl shadow-2xl border border-blue-100 transition-all">

                                                        <!-- Header -->
                                                        <div
                                                            class="flex items-center justify-between px-6 py-4 bg-blue-50 border-b border-blue-100 rounded-t-2xl">
                                                            <h3 class="text-lg font-bold text-blue-800">
                                                                {{ __('Export Patient Data') }}
                                                            </h3>
                                                            <button @click="openExportPatientModal = false"
                                                                class="text-blue-800 text-2xl font-bold hover:text-red-500 transition">
                                                                &times;
                                                            </button>
                                                        </div>

                                                        <!-- Body -->
                                                        <div class="px-6 py-5">
                                                            <form method="POST"
                                                                action="{{ route('patients.export_json', $patient->id) }}">
                                                                @csrf

                                                                <!-- Email Field with Better UX -->
                                                                <div class="mb-5">
                                                                    <label for="email"
                                                                        class="block text-sm font-semibold text-blue-700 mb-1">
                                                                        📧 Recipient Email Address
                                                                    </label>
                                                                    <input type="email" id="email"
                                                                        name="email"
                                                                        placeholder="e.g. doctor@example.com"
                                                                        class="w-full border border-blue-300 rounded-lg px-4 py-2 text-sm text-blue-900 bg-blue-50 placeholder-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                                        required />
                                                                    <p class="text-xs text-gray-500 mt-1">
                                                                        We'll send the exported patient data to this
                                                                        email address.
                                                                    </p>
                                                                    <x-input-error :messages="$errors->get('email')"
                                                                        class="mt-2 text-red-500" />
                                                                </div>

                                                                <!-- Buttons -->
                                                                <div class="flex justify-end gap-2 mt-6">
                                                                    <button type="button"
                                                                        @click="openExportPatientModal = false"
                                                                        class="bg-white hover:bg-blue-100 text-blue-700 font-semibold py-2 px-4 rounded-lg border border-blue-300 transition">
                                                                        {{ __('Cancel') }}
                                                                    </button>
                                                                    <button type="submit"
                                                                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg shadow transition">
                                                                        {{ __('Export') }}
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> --}}


                                            <button class="text-green-600 hover:text-green-800 font-medium"
                                                onclick="setPatientId({{ $patient->id }}); toggleQueueModal()"
                                                title="Add to Queue">
                                                <i class="fas fa-users-line"></i>
                                            </button>
                                            <a href="{{ route('patients.edit', $patient->id) }}"
                                                class="text-indigo-600 hover:text-indigo-800 font-medium"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST"
                                                action="{{ route('patients.destroy', $patient->id) }}"
                                                onsubmit="return confirmDelete()">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-800 font-medium"
                                                    title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $patients->appends(['search' => request('search')])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>


</x-app-layout>
{{-- Queue Modal --}}
<div id="queueModal" class="modal-container hidden">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
            <h5 class="text-xl font-semibold text-gray-800">{{ __('Add to Queue') }}</h5>
            <button type="button" onclick="toggleQueueModal()"
                class="text-gray-600 hover:text-gray-800 text-2xl">×</button>
        </div>
        <form id="queueForm" method="POST">
            @csrf
            <input type="hidden" name="patient_id" id="patientId">
            <div class="mb-4">
                <label for="in_queue_at"
                    class="block text-sm font-medium text-gray-700">{{ __('messages.select_date_time') }}
                    (optional):</label>
                <input type="datetime-local" name="in_queue_at" id="in_queue_at"
                    class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="toggleQueueModal()"
                    class="bg-gray-300 hover:bg-gray-400 text-black font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                    {{ __('Cancel') }}
                </button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                    {{ __('Add to Queue') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function setPatientId(patientId) {
        console.log('Setting patient ID:', patientId);
        document.getElementById('patientId').value = patientId;
        document.getElementById('queueForm').action = '/patients/' + patientId + '/queue';
    }

    function toggleQueueModal() {
        console.log('Toggling queue modal');
        const modal = document.getElementById('queueModal');
        modal.classList.toggle('hidden');
    }
</script>

<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this patient?");
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll("td").forEach(td => {
            let words = td.innerText.trim().split(/\s+/); // Split by spaces
            if (words.length > 4) {
                td.style.whiteSpace = "normal"; // Allow wrapping
                td.style.wordBreak = "break-word"; // Break long words if needed
            }
        });
    });
</script>

{{-- <script>
    function setPatientId(patientId) {
            console.log('Setting patient ID:', patientId);
            document.getElementById('patientId').value = patientId;
            document.getElementById('queueForm').action = '/patients/' + patientId + '/queue';
        }

        function toggleQueueModal() {
            console.log('Toggling queue modal');
            const modal = document.getElementById('queueModal');
            modal.classList.toggle('hidden');
        }
</script> --}}
