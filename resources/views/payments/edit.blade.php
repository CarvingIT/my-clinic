<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Payment
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg p-4 md:p-6">
                <form method="POST" action="{{ route('payments.update', $payment) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    @method('PUT')

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Patient</label>
                        <select name="patient_id" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient->id }}" @selected(old('patient_id', $payment->patient_id) == $patient->id)>
                                    {{ $patient->name }} ({{ $patient->patient_id }}) {{ $patient->mobile_phone ? '- ' . $patient->mobile_phone : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Follow-up (optional)</label>
                        <select name="follow_up_id" class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Payment without follow-up</option>
                            @foreach ($followUps as $followUp)
                                <option value="{{ $followUp->id }}" @selected(old('follow_up_id', $payment->follow_up_id) == $followUp->id)>
                                    #{{ $followUp->id }} - {{ $followUp->created_at->format('d M Y H:i') }}
                                </option>
                            @endforeach
                        </select>
                        @error('follow_up_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Amount</label>
                        <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount', $payment->amount) }}" class="w-full border-gray-300 rounded-md shadow-sm" required />
                        @error('amount') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Payment Method</label>
                        <select name="payment_method" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="cash" @selected(old('payment_method', $payment->payment_method) === 'cash')>Cash</option>
                            <option value="online" @selected(old('payment_method', $payment->payment_method) === 'online')>Online</option>
                            <option value="upi" @selected(old('payment_method', $payment->payment_method) === 'upi')>UPI</option>
                            <option value="card" @selected(old('payment_method', $payment->payment_method) === 'card')>Card</option>
                            <option value="bank" @selected(old('payment_method', $payment->payment_method) === 'bank')>Bank</option>
                        </select>
                        @error('payment_method') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Paid At</label>
                        <input type="datetime-local" name="paid_at" value="{{ old('paid_at', optional($payment->paid_at)->format('Y-m-d\\TH:i')) }}" class="w-full border-gray-300 rounded-md shadow-sm" required />
                        @error('paid_at') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Reference No (optional)</label>
                        <input type="text" name="reference_no" value="{{ old('reference_no', $payment->reference_no) }}" class="w-full border-gray-300 rounded-md shadow-sm" />
                        @error('reference_no') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Notes (optional)</label>
                        <textarea name="notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm">{{ old('notes', $payment->notes) }}</textarea>
                        @error('notes') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit" class="bg-indigo-600 text-white rounded-md px-4 py-2 text-sm font-semibold hover:bg-indigo-700">Update Payment</button>
                        <a href="{{ route('payments.index') }}" class="bg-gray-200 rounded-md px-4 py-2 text-sm font-semibold hover:bg-gray-300">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
