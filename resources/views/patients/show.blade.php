<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('messages.patient_details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="p-6">
                    <!-- Patient Details Section -->
                    <div class="mb-8 border-b pb-4">
                        <h2 class="text-2xl font-bold text-gray-700 mb-4">
                            {{ __('messages.patient_details') }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-600">
                            <p>
                                <span class="font-medium text-gray-800">{{ __('messages.name') }}:</span> {{$patient->name}}
                            </p>
                            <p>
                                <span class="font-medium text-gray-800">{{ __('messages.address') }}:</span> {{$patient->address}}
                            </p>
                            <p>
                                <span class="font-medium text-gray-800">{{ __('messages.occupation') }}:</span> {{$patient->occupation}}
                            </p>
                            <p>
                                <span class="font-medium text-gray-800">{{ __('messages.mobile_phone') }}:</span> {{$patient->mobile_phone}}
                            </p>
                            <p class="col-span-1 md:col-span-2">
                                <span class="font-medium text-gray-800">{{ __('messages.remark') }}:</span> {{$patient->remark}}
                            </p>
                        </div>
                    </div>

                    <!-- Add Follow Up Button -->
                    <div class="flex justify-end mb-8">
                        <a href="{{ route('followups.create', ['patient' => $patient->id]) }}"
                           class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-6 rounded-md shadow-md transition duration-300">
                            {{ __('messages.add_follow_up') }}
                        </a>
                    </div>

                    <!-- Follow Ups Section -->
                    <h2 class="text-2xl font-bold text-gray-700 mb-4">{{ __('messages.follow_ups') }}</h2>

                    @if ($patient->followUps->count() > 0)
                        <div class="overflow-hidden border rounded-lg shadow-md">
                            <table class="min-w-full bg-white divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase">
                                            {{ __('messages.diagnosis') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase">
                                            {{ __('messages.treatment') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase">
                                            {{ __('Created At') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($patient->followUps->sortByDesc('created_at') as $followUp)
                                        <tr class="hover:bg-gray-50 transition duration-300">
                                            <td class="px-6 py-4 text-gray-600">
                                                {{$followUp->diagnosis}}
                                            </td>
                                            <td class="px-6 py-4 text-gray-600">
                                                {{$followUp->treatment}}
                                            </td>
                                            <td class="px-6 py-4 text-gray-600">
                                                {{$followUp->created_at->format('d M Y, h:i A')}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-600 bg-gray-100 p-4 rounded-md shadow-sm">
                            No follow-ups added for the patient.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
