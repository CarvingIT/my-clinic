<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.Payments') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg p-4 md:p-6">
                @if (session('success'))
                    <div class="mb-4 rounded-md bg-green-100 text-green-800 px-4 py-2">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Navigation Tabs -->
                <div class="flex border-b border-gray-200 dark:border-gray-700 mb-6">
                    <a href="{{ route('payments.index', ['tab' => 'history']) }}" 
                       class="py-2.5 px-6 font-semibold text-sm border-b-2 transition duration-200 {{ request('tab') !== 'groups' ? 'border-indigo-650 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        <i class="fas fa-history mr-2"></i>Payment History
                    </a>
                    <a href="{{ route('payments.index', ['tab' => 'groups']) }}" 
                       class="py-2.5 px-6 font-semibold text-sm border-b-2 transition duration-200 {{ request('tab') === 'groups' ? 'border-indigo-650 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        <i class="fas fa-users-cog mr-2"></i>Patient Groups (Families)
                    </a>
                </div>

                @if (request('tab') === 'groups')
                    {{-- Groups Tab --}}
                    <div class="flex flex-col md:flex-row gap-3 md:items-center md:justify-between mb-4">
                        <form method="GET" action="{{ route('payments.index') }}" class="flex gap-2 w-full md:max-w-md">
                            <input type="hidden" name="tab" value="groups" />
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search family group..."
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-800 rounded-md shadow-sm text-sm flex-1 focus:ring-indigo-500 focus:border-indigo-500" />
                            <button type="submit" class="bg-indigo-600 text-white rounded-md px-4 py-2 text-sm font-semibold hover:bg-indigo-700 transition">
                                Filter
                            </button>
                        </form>

                        <a href="{{ route('groups.create') }}" class="bg-green-600 text-white rounded-md px-4 py-2 text-sm font-semibold hover:bg-green-700 whitespace-nowrap transition">
                            <i class="fas fa-plus mr-1"></i>Create Patient Group
                        </a>
                    </div>

                    <div class="overflow-x-auto mt-2">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Group Name</th>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Description</th>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Members</th>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($groups as $group)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">{{ $group->name }}</td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $group->description ?? 'No description' }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex flex-wrap gap-1.5">
                                                @forelse($group->members as $member)
                                                    <a href="{{ route('patients.show', $member->id) }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300 dark:hover:bg-indigo-900/50 transition">
                                                        {{ $member->name }}
                                                    </a>
                                                @empty
                                                    <span class="text-gray-400 dark:text-gray-550 text-xs">No members</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                @if($group->members->isNotEmpty())
                                                    <a href="{{ route('payments.create', ['patient_id' => $group->members->first()->id]) }}" class="inline-flex items-center bg-green-50 text-green-700 hover:bg-green-100 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-900/50 px-2.5 py-1 rounded text-xs font-semibold transition" title="Record Payment">
                                                        <i class="fas fa-receipt mr-1"></i> Record Payment
                                                    </a>
                                                @endif
                                                <a href="{{ route('groups.edit', $group) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('groups.destroy', $group) }}" onsubmit="return confirm('Are you sure you want to delete this group? Members will remain but lose their group association.');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-655 hover:text-red-950 dark:text-red-400 dark:hover:text-red-350 transition" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No patient groups found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $groups->links() }}
                    </div>
                @else
                    {{-- Payments Tab --}}
                    <div class="flex flex-col md:flex-row gap-3 md:items-center md:justify-between mb-4">
                        <form method="GET" action="{{ route('payments.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-2 w-full">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search patient"
                                class="border-gray-300 rounded-md shadow-sm text-sm" />

                            <select name="payment_method" class="border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="all">All Methods</option>
                                <option value="cash" @selected(request('payment_method') === 'cash')>Cash</option>
                                <option value="online" @selected(request('payment_method') === 'online')>Online</option>
                                <option value="upi" @selected(request('payment_method') === 'upi')>UPI</option>
                                <option value="card" @selected(request('payment_method') === 'card')>Card</option>
                                <option value="bank" @selected(request('payment_method') === 'bank')>Bank</option>
                            </select>

                            <input type="date" name="from_date" value="{{ request('from_date') }}" class="border-gray-300 rounded-md shadow-sm text-sm" />
                            <input type="date" name="to_date" value="{{ request('to_date') }}" class="border-gray-300 rounded-md shadow-sm text-sm" />

                            <button type="submit" class="bg-indigo-650 text-white rounded-md px-3 py-2 text-sm font-semibold hover:bg-indigo-750 transition">
                                Filter
                            </button>
                        </form>

                        <a href="{{ route('payments.create') }}" class="bg-green-600 text-white rounded-md px-4 py-2 text-sm font-semibold hover:bg-green-700 whitespace-nowrap transition">
                            Add Payment
                        </a>
                    </div>

                    <div class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Total (filtered): ₹{{ number_format($totalAmount, 2) }}
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="text-left px-3 py-2">Date</th>
                                    <th class="text-left px-3 py-2">Patient</th>
                                    <th class="text-left px-3 py-2">Method</th>
                                    <th class="text-right px-3 py-2">Amount</th>
                                    <th class="text-left px-3 py-2">Source</th>
                                    <th class="text-left px-3 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($payments as $payment)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                                        <td class="px-3 py-2">{{ optional($payment->paid_at)->format('d M Y H:i') }}</td>
                                        <td class="px-3 py-2">
                                            <a class="text-blue-600 hover:underline font-medium" href="{{ route('patients.show', $payment->patient_id) }}">
                                                {{ optional($payment->patient)->name ?? 'Unknown' }}
                                            </a>
                                        </td>
                                        <td class="px-3 py-2 uppercase">{{ $payment->payment_method }}</td>
                                        <td class="px-3 py-2 text-right font-semibold">₹{{ number_format($payment->amount, 2) }}</td>
                                        <td class="px-3 py-2">{{ $payment->source }}</td>
                                        <td class="px-3 py-2">
                                            <div class="flex items-center gap-3">
                                                <a href="{{ route('payments.edit', $payment) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('payments.destroy', $payment) }}" onsubmit="return confirm('Void this payment?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition" title="Void">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 py-6 text-center text-gray-500">No payments found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>
            </div>
        </div>
    </div>
</x-app-layout>
