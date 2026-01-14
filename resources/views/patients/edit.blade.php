<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Patient') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-lg p-6 border border-gray-300">
                <form method="POST" action="{{ route('patients.update', $patient->id) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Patient Photo Upload Section -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Patient Photo</h3>

                        <!-- Photo Display and Preview -->
                        <div class="mb-6">
                            <h4 class="text-md font-medium text-gray-700 mb-4">Photo Management</h4>
                            <div class="flex items-center gap-8">
                                <!-- Current Photo Display -->
                                <div class="flex flex-col items-center">
                                    <h5 class="text-sm font-medium text-gray-600 mb-2">Current Photo</h5>
                                    <div class="flex items-center gap-4">
                                        @php
                                            $currentPhoto = $patient->uploads()->where('photo_type', 'patient_photo')->orderByDesc('created_at')->first();
                                        @endphp

                                        @if($currentPhoto)
                                            <div class="relative">
                                                <img src="{{ route('uploads.show', $currentPhoto->id) }}"
                                                     alt="Current Patient Photo"
                                                     class="w-20 h-20 rounded-full object-cover border-2 border-gray-300 shadow-md">
                                                <div class="absolute -bottom-1 -right-1 bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                                                    Current
                                                </div>
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                <p><strong>Status:</strong> Photo exists</p>
                                                <p><strong>Uploaded:</strong> {{ $currentPhoto->created_at->format('d M Y') }}</p>
                                            </div>
                                        @else
                                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-gray-400 to-gray-500 flex items-center justify-center border-2 border-gray-300 shadow-md">
                                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                <p><strong>Status:</strong> No photo uploaded</p>
                                                <p><strong>Default avatar will be used</strong></p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- New Photo Preview (Hidden by default) -->
                                <div class="flex flex-col items-center hidden" id="newPhotoPreviewContainer">
                                    <h5 class="text-sm font-medium text-blue-600 mb-2">New Photo Preview</h5>
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <img id="newPhotoPreview"
                                                 src=""
                                                 alt="New Photo Preview"
                                                 class="w-20 h-20 rounded-full object-cover border-2 border-blue-300 shadow-md">
                                            <div class="absolute -bottom-1 -right-1 bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                                                Preview
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-600" id="previewStatus">
                                            <p><strong>Status:</strong> New photo selected</p>
                                            <p id="previewDetails"><strong>Select or capture a photo</strong></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-4">
                            <!-- File Upload -->
                            <div>
                                <x-input-label for="photo_file" :value="__('Upload Photo from File')" />
                                <input type="file" id="photo_file" name="photo_file" accept="image/*"
                                    class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-2">
                                <x-input-error :messages="$errors->get('photo_file')" class="mt-1" />
                            </div>

                            <!-- Camera Capture Button -->
                            <div>
                                <button type="button" id="openCameraModal"
                                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                                    📷 Capture Photo with Camera
                                </button>
                            </div>

                            <!-- Hidden inputs for captured photos -->
                            <input type="file" id="photoFileInput" name="photos[]" multiple style="display: none;">
                            <input type="hidden" id="photoTypesInput" name="photo_types">

                            <!-- Camera Modal -->
                            <div id="cameraModal"
                                class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden flex justify-center items-center z-50 p-4">
                                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">
                                    <!-- Header -->
                                    <div class="flex items-center justify-between p-6 border-b border-gray-200">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h2 class="text-xl font-semibold text-gray-900">Capture Patient Photo</h2>
                                                <p class="text-sm text-gray-500">Take a clear photo for the patient's profile</p>
                                            </div>
                                        </div>
                                        <button id="closeCameraModal" type="button"
                                            class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-colors">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 flex flex-col lg:flex-row overflow-hidden">
                                        <!-- Camera Section -->
                                        <div class="flex-1 flex flex-col p-6 space-y-4">
                                            <div class="space-y-2">
                                                <label for="cameraSelect" class="block text-sm font-medium text-gray-700">Camera Source</label>
                                                <select id="cameraSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                                    <option value="">Select a camera</option>
                                                </select>
                                            </div>

                                            <div class="flex-1 relative bg-gray-900 rounded-xl overflow-hidden shadow-inner flex items-center justify-center min-h-[300px]">
                                                <video id="cameraPreview" class="max-w-full max-h-full object-contain rounded-lg" autoplay playsinline></video>
                                                <div class="absolute inset-0 border-2 border-dashed border-gray-400 rounded-xl pointer-events-none opacity-20"></div>
                                                <!-- Overlay instructions -->
                                                <div class="absolute bottom-4 left-4 right-4 bg-black bg-opacity-75 text-white p-3 rounded-lg">
                                                    <p class="text-sm text-center">📸 Position the patient clearly in the frame</p>
                                                </div>
                                            </div>

                                            <div class="flex justify-center">
                                                <button id="captureBtn" type="button" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 flex items-center space-x-2">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    <span>Capture Photo</span>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Photo Preview Section -->
                                        <div class="w-full lg:w-80 border-t lg:border-t-0 lg:border-l border-gray-200 flex flex-col">
                                            <div class="p-6 border-b border-gray-200">
                                                <h3 class="text-lg font-semibold text-gray-900">Photo Preview</h3>
                                                <p class="text-sm text-gray-500 mt-1">Review your captured photo</p>
                                            </div>

                                            <div class="flex-1 p-6 flex flex-col items-center justify-center space-y-4">
                                                <!-- No Photo State -->
                                                <div id="noPhotoState" class="text-center space-y-3">
                                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto">
                                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">No photo captured yet</p>
                                                        <p class="text-xs text-gray-500">Click "Capture Photo" to take a picture</p>
                                                    </div>
                                                </div>

                                                <!-- Captured Photo Container -->
                                                <div id="capturedPhotoContainer" class="hidden space-y-4 w-full">
                                                    <div class="relative">
                                                        <img id="capturedPhotoPreview" class="w-full h-auto object-contain rounded-lg border border-gray-300" alt="Captured photo">
                                                    </div>

                                                    <div class="flex space-x-3">
                                                        <button id="retakeBtn" type="button" class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors flex items-center justify-center space-x-2" disabled>
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                            </svg>
                                                            <span>Retake</span>
                                                        </button>

                                                        <button id="doneBtn" type="button" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center space-x-2" disabled>
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            <span>Done</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Patient ID -->
                        {{-- <div>
                            <x-input-label for="patient_id" :value="__('Patient ID')" />
                            <x-text-input id="patient_id"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="text" name="patient_id" :value="$patient->patient_id" autocomplete="off" />
                            <x-input-error :messages="$errors->get('patient_id')" class="mt-1" />
                        </div> --}}

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('messages.name')" />
                            <x-text-input id="name"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="text" name="name" :value="$patient->name" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <!-- Birthdate Field -->
                        <div>
                            <x-input-label for="birthdate" :value="__('Birthdate')" />
                            <x-text-input id="birthdate"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="date" name="birthdate"
                                value="{{ old('birthdate', isset($patient->birthdate) ? \Carbon\Carbon::parse($patient->birthdate)->format('Y-m-d') : '') }}"
                                oninput="calculateAgeFromBirthdate()" />
                            <x-input-error :messages="$errors->get('birthdate')" class="mt-1" />
                        </div>

                        <!-- Age Field -->
                        <div class="mt-2">
                            <x-input-label for="age" :value="__('Age')" />
                            <x-text-input id="age"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2 reverse-transliteration"
                                type="text" name="age" placeholder="Enter Age (If no birthdate)"
                                value="{{ old('age', isset($patient->birthdate) ? \Carbon\Carbon::parse($patient->birthdate)->age : '') }}" />
                        </div>

                        <!-- Mobile Phone -->
                        <div>
                            <x-input-label for="mobile_phone" :value="__('messages.mobile_phone')" />
                            <x-text-input id="mobile_phone"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="text" name="mobile_phone" :value="$patient->mobile_phone" required />
                            <x-input-error :messages="$errors->get('mobile_phone')" class="mt-1" />
                        </div>

                        <!-- Email ID -->
                        <div>
                            <x-input-label for="email_id" :value="__('Email ID')" />
                            <x-text-input id="email_id"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="email" name="email_id" :value="$patient->email_id" />
                            <x-input-error :messages="$errors->get('email_id')" class="mt-1" />
                        </div>

                        <!-- Gender -->
                        <div>
                            <x-input-label for="gender" :value="__('Gender')" />
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center space-x-1">
                                    <input type="radio" id="male" name="gender" value="M"
                                        class="border-gray-400 focus:ring-0 focus:border-gray-500"
                                        {{ $patient->gender == 'M' ? 'checked' : '' }}>
                                    <span>Male</span>
                                </label>
                                <label class="flex items-center space-x-1">
                                    <input type="radio" id="female" name="gender" value="F"
                                        class="border-gray-400 focus:ring-0 focus:border-gray-500"
                                        {{ $patient->gender == 'F' ? 'checked' : '' }}>
                                    <span>Female</span>
                                </label>
                                <label class="flex items-center space-x-1">
                                    <input type="radio" id="other" name="gender" value="O"
                                        class="border-gray-400 focus:ring-0 focus:border-gray-500"
                                        {{ $patient->gender == 'O' ? 'checked' : '' }}>
                                    <span>Other</span>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('gender')" class="mt-1" />
                        </div>

                        <!-- Address -->
                        <div>
                            <x-input-label for="address" :value="__('messages.address')" />
                            <x-text-input id="address"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="text" name="address" :value="$patient->address" required />
                            <x-input-error :messages="$errors->get('address')" class="mt-1" />
                        </div>
                        <!-- Occupation -->
                        <div>
                            <x-input-label for="job" :value="__('messages.occupation')" />
                            <x-text-input id="occupation"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="text" name="occupation" :value="$patient->occupation" />
                            <x-input-error :messages="$errors->get('occupation')" class="mt-1" />
                        </div>
                        <!-- Reference -->
                        <div>
                            <x-input-label for="reference" :value="__('messages.reference')" />
                            <x-text-input id="reference"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="text" name="reference" :value="$patient->reference" />
                            <x-input-error :messages="$errors->get('reference')" class="mt-1" />
                        </div>

                        <!-- Height Field -->
                        <div>
                            <x-input-label for="height" :value="__('messages.Height')" />
                            <x-text-input id="height"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2 reverse-transliteration"
                                type="text" step="0.01" name="height" value="{{ old('height', $patient->height) }}" />
                            <x-input-error :messages="$errors->get('height')" class="mt-1" />
                        </div>

                        <!-- Weight Field -->
                        <div>
                            <x-input-label for="weight" :value="__('messages.Weight')" />
                            <x-text-input id="weight"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2 reverse-transliteration"
                                type="text" step="0.01" name="weight" value="{{ old('weight', $patient->weight) }}" />
                            <x-input-error :messages="$errors->get('weight')" class="mt-1" />
                        </div>



                        <!-- Occupation -->
                        {{-- <div>
                            <x-input-label for="occupation" :value="__('messages.occupation')" />
                            <x-text-input id="occupation"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="text" name="occupation" :value="$patient->occupation" />
                            <x-input-error :messages="$errors->get('occupation')" class="mt-1" />
                        </div> --}}

                        <!-- Remark -->
                        {{-- <div>
                            <x-input-label for="remark" :value="__('messages.remark')" />
                            <x-text-input id="remark"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="text" name="remark" :value="$patient->remark" />
                            <x-input-error :messages="$errors->get('remark')" class="mt-1" />
                        </div> --}}

                        <!-- Balance -->
                        {{-- <div>
                            <x-input-label for="balance" :value="__('Balance')" />
                            <x-text-input id="balance"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="number" name="balance" :value="$patient->balance" />
                            <x-input-error :messages="$errors->get('balance')" class="mt-1" />
                        </div> --}}


                        {{--
                        <input type="text" name="check_up_info[user_name]"
                            value="{{ $checkUpInfo['user_name'] ?? '' }}" class="form-input">
                        <input type="text" name="check_up_info[branch_name]"
                            value="{{ $checkUpInfo['branch_name'] ?? '' }}" class="form-input">
                        --}}

                    </div>

                    <!-- Vishesh Field -->
                    <div>
                        <x-input-label for="vishesh" :value="__('messages.Vishesh')" />
                        {{--
                            <x-text-input id="vishesh"
                                class="w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2"
                                type="text" name="vishesh" :value="$patient->vishesh" />
                            --}}
                        <textarea id="vishesh" name="vishesh"
                            class="tinymce-editor w-full rounded-lg border-2 border-gray-400 focus:ring-0 focus:border-gray-500 p-1.5 px-2">{{ $patient->vishesh }}</textarea>
                        <x-input-error :messages="$errors->get('vishesh')" class="mt-1" />
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-4 pt-4 border-t border-gray-200 mt-4">
                        <a href="{{ route('patients.index') }}">
                            <x-secondary-button class="text-gray-700 hover:bg-gray-100">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                        </a>
                        <x-primary-button class="bg-blue-600 hover:bg-blue-700 text-white">
                            {{ __('Update Patient') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function calculateAgeFromBirthdate() {
        let birthdate = document.getElementById('birthdate').value;
        if (birthdate) {
            let birthYear = new Date(birthdate).getFullYear();
            let currentYear = new Date().getFullYear();
            let age = currentYear - birthYear;
            document.getElementById('age').value = age; // Auto-fill age
        }
    }

    function calculateBirthdateFromAge() {
        let age = document.getElementById('age').value;
        if (age) {
            let currentYear = new Date().getFullYear();
            let birthYear = currentYear - age;
            let birthdate = `${birthYear}-01-01`; // Default to Jan 1st
            document.getElementById('birthdate').value = birthdate;
        }
    }

    // Run this when the page loads to ensure proper values are set
    document.addEventListener('DOMContentLoaded', function() {
        calculateAgeFromBirthdate();

        // Calculate birthdate from age after Marathi conversion
        document.getElementById('age').addEventListener('marathiConverted', calculateBirthdateFromAge);

        // Camera functionality
        let cameraStream = null;
        let capturedFile = null;

        const cameraModal = document.getElementById("cameraModal");
        const openCameraModal = document.getElementById("openCameraModal");
        const closeCameraModal = document.getElementById("closeCameraModal");
        const captureBtn = document.getElementById("captureBtn");
        const video = document.getElementById("cameraPreview");
        const cameraSelect = document.getElementById("cameraSelect");
        const photoFileInput = document.getElementById("photoFileInput");
        const photoTypesInput = document.getElementById("photoTypesInput");
        const retakeBtn = document.getElementById("retakeBtn");
        const doneBtn = document.getElementById("doneBtn");
        const capturedPhotoContainer = document.getElementById("capturedPhotoContainer");
        const capturedPhotoPreview = document.getElementById("capturedPhotoPreview");
        const noPhotoState = document.getElementById("noPhotoState");

        // Safe initialization
        if (photoFileInput && photoTypesInput) {
            if (!photoFileInput.files || photoFileInput.files.length === 0) {
                const dataTransfer = new DataTransfer();
                photoFileInput.files = dataTransfer.files;
            }
            if (!photoTypesInput.value) {
                photoTypesInput.value = "[]";
            }
        } else {
            console.error("Photo inputs not found");
        }

        // Photo preview functionality
        const newPhotoPreviewContainer = document.getElementById('newPhotoPreviewContainer');
        const newPhotoPreview = document.getElementById('newPhotoPreview');
        const previewStatus = document.getElementById('previewStatus');
        const previewDetails = document.getElementById('previewDetails');
        const photoFileInputField = document.getElementById('photo_file');

        // Function to update preview
        function updatePhotoPreview(file) {
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    newPhotoPreview.src = e.target.result;
                    newPhotoPreviewContainer.classList.remove('hidden');
                    previewDetails.innerHTML = `<strong>File:</strong> ${file.name}<br><strong>Size:</strong> ${(file.size / 1024).toFixed(1)} KB`;
                };
                reader.readAsDataURL(file);
            } else {
                // Hide the preview container when no new file is selected
                newPhotoPreviewContainer.classList.add('hidden');
            }
        }

        // Function to update preview from captured file
        function updatePreviewFromCaptured() {
            if (capturedFile) {
                updatePhotoPreview(capturedFile);
            } else {
                updatePhotoPreview(null);
            }
        }

        // Add event listener for file input changes
        if (photoFileInputField) {
            photoFileInputField.addEventListener('change', function(e) {
                const file = e.target.files[0];
                updatePhotoPreview(file);
            });
        }

        openCameraModal.addEventListener("click", async (e) => {
            e.preventDefault();
            cameraModal.classList.remove("hidden");
            await loadCameras();
            // Reset state
            capturedFile = null;
            capturedPhotoContainer.classList.add("hidden");
            noPhotoState.classList.remove("hidden");
            retakeBtn.disabled = true;
            doneBtn.disabled = true;
        });

        closeCameraModal.addEventListener("click", (e) => {
            e.preventDefault();
            updateFileInput();
            cameraModal.classList.add("hidden");
            stopCamera();
        });

        async function loadCameras() {
            try {
                cameraSelect.innerHTML = '<option>Loading cameras...</option>';
                const devices = await navigator.mediaDevices.enumerateDevices();
                const videoDevices = devices.filter(device => device.kind === "videoinput");
                if (videoDevices.length === 0) {
                    cameraSelect.innerHTML = '<option>No cameras found</option>';
                    alert("No cameras found.");
                    return;
                }
                cameraSelect.innerHTML = '<option value="">Select a camera</option>';
                videoDevices.forEach((device, index) => {
                    const option = document.createElement("option");
                    option.value = device.deviceId;
                    option.text = device.label || `Camera ${index + 1}`;
                    cameraSelect.appendChild(option);
                });
                // Auto-select first camera
                if (videoDevices.length > 0) {
                    cameraSelect.value = videoDevices[0].deviceId;
                    await startCamera(videoDevices[0].deviceId);
                }
            } catch (error) {
                console.error("Error loading cameras:", error);
                cameraSelect.innerHTML = '<option>Failed to load cameras</option>';
                alert("Failed to access camera. Please allow permissions.");
            }
        }

        async function startCamera(deviceId) {
            stopCamera();
            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        deviceId: deviceId ? { exact: deviceId } : undefined
                    }
                });
                video.srcObject = cameraStream;
                video.play();
            } catch (error) {
                console.error("Error starting camera:", error);
                alert("Camera access denied or unavailable.");
            }
        }

        function stopCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
        }

        cameraSelect.addEventListener("change", () => {
            startCamera(cameraSelect.value);
        });

        captureBtn.addEventListener("click", (e) => {
            e.preventDefault();
            const canvas = document.createElement("canvas");
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext("2d").drawImage(video, 0, 0, canvas.width, canvas.height);

            canvas.toBlob((blob) => {
                capturedFile = new File([blob], `patient_photo_${Date.now()}.png`, { type: "image/png" });

                // Update preview
                capturedPhotoPreview.src = URL.createObjectURL(capturedFile);
                capturedPhotoContainer.classList.remove("hidden");
                noPhotoState.classList.add("hidden");

                // Enable buttons
                retakeBtn.disabled = false;
                doneBtn.disabled = false;
            }, "image/png");
        });

        // Retake button
        retakeBtn.addEventListener("click", () => {
            capturedFile = null;
            capturedPhotoContainer.classList.add("hidden");
            noPhotoState.classList.remove("hidden");
            retakeBtn.disabled = true;
            doneBtn.disabled = true;
        });

        // Done button
        doneBtn.addEventListener("click", () => {
            updateFileInput();
            cameraModal.classList.add("hidden");
            stopCamera();
        });

        function updateFileInput() {
            const dataTransfer = new DataTransfer();
            const types = [];

            if (capturedFile) {
                dataTransfer.items.add(capturedFile);
                types.push("patient_photo");
            }

            photoFileInput.files = dataTransfer.files;
            photoTypesInput.value = JSON.stringify(types);

            // Update main preview
            if (capturedFile) {
                updatePreviewFromCaptured();
            } else if (!photoFileInputField.files[0]) {
                updatePhotoPreview(null);
            }
        }

        // Add form submit handler to ensure captured files are included
        const editForm = document.querySelector('form[action*="/patients/"]');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                // Ensure captured camera files are added to the form before submission
                updateFileInput();
            });
        }
    });
</script>
