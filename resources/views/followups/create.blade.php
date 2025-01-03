<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add Follow Up') }} - {{ $patient->name }}
        </h2>
    </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
             <div class="p-6 text-gray-900 dark:text-gray-100">
                  <form method="POST" action="{{ route('followups.store') }}">
                      @csrf
                      <input type="hidden" name="patient_id" value="{{$patient->id}}" />

                       <div class="mb-6">
                           <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">  {{ __('नाडी') }}</h2>
                             <!-- Checkboxes for Nadi Parameters -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                                   <div class="flex items-center">
                                        <input type="checkbox" id="vata" name="vata" value="1" class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md" />
                                          <x-input-label for="vata" :value="__('वात')" class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                       </div>
                                    <div class="flex items-center">
                                          <input type="checkbox" id="pitta" name="pitta" value="1" class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md" />
                                        <x-input-label for="pitta" :value="__('पित्त')" class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                       </div>
                                    <div class="flex items-center">
                                       <input type="checkbox" id="kapha" name="kapha" value="1" class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md" />
                                      <x-input-label for="kapha" :value="__('कफ')" class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                     </div>
                                     <div class="flex items-center">
                                         <input type="checkbox" id="sukshma" name="sukshma" value="1" class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md" />
                                         <x-input-label for="sukshma" :value="__('सूक्ष्म')" class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                        </div>
                                      <div class="flex items-center">
                                          <input type="checkbox" id="kothin" name="kothin" value="1" class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md" />
                                          <x-input-label for="kothin" :value="__('कठीण')" class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                      </div>
                                      <div class="flex items-center">
                                          <input type="checkbox" id="sam" name="sam" value="1" class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md" />
                                        <x-input-label for="sam" :value="__('साम')" class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="prana" name="prana" value="1" class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md" />
                                       <x-input-label for="prana" :value="__('प्राण')" class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                    </div>
                                     <div class="flex items-center">
                                            <input type="checkbox" id="vyana" name="vyana" value="1" class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md" />
                                            <x-input-label for="vyana" :value="__('व्यान')" class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                       </div>
                                     <div class="flex items-center">
                                           <input type="checkbox" id="udana" name="udana" value="1" class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md" />
                                           <x-input-label for="udana" :value="__('उदान')" class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                      </div>
                                   <div class="flex items-center">
                                        <input type="checkbox" id="apana" name="apana" value="1" class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md" />
                                        <x-input-label for="apana" :value="__('अपान')" class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                      </div>
                                       <div class="flex items-center">
                                         <input type="checkbox" id="samana" name="samana" value="1" class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md" />
                                         <x-input-label for="samana" :value="__('समान')" class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                      </div>
                                     <div class="flex items-center">
                                        <input type="checkbox" id="rasa" name="rasa" value="1" class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md" />
                                        <x-input-label for="rasa" :value="__('रस')" class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                       </div>
                                     <div class="flex items-center">
                                         <input type="checkbox" id="rakta" name="rakta" value="1" class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md" />
                                         <x-input-label for="rakta" :value="__('रक्त')" class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                     </div>
                                       <div class="flex items-center">
                                          <input type="checkbox" id="ambakshaya" name="ambakshaya" value="1" class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md" />
                                         <x-input-label for="ambakshaya" :value="__('अन्नक्षय')" class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                       </div>
                                </div>

                                 <!--Text input for symptoms -->
                                 <div class="mt-4">
                                     <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">  {{ __('लक्षणे') }} </h2>
                                       <textarea id="lakshane" name="diagnosis" rows="4" class="block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm "></textarea>
                                       <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />
                                 </div>


                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-3"> {{ __('चिकित्सा') }}</h2>
                             <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                                      <div class="flex items-center">
                                           <input type="checkbox" id="arsh" name="arsh" value="1" class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md" />
                                             <x-input-label for="arsh" :value="__('अर्श')" class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                         </div>
                                       <div class="flex items-center">
                                            <input type="checkbox" id="grahni" name="grahni" value="1" class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md" />
                                          <x-input-label for="grahni" :value="__('ग्रहणी')" class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                       </div>
                                       <div class="flex items-center">
                                             <input type="checkbox" id="jwar" name="jwar" value="1" class="mr-2 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md" />
                                           <x-input-label for="jwar" :value="__('ज्वर/प्रतिश्याय')" class="text-gray-700 dark:text-gray-300 font-medium cursor-pointer" />
                                         </div>
                                </div>
                           <!--Text input for treatment-->
                           <div class="mt-4">
                                  <x-input-label for="treatment" :value="__('messages.treatment')" />
                                    <x-text-input id="treatment" class="block mt-1 w-full" type="text" name="treatment"  />
                                 <x-input-error :messages="$errors->get('treatment')" class="mt-2" />
                         </div>

                         <div class="mt-4">
                                <x-input-label for="nidan" :value="__('निदान')" />
                                   <x-text-input id="nidan" class="block mt-1 w-full" type="text" name="nidan" />
                                    <x-input-error :messages="$errors->get('nidan')" class="mt-2" />
                             </div>

                            <div class="mt-4">
                                  <x-input-label for="upashay" :value="__('उपशय')" />
                                   <x-text-input id="upashay" class="block mt-1 w-full" type="text" name="upashay" />
                                   <x-input-error :messages="$errors->get('upashay')" class="mt-2" />
                             </div>

                            <div class="mt-4">
                                  <x-input-label for="salla" :value="__('सल्ला')" />
                                    <x-text-input id="salla" class="block mt-1 w-full" type="text" name="salla" />
                                  <x-input-error :messages="$errors->get('salla')" class="mt-2" />
                             </div>

                             <!-- Text Input for amount-->
                            <div class="mt-4">
                                   <x-input-label for="amount" :value="__('Amount')" />
                                   <x-text-input id="amount" class="block mt-1 w-full" type="number" name="amount"  />
                                 <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                           </div>

                             <!-- Text Input for balance-->
                           <div class="mt-4">
                                  <x-input-label for="balance" :value="__('Balance')" />
                                 <x-text-input id="balance" class="block mt-1 w-full" type="number" name="balance" />
                                 <x-input-error :messages="$errors->get('balance')" class="mt-2" />
                            </div>

                           <!-- Select Input for Payment Method-->
                           <div class="mt-4">
                                 <x-input-label for="payment_method" :value="__('Payment Method')" />
                                <select id="payment_method" name="payment_method" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                                     <option value="">Please Select</option>
                                      <option value="cash">Cash</option>
                                      <option value="card">Card</option>
                                       <option value="online">Online</option>
                                    </select>
                                 <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                           </div>

                            <!-- Text Input for certificate-->
                            <div class="mt-4">
                                  <x-input-label for="certificate" :value="__('Certificate')" />
                                  <x-text-input id="certificate" class="block mt-1 w-full" type="text" name="certificate" />
                                  <x-input-error :messages="$errors->get('certificate')" class="mt-2" />
                             </div>

                             <div class="mt-4">
                                  <x-input-label for="drawing" :value="__('Drawing')" />
                                    <x-text-input id="drawing" class="block mt-1 w-full" type="text" name="drawing"  />
                                 <x-input-error :messages="$errors->get('drawing')" class="mt-2" />
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
