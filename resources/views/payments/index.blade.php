<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Payments
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

                        <button type="submit" class="bg-indigo-600 text-white rounded-md px-3 py-2 text-sm font-semibold hover:bg-indigo-700">
                            Filter
                        </button>
                    </form>

                    <a href="{{ route('payments.create') }}" class="bg-green-600 text-white rounded-md px-4 py-2 text-sm font-semibold hover:bg-green-700 whitespace-nowrap">
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
                                <tr>
                                    <td class="px-3 py-2">{{ optional($payment->paid_at)->format('d M Y H:i') }}</td>
                                    <td class="px-3 py-2">
                                        <a class="text-blue-600 hover:underline" href="{{ route('patients.show', $payment->patient_id) }}">
                                            {{ optional($payment->patient)->name ?? 'Unknown' }}
                                        </a>
                                    </td>
                                    <td class="px-3 py-2 uppercase">{{ $payment->payment_method }}</td>
                                    <td class="px-3 py-2 text-right font-semibold">₹{{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-3 py-2">{{ $payment->source }}</td>
                                    <td class="px-3 py-2">
                                        <div class="flex gap-2">
                                            <a href="{{ route('payments.edit', $payment) }}" class="text-indigo-600 hover:underline">Edit</a>
                                            <form method="POST" action="{{ route('payments.destroy', $payment) }}" onsubmit="return confirm('Void this payment?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">Void</button>
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
            </div>
        </div>
    </div>
</x-app-layout>
