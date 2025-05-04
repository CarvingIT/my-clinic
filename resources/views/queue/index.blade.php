<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('messages.Queue') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-xl">
                <div class="p-6 text-gray-900">
                    <!-- Success Message -->
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md shadow-sm mb-6">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Table Section -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ __('messages.Name') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ __('Appointment At') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ __('Added By') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ __('messages.Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($queue as $entry)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <a href="{{ route('patients.show', $entry->patient->id) }}"
                                                class="text-blue-600 hover:text-blue-800">
                                                {{ $entry->patient->name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $entry->in_queue_at->format('d-m-Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $entry->addedBy->name }}
                                        </td>
                                        <td class="px-4 py-4 align-top break-normal whitespace-normal text-right text-sm justify-end font-medium flex gap-4"
                                            style="vertical-align: top;">
                                            <form method="POST" action="{{ route('queue.remove', $entry->id) }}"
                                                onsubmit="return confirm('Are you sure you want to remove this patient from the queue?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-800 font-medium" title="Remove">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('queue.in', $entry->id) }}"
                                                onsubmit="return confirm('Are you sure you want to mark this patient as in?')">
                                                @csrf
                                                <button type="submit"
                                                    class="text-green-600 hover:text-green-800 font-medium" title="Mark as In">
                                                    <i class="fas fa-check"></i> In
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                            {{ __('messages.No patients in queue') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
