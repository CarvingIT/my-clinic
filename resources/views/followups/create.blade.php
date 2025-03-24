<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight px-5">
            {{ __('messages.Add Follow Up') }} - {{ $patient->name }}

            <span class="text-gray-600 text-sm">
                @if ($patient->birthdate || $patient->gender)
                    {{-- | {{ __('messages.Age') }}/{{ __('messages.Gender') }}: --}}
                    {{ $patient->birthdate?->age ?? __('') }}/{{ $patient->gender ?? __('') }}
                @endif
                @if ($patient->vishesh)
                    | {{ __('messages.Vishesh') }}: {{ $patient->vishesh }}
                @endif
                @if ($patient->height)
                    | {{ __('messages.Height') }}: {{ $patient->height }} cm
                @endif
                @if ($patient->weight)
                    | {{ __('messages.Weight') }}: {{ $patient->weight }} kg
                @endif
                @if ($patient->height && $patient->weight)
                    @php
                        $heightInMeters = $patient->height / 100;
                        $bmi = $patient->weight / ($heightInMeters * $heightInMeters);
                        $bmiCategory = match (true) {
                            $bmi < 18.5 => 'Underweight',
                            $bmi >= 18.5 && $bmi < 25 => 'Healthy Weight',
                            $bmi >= 25 && $bmi < 30 => 'Overweight',
                            default => 'Obese',
                        };
                    @endphp
                    | {{ __('BMI') }}: {{ number_format($bmi, 2) }}
                @endif
            </span>
        </h2>
    </x-slot>

    <div class="py-12 px-4 sm:px-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Follow-up Creation Form (Left Column) -->
                        <div class="lg:col-span-2">
                            <form method="POST" action="{{ route('followups.store') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="patient_id" value="{{ $patient->id }}" />

                                <!-- Naadi Textarea -->
                                <div class="mb-6">
                                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                        {{ __('नाडी') }}
                                    </h2>
                                    <textarea id="nadiInput" name="nadi" rows="4"
                                        class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"></textarea>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-4 mt-4">
                                        @foreach (['वात', 'पित्त', 'कफ', 'सूक्ष्म', 'कठीण', 'साम', 'वेग', 'प्राण', 'व्यान', 'स्थूल', 'अल्प स्थूल', 'अनियमित', 'तीक्ष्ण', 'वेगवती'] as $nadi)
                                            <button type="button"
                                                class="nadi-box bg-gray-200 dark:bg-gray-700 p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                                                onclick="appendNadi('{{ $nadi }}')">{{ $nadi }}</button>
                                        @endforeach
                                    </div>
                                    <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />
                                </div>


                                <!-- Diagnosis Textarea -->
                                <div class="mt-4 mb-4">
                                    <div class="flex items-center space-x-2">
                                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-1">
                                            {{ __('लक्षणे') }}
                                        </h2>
                                    </div>

                                    <textarea id="lakshane" name="diagnosis" rows="4"
                                        class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"></textarea>
                                    <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />

                                    <!-- Preset and Arrow Buttons Below Textarea -->
                                    <div class="mt-4 grid grid-cols-7 gap-2">
                                        <button type="button" onclick="insertArrow('↑')"
                                            class="w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            ↑
                                        </button>
                                        <button type="button" onclick="insertArrow('↓')"
                                            class="w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            ↓
                                        </button>
                                        <button type="button" onclick="insertText('मल- ')"
                                            class="w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            मल
                                        </button>
                                        <button type="button" onclick="insertText('मूत्र - ')"
                                            class="w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            मूत्र
                                        </button>
                                        <button type="button" onclick="insertText('जिव्हा - ')"
                                            class="w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            जिव्हा
                                        </button>
                                        <button type="button" onclick="insertText('निद्रा - ')"
                                            class="w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            निद्रा
                                        </button>
                                        <button type="button" onclick="insertText('क्षुधा - ')"
                                            class="w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            क्षुधा
                                        </button>
                                    </div>
                                </div>


                                @php
                                    // Fetch the latest follow-up's 'chikitsa' if available
