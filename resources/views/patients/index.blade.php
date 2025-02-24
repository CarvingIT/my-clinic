<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('messages.patients') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-xl">
                <div class="p-5 text-gray-900">
                    <!-- Action Section -->
                    <div class="flex justify-between items-center mb-6">
                        <!-- Add New Patient Button -->
                        <a href="{{ route('patients.create') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                            {{ __('messages.add_new_patient') }}
                        </a>

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
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-5">
                            {{ session('success') }}
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
                                        <td class="px-4 py-4 align-top    break-normal whitespace-normal" style="vertical-align: top;">
                                            {{ $patient->patient_id }}
                                        </td>
                                        <td class="px-4 py-4 align-top   break-normal whitespace-normal" style="vertical-align: top;">
                                            <a href="{{ route('patients.show', $patient->id) }}"
                                                class="text-blue-600 hover:text-blue-800 font-medium">
                                                {{ $patient->name }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-4 align-top    break-normal whitespace-normal" style="vertical-align: top;">
                                            {{ $patient->mobile_phone }}
                                        </td>
                                        <td class="px-4 py-4 align-top    break-normal whitespace-normal" style="vertical-align: top;">
                                            {{ $patient->address }}
                                        </td>
                                        <td class="px-4 py-4 align-top    break-normal whitespace-normal" style="vertical-align: top;">
                                            {{ $patient->vishesh }}
                                        </td>
                                        <td
                                            class="px-4 py-4 align-top    break-normal whitespace-normal text-right text-sm font-medium flex gap-4" style="vertical-align: top;">
                                            <a href="{{ route('patients.edit', $patient->id) }}"
                                                class="text-indigo-600 hover:text-indigo-800 font-medium"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('patients.destroy', $patient->id) }}"
                                                onsubmit="return confirmDelete()">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-800 font-medium" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $patients->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this patient?");
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll("td").forEach(td => {
            let words = td.innerText.trim().split(/\s+/); // Split by spaces
            if (words.length > 4) {
                td.style.whiteSpace = "normal"; // Allow wrapping
                td.style.wordBreak = "break-word"; // Break long words if needed
            }
        });
    });
</script>

