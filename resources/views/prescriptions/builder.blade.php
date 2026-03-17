<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Build Prescription - {{ $patient->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-6 pb-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Build Prescription</h1>
                <p class="text-sm text-gray-600">
                    <span class="font-semibold">{{ $patient->name }}</span> • {{ $followup->created_at->format('d M Y') }}
                </p>
            </div>

            <form action="{{ route('followups.prescription.build', ['followup' => $followup->id]) }}" method="POST" id="prescriptionForm">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 mb-6">
                    <!-- Left Sidebar: Field Selector -->
                    <div class="lg:col-span-2">
                        <div class="bg-white border border-gray-300 rounded-lg overflow-hidden shadow-sm">
                            <div class="bg-gray-100 px-4 py-3 border-b border-gray-300">
                                <h2 class="text-sm font-bold text-gray-900">Select Fields</h2>
                            </div>

                            <div class="p-4 space-y-2 max-h-[55vh] overflow-y-auto">
                                @php
                                    $sections = [
                                        'patient_info' => ['title' => 'Patient Info', 'icon' => 'fa-user'],
                                        'medical_info' => ['title' => 'Medical Info', 'icon' => 'fa-heartbeat'],
                                        'treatment_details' => ['title' => 'Treatment', 'icon' => 'fa-prescription-bottle'],
                                        'payment_info' => ['title' => 'Payment', 'icon' => 'fa-money-bill'],
                                        'clinic_info' => ['title' => 'Clinic', 'icon' => 'fa-hospital'],
                                    ];
                                @endphp

                                @foreach ($sections as $sectionKey => $sectionData)
                                    <div class="border border-gray-200 rounded p-3">
                                        <h3 class="font-semibold text-xs text-gray-900 mb-2">{{ $sectionData['title'] }}</h3>
                                        <div class="space-y-1">
                                            @foreach ($allFields as $fieldKey => $fieldData)
                                                @if ($fieldData['section'] === $sectionKey)
                                                    <label class="flex items-center gap-2 cursor-pointer text-xs">
                                                        <input type="checkbox"
                                                            name="selected_fields[]"
                                                            value="{{ $fieldKey }}"
                                                            {{ in_array($fieldKey, $defaultSelected) ? 'checked' : '' }}
                                                            class="field-toggle w-4 h-4 rounded border-gray-300 text-indigo-600"
                                                            data-field="{{ $fieldKey }}">
                                                        <span class="text-gray-700">{{ $fieldData['label'] }}</span>
                                                    </label>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Select All / Deselect All -->
                            <div class="border-t border-gray-300 p-3 flex gap-2 bg-gray-50">
                                <button type="button" onclick="selectAllFields()" class="flex-1 px-3 py-2 bg-indigo-600 text-white rounded text-xs font-medium hover:bg-indigo-700 transition">
                                    All
                                </button>
                                <button type="button" onclick="deselectAllFields()" class="flex-1 px-3 py-2 bg-gray-400 text-white rounded text-xs font-medium hover:bg-gray-500 transition">
                                    None
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Field Editor -->
                    <div class="lg:col-span-3">
                        <div class="bg-white border border-gray-300 rounded-lg overflow-hidden shadow-sm flex flex-col h-full">
                            <div class="bg-gray-100 px-4 py-3 border-b border-gray-300 flex-shrink-0">
                                <h2 class="text-sm font-bold text-gray-900">Edit Values</h2>
                            </div>

                            <div class="p-4 max-h-[55vh] overflow-y-auto flex-grow">
                                <div id="fieldsContainer" class="space-y-3">
                                    <p class="text-gray-500 text-center py-8 text-xs">
                                        Select fields from the left panel
                                    </p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="border-t border-gray-300 px-4 py-3 bg-gray-50 flex gap-2 flex-shrink-0">
                                <a href="{{ route('patients.show', $patient->id) }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded text-sm font-medium transition">
                                    Cancel
                                </a>
                                <button type="submit" class="ml-auto px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-medium transition">
                                    Continue
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const allFields = {!! json_encode($allFields) !!};

        function renderFields() {
            const container = document.getElementById('fieldsContainer');
            const selectedCheckboxes = document.querySelectorAll('input[name="selected_fields[]"]:checked');

            if (selectedCheckboxes.length === 0) {
                container.innerHTML = `<p class="text-gray-500 text-center py-6 text-xs">Select fields from the left panel</p>`;
                return;
            }

            let html = '';
            const selectedFields = Array.from(selectedCheckboxes).map(cb => cb.value);

            // Group by section
            const sections = {
                'patient_info': { title: 'Patient Info' },
                'medical_info': { title: 'Medical Info' },
                'treatment_details': { title: 'Treatment' },
                'payment_info': { title: 'Payment' },
                'clinic_info': { title: 'Clinic' },
            };

            for (const [sectionKey, sectionData] of Object.entries(sections)) {
                const fieldsInSection = selectedFields.filter(fk => allFields[fk].section === sectionKey);

                if (fieldsInSection.length === 0) continue;

                html += `<div class="border-b border-gray-200 pb-2 last:border-b-0">
                    <h3 class="font-semibold text-xs text-gray-900 mb-2">${sectionData.title}</h3>
                    <div class="grid grid-cols-2 gap-2">`;

                fieldsInSection.forEach(fieldKey => {
                    const field = allFields[fieldKey];
                    const inputId = `field_${fieldKey}`;
                    const value = field.value || '';

                    if (field.type === 'textarea') {
                        html += `<div class="col-span-2">
                            <label for="${inputId}" class="text-xs font-semibold text-gray-700 mb-1 block">${field.label}</label>
                            <textarea name="field_values[${fieldKey}]"
                                id="${inputId}"
                                rows="2"
                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-200 resize-none"
                                placeholder="Enter ${field.label.toLowerCase()}">${value}</textarea>
                        </div>`;
                    } else {
                        html += `<div>
                            <label for="${inputId}" class="text-xs font-semibold text-gray-700 mb-1 block">${field.label}</label>
                            <input type="text"
                                name="field_values[${fieldKey}]"
                                id="${inputId}"
                                value="${value}"
                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-200"
                                placeholder="Enter ${field.label.toLowerCase()}">
                        </div>`;
                    }
                });

                html += `</div></div>`;
            }

            container.innerHTML = html;
        }

        function selectAllFields() {
            document.querySelectorAll('input[name="selected_fields[]"]').forEach(cb => cb.checked = true);
            renderFields();
        }

        function deselectAllFields() {
            document.querySelectorAll('input[name="selected_fields[]"]').forEach(cb => cb.checked = false);
            renderFields();
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            renderFields();
            document.querySelectorAll('.field-toggle').forEach(checkbox => {
                checkbox.addEventListener('change', renderFields);
            });
        });

        // Prevent form submission if no fields selected
        document.getElementById('prescriptionForm').addEventListener('submit', (e) => {
            const selectedCheckboxes = document.querySelectorAll('input[name="selected_fields[]"]:checked');
            if (selectedCheckboxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one field to include in the prescription.');
                return false;
            }
        });
    </script>
</body>
</html>
