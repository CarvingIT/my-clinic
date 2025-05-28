<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-700 leading-tight">
            {{ __('messages.Patient with Dues') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="p-6">
                    {{-- <form method="GET" action="{{ route('patient-dues.index') }}" class="mb-6" id="searchForm">
                        <div class="flex items-center space-x-4 w-full md:w-2/3">

                            <div class="relative w-72">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Search by Name or Mobile..."
                                    class="block w-72 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm pr-10"
                                    id="searchInput">


                                <button type="button" id="clearButton"
                                    class="absolute inset-y-0 right-2 flex items-center text-gray-500 hover:text-red-600 focus:outline-none"
                                    style="display: {{ request('search') ? 'flex' : 'none' }};">
                                    &times;
                                </button>
                            </div>


                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md shadow-md transition duration-300">
                                {{ __('Search') }}
                            </button>
                        </div>
                    </form> --}}

                    @if ($paginatedPatients->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead class="bg-gray-50 text-black dark:bg-gray-700 dark:text-gray-200">
                                    <tr>
                                        <th scope="col"
                                            class="w-1/4 px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                            {{ __('messages.name') }}
                                        </th>
                                        {{-- <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                            {{ __('Patient ID') }}
                                        </th> --}}
                                        <th scope="col"
                                            class="w-1/4 px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                            {{ __('messages.mobile_phone') }}
                                        </th>
                                        <th scope="col"
                                            class="w-1/4 px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                            {{ __('messages.Outstanding Balance') }}
                                        </th>
                                        <th scope="col"
                                            class="w-1/4 px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                            {{ __('messages.Last Follow Up') }}
                                        </th>
                                        <th scope="col"
                                            class="w-1/4 px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                            {{ __('messages.Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach ($paginatedPatients as $patient)
                                        <tr class="hover:bg-gray-50 transition duration-300 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                                {{ $patient->name }}
                                            </td>
                                            {{-- <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                                {{ $patient->patient_id }}
                                            </td> --}}
                                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                                {{ $patient->mobile_phone }}
                                            </td>
                                            <td
                                                class="px-6 py-4 font-bold {{ $patient->total_due > 0 ? 'text-red-600' : 'text-blue-600' }} dark:text-gray-300">
                                                ₹{{ number_format($patient->total_due, 2) }}
                                            </td>
                                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                                {{ $patient->last_follow_up_date ? $patient->last_follow_up_date->format('d-m-Y') : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                                <a href="{{ route('patients.show', $patient) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 font-medium"
                                                    title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-6">
                                {{ $paginatedPatients->links() }}
                            </div>
                        </div>
                    @else
                        <p class="text-gray-600 bg-gray-100 p-4 rounded-md shadow-sm">
                            {{ __('No patients with outstanding dues found.') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>



{{-- // Script to clear the search input and submit the form --}}
<script>
    const searchInput = document.getElementById('searchInput');
    const clearButton = document.getElementById('clearButton');
    const searchForm = document.getElementById('searchForm');

    clearButton.addEventListener('click', () => {
        searchInput.value = '';
        searchForm.submit(); // automatically submits to reset results
    });

    searchInput.addEventListener('input', () => {
        clearButton.style.display = searchInput.value ? 'flex' : 'none';
    });
</script>
