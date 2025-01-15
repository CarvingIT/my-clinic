<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.All Follow Ups') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-hidden overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-s font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                        {{ __('messages.Created At') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-s font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                        {{ __('messages.Patient Name') }}
                                    </th>

                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($followUps as $followUp)
                                    <tr class="hover:bg-gray-50 transition duration-300">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $followUp->created_at->format('d M Y, h:i A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('patients.show', $followUp->patient->id) }}"
                                                class="text-indigo-600 hover:text-indigo-900">{{ $followUp->patient->name }}</a>
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                        {{ $followUps->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
