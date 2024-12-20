<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.patient_details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-5">
                        <h2 class="text-xl font-semibold">{{ __('messages.patient_details') }}</h2>
                        <p>
                            <span class="font-bold">{{ __('messages.name') }}:</span> {{$patient->name}}
                        </p>
                        <p>
                            <span class="font-bold">{{ __('messages.address') }}:</span> {{$patient->address}}
                        </p>
                        <p>
                            <span class="font-bold">{{ __('messages.occupation') }}:</span> {{$patient->occupation}}
                        </p>
                        <p>
                            <span class="font-bold">{{ __('messages.mobile_phone') }}:</span> {{$patient->mobile_phone}}
                        </p>
                        <p>
                            <span class="font-bold">{{ __('messages.remark') }}:</span> {{$patient->remark}}
                        </p>
                    </div>
                    <div class="flex justify-end mb-5">
                        <a href="{{ route('followups.create', ['patient' => $patient->id]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">{{ __('messages.add_follow_up') }}</a>
                    </div>

                    <h2 class="text-xl font-semibold mb-2">{{ __('messages.follow_ups') }}</h2>

                    @if ($patient->followUps->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('messages.diagnosis') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('messages.treatment') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Created At') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($patient->followUps->sortByDesc('created_at') as $followUp)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{$followUp->diagnosis}}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{$followUp->treatment}}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{$followUp->created_at}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>No Follow ups added for the patient.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
