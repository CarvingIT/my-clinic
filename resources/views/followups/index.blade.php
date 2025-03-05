<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.All Follow Ups') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-900">
                <div class="p-6 text-gray-900">

                    {{-- From Date, To Date, and Branch Selection --}}
                    <form method="GET" action="{{ route('followups.index') }}" class="mb-4 flex items-center gap-4">
                        <label for="from_date" class="font-semibold text-gray-700 dark:text-gray-300">From:</label>
                        <input type="date" id="from_date" name="from_date" value="{{ request('from_date') }}"
                            class="border rounded px-3 py-2 dark:bg-gray-800 dark:text-white">

                        <label for="to_date" class="font-semibold text-gray-700 dark:text-gray-300">To:</label>
                        <input type="date" id="to_date" name="to_date" value="{{ request('to_date') }}"
                            class="border rounded px-3 py-2 dark:bg-gray-800 dark:text-white">

                        <label for="branch_name" class="font-semibold text-gray-700 dark:text-gray-300">Branch:</label>
                        <select id="branch_name" name="branch_name"
                            class="border rounded px-3 py-2 dark:bg-gray-800 dark:text-white">
                            <option value="all" {{ request('branch_name') == 'all' ? 'selected' : '' }}>All Branches</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch }}" {{ request('branch_name') == $branch ? 'selected' : '' }}>
                                    {{ $branch }}
                                </option>
                            @endforeach
                        </select>

                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Filter</button>
                    </form>

                    {{-- Summary --}}
                    <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded mb-4">
                        <p class="text-lg font-bold text-gray-800 dark:text-gray-200">Summary</p>
                        <p>Branch: <span class="font-semibold">
                                {{ request('branch_name') ? request('branch_name') : 'All Branches' }}
                            </span></p>
                        <p>Selected Date Range:
                            <span class="font-semibold">
                                {{ request('from_date') ? request('from_date') : 'All' }} to
                                {{ request('to_date') ? request('to_date') : 'All' }}
                            </span>
                        </p>
                        <p>Total Patients: <span class="font-semibold">{{ $totalPatients }}</span></p>
                        <p>Total Follow-ups: <span class="font-semibold">{{ $totalFollowUps }}</span></p>
                        <p>Total Outstanding Balance: <span class="font-semibold text-orange-600">₹{{ number_format($totalDueAll, 2) }}</span></p>
                        <p>Total Payment Received: <span class="font-semibold">₹{{ number_format($totalIncome, 2) }}</span></p>
                    </div>
                   {{-- Summary ended --}}

                    <div class="overflow-hidden overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-white dark:bg-gray-900">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-s font-semibold text-gray-500 uppercase dark:text-gray-400">
                                        {{ __('messages.Created At') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-s font-semibold text-gray-500 uppercase dark:text-gray-400">
                                        {{ __('messages.Patient Name') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-s font-semibold text-gray-500 uppercase dark:text-gray-400">
                                        {{ __('messages.Amount') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-s font-semibold text-gray-500 uppercase dark:text-gray-400">
                                        {{ __('messages.Balance') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y dark:divide-gray-700 dark:text-white">
                                @foreach ($followUps as $followUp)
                                    @if ($followUp->patient)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $followUp->created_at->format('d M Y, h:i A') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('patients.show', $followUp->patient->id) }}"
                                                    class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $followUp->patient->name }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                                @if ($followUp->check_up_info)
                                                    @php
                                                        $checkUpInfo = json_decode($followUp->check_up_info, true);
                                                    @endphp
                                                    <p>{{ $checkUpInfo['amount'] ?? 'N/A' }}</p>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                                @if ($followUp->check_up_info)
                                                    <p>{{ $checkUpInfo['balance'] ?? 'N/A' }}</p>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $followUps->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
