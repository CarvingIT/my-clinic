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
                <div class="flex justify-between items-center mb-5">
                  <a href="{{ route('patients.create') }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">{{ __('messages.add_new_patient') }}</a>

                     <form method="GET" action="{{ route('patients.index') }}">
                       <input type="text" name="search" placeholder="{{ __('messages.search') }}"
                             value="{{ request('search') }}" class="border rounded px-2 py-1 text-black"/>
                       <button class="bg-gray-300 hover:bg-gray-400 text-black px-2 py-1 rounded">Search
                       </button>
                     </form>
                 </div>
                 @if (session('success'))
                    <div class="bg-green-200 text-green-800 p-2 rounded mb-5">
                       {{ session('success') }}
                    </div>
                 @endif
                 <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                       <thead class="bg-gray-50">
                          <tr>
                             <th scope="col"
                                   class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                   {{ __('messages.name') }}
                             </th>
                             <th scope="col"
                                   class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                   {{ __('messages.mobile_phone') }}
                             </th>
                             <th scope="col"
                                   class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                             Actions
                             </th>
                          </tr>
                       </thead>
                       <tbody class="bg-white divide-y divide-gray-200">
                          @foreach ($patients as $patient)
                             <tr>
                                   <td class="px-6 py-4 whitespace-nowrap">
                                     <a href="{{ route('patients.show', $patient->id) }}"
                                         class="text-indigo-600 hover:text-indigo-900">{{ $patient->name }}</a>
                                   </td>
                                   <td class="px-6 py-4 whitespace-nowrap">
                                      {{ $patient->mobile_phone }}
                                   </td>
                                   <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex gap-2">
                                         <a href="{{ route('patients.edit', $patient->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                         <form method="POST" action="{{ route('patients.destroy', $patient->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                         </form>
                                   </td>
                             </tr>
                          @endforeach
                       </tbody>
                    </table>
                 </div>
                 {{ $patients->links() }}
              </div>
           </div>
        </div>
    </div>
 </x-app-layout>