$latestFollowUp = $followUps->first();
$previousChikitsa = $latestFollowUp
    ? json_decode($latestFollowUp->check_up_info, true)['chikitsa'] ?? ''
    : '';
                                @endphp
                                <!-- Chikitsa Textarea -->
                                <div class="mt-6 mb-4 flex flex-col">
                                    <div class="flex-1">
                                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                            {{ __('चिकित्सा') }}</h2>
                                        <textarea id="chikitsa" name="chikitsa" rows="4"
                                            class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"></textarea>
                                        <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />

                                        <div class="mt-4 grid grid-cols-5 gap-4">
                                            <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                                                data-preset="महासुदर्शन, वैदेही, बिभितक, यष्टी, तालीसादी ">
                                                ज्वर
                                            </div>
                                            <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                                                data-preset="वरा, गुग्गुळ, विश्व, अश्वकपी, वत्स, गोक्षुर, गोदंती">
                                                संधिशूल
                                            </div>
                                            <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                                                data-preset="हरीतकी, अमृता, सारिवा ">
                                                अर्श
                                            </div>
                                            <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                                                data-preset="कुटज, मुस्ता, विश्व ">
                                                ग्रहणी
                                            </div>
                                            <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                                                data-preset="{{ $previousChikitsa }}">
                                                चिकित्सा यथा पूर्व
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Webcam Capture Section -->
                                    <div class="mb-6">
                                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                            {{ __('Patient Photo') }}
                                        </h2>

                                        <!-- Standard File Input (Hidden) -->
                                        <input type="file" id="patient_photo" name="patient_photo" accept="image/*" class="hidden" />

                                        <!-- Buttons -->
                                        <div class="flex space-x-4">
                                            <!-- Choose File Button (Triggers File Input) -->
                                            <label for="patient_photo"
                                                   class="cursor-pointer bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-block">
                                                {{ __('Choose File') }}
                                            </label>

                                            <!-- Capture Photo Button (Opens Webcam Modal) -->
                                            <button type="button" id="capturePhotoButton"
                                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                {{ __('Capture Photo') }}
                                            </button>
                                        </div>


                                        <!-- Webcam Modal -->
                                        <div id="webcamModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
                                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md">
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Capture Photo') }}</h3>

                                                <!-- Camera Selection -->
                                                 <div class="mb-4">
                                                    <label for="cameraSelect" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Select Camera') }}</label>
                                                    <select id="cameraSelect" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                        <!-- Options will be populated by JavaScript -->
                                                    </select>
                                                </div>


                                                <video id="webcamPreview" class="w-full" autoplay playsinline></video>
                                                <canvas id="capturedCanvas" class="hidden"></canvas> <!-- Hidden canvas for image capture -->

                                                <div class="mt-4 flex justify-between">
                                                    <button type="button" id="captureButton"
                                                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                                        {{ __('Capture') }}
                                                    </button>
                                                    <button type="button" id="retakeButton"
                                                            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded hidden">
                                                        {{ __('Retake') }}
                                                    </button>
                                                    <button type="button" id="closeModalButton"
                                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                                        {{ __('Close') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Preview Image -->
                                        <img id="imagePreview" src="#" alt="Image Preview" class="mt-2 max-w-xs hidden">

                                    </div>

                                    <!-- Numeric Input Boxes -->
                                    <div class="flex items-center space-x-4 mt-6">
                                        <div class="flex flex-col">
                                            <h2 class="text-l font-semibold text-gray-800 dark:text-white mb-2">
                                                {{ __('दिवस') }} </h2>
                                            <input type="number" name="days" id="days" placeholder=""
                                                class="px-2 py-1 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400 w-21" />
                                        </div>
                                        <div class="flex flex-col">
                                            <h2 class="text-l font-semibold text-gray-800 dark:text-white mb-2">
                                                {{ __('पुड्या') }} </h2>
                                            <input type="number" name="packets" id="packets" placeholder=""
                                                class="px-2 py-1 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400 w-21" />
                                        </div>
                                    </div>
                                </div>



                                <div class="mt-4">
                                    <label for="payment_method"
                                        class="text-l font-semibold text-gray-700 dark:text-white mb-4">{{ __('messages.Payment Method') }}
                                    </label>
                                    <select id="payment_method" name="payment_method"
                                        class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400 text-sm">
                                        <option value="">Please Select</option>
                                        <option value="cash">Cash</option>
                                        <option value="card">Card</option>
                                        <option value="online">Online</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                                </div>

                                <div class="mt-4">
                                    <label for="all_dues"
                                        class="text-l font-semibold text-gray-700 dark:text-white mb-4">
                                        {{ __('messages.All Dues') }}
                                    </label>
                                    <x-text-input id="all_dues"
                                        class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm"
                                        type="number" name="all_dues"
                                        value="{{ old('all_dues', $totalDueAll ?? 0) }}" readonly />
                                </div>

                                <div class="mt-4">
                                    <label for="amount_billed"
                                        class="text-l font-semibold text-gray-700 dark:text-white mb-4">
                                        {{ __('messages.Amount Billed') }}
                                    </label>
                                    <x-text-input id="amount_billed"
                                        class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm"
                                        type="number" name="amount_billed" required />
                                </div>

                                <div class="mt-4">
                                    <label for="amount_paid"
                                        class="text-l font-semibold text-gray-700 dark:text-white mb-4">
                                        {{ __('messages.Amount Paid') }}
                                    </label>
                                    <x-text-input id="amount_paid"
                                        class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm"
                                        type="number" name="amount_paid" required />
                                </div>

                                <div class="mt-4">
                                    <label for="total_due"
                                        class="text-l font-semibold text-gray-700 dark:text-white mb-4">
                                        {{ __('messages.Total Due') }}
                                    </label>
                                    <x-text-input id="total_due"
                                        class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm"
                                        type="number" name="total_due" readonly />
                                </div>

                                <script>
                                    function calculateTotalDue() {
                                        let allDues = parseFloat(document.getElementById('all_dues').value) || 0;
                                        let amountBilled = parseFloat(document.getElementById('amount_billed').value) || 0;
                                        let amountPaid = parseFloat(document.getElementById('amount_paid').value) || 0;

                                        let totalDue = allDues + amountBilled - amountPaid;
                                        // totalDue = totalDue > 0 ? totalDue : 0; // Prevent negative values

                                        document.getElementById('total_due').value = totalDue.toFixed(2); // Ensure 2 decimal places
                                    }

                                    // Ensure script runs after page load
                                    window.onload = function() {
                                        calculateTotalDue();

                                        document.getElementById('amount_billed').addEventListener('input', calculateTotalDue);
                                        document.getElementById('amount_paid').addEventListener('input', calculateTotalDue);
                                    };
                                </script>
                                <!-- Submit Button -->
                                <div class="flex items-center justify-end mt-4">
                                    <x-primary-button class="ms-4">
                                        {{ __('Add Follow Up') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>

                        <!-- Previous Follow-ups (Right Column) -->
                        <div
                            class="fixed mb-3 right-40 top-62 max-h-[calc(100vh-80px)] w-[375px] overflow-y-auto p-4 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 scrollbar-thin">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
                                {{ __('Previous Follow-ups') }}
                            </h3>

                            @if ($followUps->count() > 0)
                                <div class="space-y-4">
                                    @foreach ($followUps as $followUp)
                                        <div
                                            class="bg-gray-100 dark:bg-gray-700 p-4 rounded-md shadow-sm border border-gray-300 dark:border-gray-600">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $followUp->created_at->format('d M Y, h:i A') }}
                                            </p>
                                            @php $checkUpInfo = json_decode($followUp->check_up_info, true); @endphp
                                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                                <strong>{{ __('नाडी') }}:</strong>
                                                {{ $checkUpInfo['nadi'] ?? '-' }}<br>
                                                <strong>{{ __('लक्षणे') }}:</strong>
                                                {{ $followUp->diagnosis ?? '-' }}<br>
                                                <strong>{{ __('चिकित्सा') }}:</strong>
                                                {{ $checkUpInfo['chikitsa'] ?? '-' }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-600 dark:text-gray-400">
                                    {{ __('No previous follow-ups.') }}
                                </p>
                            @endif
                        </div>



                    </div>
                </div>
            </div>
        </div>
</x-app-layout>
<script>
    function appendNadi(nadi) {
        const input = document.getElementById('nadiInput');
        input.value += (input.value ? ', ' : '') + nadi;
        input.focus();
    }
</script>

<script>
    const textarea = document.getElementById('chikitsa');
    const presetBoxes = document.querySelectorAll('.preset-box');

    presetBoxes.forEach(box => {
        box.addEventListener('click', () => {
            const presetText = box.dataset.preset;
            textarea.value += (textarea.value ? ', ' : '') + presetText;
        });
    });
</script>

<script>
    function insertArrow(arrow) {
        let textarea = document.getElementById("lakshane");
        let start = textarea.selectionStart;
        let end = textarea.selectionEnd;

        // Insert the arrow at the cursor position
        let text = textarea.value;
        textarea.value = text.substring(0, start) + arrow + text.substring(end);

        // Move cursor after inserted arrow
        textarea.selectionStart = textarea.selectionEnd = start + arrow.length;

        // Focus back on textarea
        textarea.focus();
    }
</script>
<script>
    function insertText(text) {
        let textarea = document.getElementById("lakshane");
        textarea.value += text + " ";
    }
</script>

{{-- webcam --}}
<script>
    const patientPhotoInput = document.getElementById('patient_photo');
    const imagePreview = document.getElementById('imagePreview');
    const capturePhotoButton = document.getElementById('capturePhotoButton'); // New button
    const webcamModal = document.getElementById('webcamModal');
    const cameraSelect = document.getElementById('cameraSelect');
    const webcamPreview = document.getElementById('webcamPreview');
    const capturedCanvas = document.getElementById('capturedCanvas');
    const captureButton = document.getElementById('captureButton');
    const retakeButton = document.getElementById('retakeButton');
    const closeModalButton = document.getElementById('closeModalButton');

    let currentStream = null;

    // --- Function to open the webcam modal and get the camera list ---
    async function openWebcam() {
        webcamModal.classList.remove('hidden');
        webcamModal.classList.add('flex');

        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            const videoDevices = devices.filter(device => device.kind === 'videoinput');

            cameraSelect.innerHTML = '';
            videoDevices.forEach(device => {
                const option = document.createElement('option');
                option.value = device.deviceId;
                option.text = device.label || `Camera ${cameraSelect.options.length + 1}`;
                cameraSelect.appendChild(option);
            });

            if (videoDevices.length > 0) {
                startCamera(videoDevices[0].deviceId);
            }

        } catch (error) {
            console.error('Error accessing media devices:', error);
            alert('Error accessing camera. Please ensure camera permissions are granted.');
        }
    }

       //  Function to start the camera stream
    async function startCamera(deviceId) {
        // Stop any existing stream
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
        }

        const constraints = {
            video: {
                deviceId: { exact: deviceId },
                width: { ideal: 640 }, // Adjust
                height: { ideal: 480 }  // Adjust
            }
        };

        try {
            currentStream = await navigator.mediaDevices.getUserMedia(constraints);
            webcamPreview.srcObject = currentStream;
             captureButton.classList.remove('hidden'); // Ensure capture button is visible
            retakeButton.classList.add('hidden');     // Hide retake button
        } catch (error) {
            console.error('Error starting camera:', error);
            alert('Error starting camera. Please check your camera connection and permissions.');
        }
    }

    //  Event Listeners

    // Open Webcam Modal when "Capture Photo" button is clicked
    capturePhotoButton.addEventListener('click', openWebcam);


    cameraSelect.addEventListener('change', (event) => {
        startCamera(event.target.value);
    });

    captureButton.addEventListener('click', () => {
        capturedCanvas.width = webcamPreview.videoWidth;
        capturedCanvas.height = webcamPreview.videoHeight;
        capturedCanvas.getContext('2d').drawImage(webcamPreview, 0, 0);

        capturedCanvas.toBlob((blob) => {
            const file = new File([blob], "captured_image.jpg", { type: 'image/jpeg' });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            patientPhotoInput.files = dataTransfer.files;
            // Show/hide buttons
            captureButton.classList.add('hidden');
            retakeButton.classList.remove('hidden');
            patientPhotoInput.dispatchEvent(new Event('change')); // Trigger change for preview
            closeWebcam();//close
        }, 'image/jpeg');
    });

      retakeButton.addEventListener('click', () => {
         startCamera(cameraSelect.value); // Restart the camera
    });

    closeModalButton.addEventListener('click', closeWebcam); //closeWebcam function call

    function closeWebcam() {
        // Stop the camera stream and hide modal
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
        }
        webcamModal.classList.add('hidden');
        webcamModal.classList.remove('flex');
        webcamPreview.srcObject = null; //clear video
    }

    //  Update Preview Image
    patientPhotoInput.addEventListener('change', () => {
        if (patientPhotoInput.files && patientPhotoInput.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.src = e.target.result;
                imagePreview.classList.remove('hidden');
            };
            reader.readAsDataURL(patientPhotoInput.files[0]);
        } else {
            imagePreview.classList.add('hidden'); //hide preview
        }
    });

</script>
