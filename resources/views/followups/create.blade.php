<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Follow Up') }} - {{ $patient->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('followups.store') }}">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ $patient->id }}"/>

                        @foreach($parameters as $parameter)
                            <div class="mt-4">
                                <x-input-label for="{{ $parameter->param }}" :value="$parameter->param"/>
                                @if($parameter->type == 'text')
                                    <x-text-input id="{{ $parameter->param }}" class="block mt-1 w-full"
                                                  type="text" name="{{ $parameter->param }}"/>
                                @elseif($parameter->type == 'number')
                                    <x-text-input id="{{ $parameter->param }}" class="block mt-1 w-full"
                                                  type="number" name="{{ $parameter->param }}"/>
                                @elseif($parameter->type == 'options')
                                    <select id="{{ $parameter->param }}" name="{{ $parameter->param }}"
                                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                        <option value="">Please Select</option>
                                        @foreach(json_decode($parameter->option_values) as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                <x-input-error :messages="$errors->get('{{ $parameter->param }}')" class="mt-2"/>
                            </div>
                        @endforeach
                         <div class="mt-4">
                            <x-input-label for="diagnosis" :value="__('messages.diagnosis')"/>
                            <x-text-input id="diagnosis" class="block mt-1 w-full" type="text" name="diagnosis"/>
                            <x-input-error :messages="$errors->get('diagnosis')" class="mt-2"/>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="treatment" :value="__('messages.treatment')"/>
                            <x-text-input id="treatment" class="block mt-1 w-full" type="text" name="treatment"/>
                            <x-input-error :messages="$errors->get('treatment')" class="mt-2"/>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Add Follow Up') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
