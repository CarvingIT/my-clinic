<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 leading-tight">
            {{ __('Add Follow Up') }} - {{ $patient->name }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-lg">
                <div class="p-8 text-gray-900">
                    <form method="POST" action="{{ route('followups.store') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ $patient->id }}"/>

                        @foreach($parameters as $parameter)
                            <div>
                                <x-input-label for="{{ $parameter->param }}" :value="$parameter->param" class="block text-sm font-medium text-gray-700" />
                                @if($parameter->type == 'text')
                                    <x-text-input
                                        id="{{ $parameter->param }}"
                                        class="mt-2 block w-full border-2 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        type="text"
                                        name="{{ $parameter->param }}"
                                    />
                                @elseif($parameter->type == 'number')
                                    <x-text-input
                                        id="{{ $parameter->param }}"
                                        class="mt-2 block w-full border-2 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        type="number"
                                        name="{{ $parameter->param }}"
                                    />
                                @elseif($parameter->type == 'options')
                                    <select
                                        id="{{ $parameter->param }}"
                                        name="{{ $parameter->param }}"
                                        class="mt-2 block w-full border-2 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    >
                                        <option value="">Please Select</option>
                                        @foreach(json_decode($parameter->option_values) as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                <x-input-error :messages="$errors->get('{{ $parameter->param }}')" class="mt-2 text-red-500 text-sm" />
                            </div>
                        @endforeach

                        <!-- Diagnosis -->
                        <div>
                            <x-input-label for="diagnosis" :value="__('messages.diagnosis')" class="block text-sm font-medium text-gray-700" />
                            <x-text-input
                                id="diagnosis"
                                class="mt-2 block w-full border-2 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                type="text"
                                name="diagnosis"
                            />
                            <x-input-error :messages="$errors->get('diagnosis')" class="mt-2 text-red-500 text-sm" />
                        </div>

                        <!-- Treatment -->
                        <div>
                            <x-input-label for="treatment" :value="__('messages.treatment')" class="block text-sm font-medium text-gray-700" />
                            <x-text-input
                                id="treatment"
                                class="mt-2 block w-full border-2 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                type="text"
                                name="treatment"
                            />
                            <x-input-error :messages="$errors->get('treatment')" class="mt-2 text-red-500 text-sm" />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <x-primary-button
                                class="py-3 px-6 rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:ring focus:ring-indigo-300 focus:outline-none transition duration-200 shadow-md"
                            >
                                {{ __('Add Follow Up') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
