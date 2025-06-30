<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.Followup Data Analysis') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-lg p-6">
                <!-- Filters & Keyword Search Compact Bar -->
                <form method="GET" action="{{ route('data-analysis.index') }}"
                    class="bg-white border border-gray-200 rounded-lg p-4 mb-6 shadow-sm space-y-3 md:space-y-0 md:flex md:flex-wrap md:items-end md:gap-4">
                    <!-- Date From -->
                    <div class="flex-1 min-w-[150px]">
                        <x-input-label for="date_from" :value="__('From')" class="text-xs text-gray-500" />
                        <x-text-input id="date_from" name="date_from" type="date" class="block w-full text-sm"
                            value="{{ request('date_from') }}" />
                    </div>
                    <!-- Date To -->
                    <div class="flex-1 min-w-[150px]">
                        <x-input-label for="date_to" :value="__('To')" class="text-xs text-gray-500" />
                        <x-text-input id="date_to" name="date_to" type="date" class="block w-full text-sm"
                            value="{{ request('date_to') }}" />
                    </div>
                    <!-- Gender -->
                    <div class="flex-1 min-w-[130px]">
                        <x-input-label for="gender" :value="__('Gender')" class="text-xs text-gray-500" />
                        <select id="gender" name="gender"
                            class="block w-full rounded-md border-gray-300 text-sm shadow-sm">
                            <option value="">All</option>
                            <option value="M" @selected(request('gender') === 'M')>Male</option>
                            <option value="F" @selected(request('gender') === 'F')>Female</option>
                            <option value="O" @selected(request('gender') === 'O')>Other</option>
                        </select>
                    </div>
                    <!-- Age Group -->
                    <div class="flex-1 min-w-[130px]">
                        <x-input-label for="age_group" :value="__('Age Group')" class="text-xs text-gray-500" />
                        <select id="age_group" name="age_group"
                            class="block w-full rounded-md border-gray-300 text-sm shadow-sm">
                            <option value="">{{ __('All') }}</option>
                            <option value="0-10" @selected(request('age_group') === '0-10')>0 - 10</option>
                            <option value="10-20" @selected(request('age_group') === '10-20')>10 - 20</option>
                            <option value="20-30" @selected(request('age_group') === '20-30')>20 - 30</option>
                            <option value="30-40" @selected(request('age_group') === '30-40')>30 - 40</option>
                            <option value="40-50" @selected(request('age_group') === '40-50')>40 - 50</option>
                            <option value="50-60" @selected(request('age_group') === '50-60')>50 - 60</option>
                            <option value="60-70" @selected(request('age_group') === '60-70')>60 - 70</option>
                            <option value="70-80" @selected(request('age_group') === '70-80')>70 - 80</option>
                            <option value="80+" @selected(request('age_group') === '80+')>80+</option>
                        </select>
                    </div>
                    <!-- Weight Group -->
                    <div class="flex-1 min-w-[130px]">
                        <x-input-label for="weight_range" :value="__('Weight Range')" class="text-xs text-gray-500" />
                        <select id="weight_range" name="weight_range"
                            class="block w-full rounded-md border-gray-300 text-sm shadow-sm">
                            <option value="">{{ __('All') }}</option>
                            <option value="0-30" @selected(request('weight_range') === '0-30')>0 - 30 kg</option>
                            <option value="31-50" @selected(request('weight_range') === '31-50')>31 - 50 kg</option>
                            <option value="51-70" @selected(request('weight_range') === '51-70')>51 - 70 kg</option>
                            <option value="71-90" @selected(request('weight_range') === '71-90')>71 - 90 kg</option>
                            <option value="91-999" @selected(request('weight_range') === '91-999')>91+ kg</option>
                        </select>
                    </div>


                    <!-- Keyword -->
                    <div class="flex-1 min-w-[200px]">
                        <x-input-label for="keyword" :value="__('Keyword')" class="text-xs text-gray-500" />
                        <x-text-input id="keyword" name="keyword" type="text" placeholder="Search..."
                            class="block w-full text-sm" />
                    </div>
                    <!-- Buttons -->
                    <div class="flex items-center gap-2 mt-2 md:mt-6">
                        <x-primary-button name="add_keyword" value="1" type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-xs px-3 py-2">
                            + Filter
                        </x-primary-button>
                        {{-- <x-primary-button class="bg-green-600 hover:bg-green-700 text-xs px-3 py-2">
                            Apply
                        </x-primary-button> --}}
                    </div>
                </form>
                <!-- Active Keywords -->
                @if (!empty($keywords))
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach ($keywords as $keyword)
                            <form method="GET" action="{{ route('data-analysis.index') }}"
                                class="inline-flex items-center bg-indigo-100 text-indigo-800 rounded-full px-3 py-1 text-xs font-semibold">
                                <input type="hidden" name="remove_keyword" value="{{ $keyword }}">
                                <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                                <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                                <input type="hidden" name="gender" value="{{ request('gender') }}">
                                <button type="submit" class="mr-1 font-bold hover:text-red-600">×</button>
                                {{ $keyword }}
                            </form>
                        @endforeach
                        <form method="GET" action="{{ route('data-analysis.index') }}">
                            <x-secondary-button type="submit" name="clear_keywords" value="1" class="text-xs">
                                Clear all
                            </x-secondary-button>
                        </form>
                    </div>
                @endif
                <!-- Descriptive Filter Summary -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded mb-6">
                    <h3 class="font-semibold text-blue-800 mb-2">Filter Summary</h3>
                    <ul class="text-sm text-blue-900 space-y-1">
                        @if (request('date_from') || request('date_to'))
                            <li>
                                📅 Showing follow-ups
                                @if (request('date_from'))
                                    from
                                    <strong>{{ \Carbon\Carbon::parse(request('date_from'))->format('d M Y') }}</strong>
                                @endif
                                @if (request('date_to'))
                                    to
                                    <strong>{{ \Carbon\Carbon::parse(request('date_to'))->format('d M Y') }}</strong>
                                @endif
                            </li>
                        @endif
                        @if (request('gender'))
                            <li>
                                🧑‍⚕️ Gender filter: <strong>
                                    @php
                                        $genderLabels = ['M' => 'Male', 'F' => 'Female', 'O' => 'Other'];
                                    @endphp
                                    {{ $genderLabels[request('gender')] ?? request('gender') }}
                                </strong>
                            </li>
                        @endif
                        @if (request('age_group'))
                            <li>
                                👶 Age Group filter:
                                <strong>{{ request('age_group') }}</strong>
                            </li>
                        @endif
                        @if (request('weight_range'))
                            <li>
                                ⚖️ Weight Range:
                                <strong>{{ str_replace('-', ' - ', request('weight_range')) }} kg</strong>
                            </li>
                        @endif


                        @if (!empty($keywords))
                            <li>
                                🔍 Keyword(s) applied:
                                <span class="font-semibold text-indigo-700">
                                    {{ implode(', ', $keywords) }}
                                </span>
                            </li>
                        @endif
                        <li>
                            📊 Total results: <strong>{{ $matchCount }}</strong>
                        </li>
                    </ul>
                </div>
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    {{ __('messages.Patient Name') }}
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    {{ __('messages.reference') }}
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    {{ __('नाडी / लक्षणे') }}
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    {{ __('चिकित्सा') }}
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    {{ __('निदान ') }}
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    {{ __('messages.Vishesh') }}
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    {{ __('messages.Followup Timestamp') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($followUps as $followUp)
                                <tr>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('patients.show', $followUp->patient->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900 block">
                                            {{ optional($followUp->patient)->name ?? 'N/A' }}
                                            @if ($followUp->patient && $followUp->patient->birthdate)
                                                [{{ intval(\Carbon\Carbon::parse($followUp->patient->birthdate)->diffInYears($followUp->created_at)) }}]
                                            @elseif ($followUp->patient && $followUp->patient->age)
                                                [{{ intval($followUp->patient->age) }}y]
                                            @endif
                                        </a>


                                        @if ($followUp->patient && ($followUp->patient->height || $followUp->patient->weight))
                                            <div class="text-gray-500 text-sm">
                                                {{ $followUp->patient->height ? $followUp->patient->height . 'cm' : 'N/A' }}
                                                |
                                                {{ $followUp->patient->weight ? $followUp->patient->weight . 'kg' : 'N/A' }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ optional($followUp->patient)->reference ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $checkUpInfo = json_decode($followUp->check_up_info, true) ?? [];
                                        @endphp
                                        @if (!empty($checkUpInfo['nadi']))
                                            <p>{!! $checkUpInfo['nadi'] !!}</p>
                                        @endif
                                        @if (!empty($followUp->diagnosis))
                                            <p>{!! $followUp->diagnosis !!}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if (!empty($checkUpInfo['chikitsa']))
                                            <p>{!! $checkUpInfo['chikitsa'] !!}</p>
                                        @endif
                                        {{-- @if (!empty($checkUpInfo['nidan']))
                                            <p>{!! $checkUpInfo['nidan'] !!}</p>
                                        @endif
                                        @if (!empty($checkUpInfo['days']))
                                            <p>{{ __('Days') }}: {{ $checkUpInfo['days'] }}</p>
                                        @endif
                                        @if (!empty($checkUpInfo['packets']))
                                            <p>{{ __('Packets') }}: {{ $checkUpInfo['packets'] }}</p>
                                        @endif --}}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if (!empty($checkUpInfo['nidan']))
                                            <p>{!! $checkUpInfo['nidan'] !!}</p>
                                        @endif

                                    </td>
                                    <td class="px-6 py-4">
                                        @if (!empty($followUp->patient->vishesh))
                                            <p>{!! optional($followUp->patient)->vishesh !!}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $followUp->created_at->format('d M Y, h:i A') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $followUps->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
