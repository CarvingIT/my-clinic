<!-- Nadi Modal -->
<div id="nadiModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-md shadow-lg w-full max-w-2xl">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">नाडी प्रीसेट व्यवस्थापन</h2>
        <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">नवीन नाडी जोडा</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300">बटण टेक्स्ट</label>
                    <input type="text" id="nadiButtonText" placeholder="उदा. वेगवती"
                        class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:text-white" />
                </div>
                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300">प्रीसेट टेक्स्ट</label>
                    <input type="text" id="nadiPresetText" placeholder="उदा. वेगवती"
                        class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:text-white" />
                </div>
            </div>
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" onclick="clearNadiForm()"
                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 rounded">Clear</button>
                <button type="button" onclick="saveNadiPreset()"
                    class="px-4 py-2 bg-blue-500 text-white hover:bg-blue-600 rounded">Save</button>
            </div>
        </div>
        <div class="max-h-96 overflow-y-auto">
            <table class="w-full text-left text-sm text-gray-700 dark:text-gray-300">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-700">
                        <th class="p-2">बटण टेक्स्ट</th>
                        <th class="p-2">प्रीसेट टेक्स्ट</th>
                        <th class="p-2">स्रोत</th>
                        <th class="p-2">कृती</th>
                    </tr>
                </thead>
                <tbody id="nadiPresetList"></tbody>
            </table>
        </div>
        <div class="mt-4 flex justify-end">
            <button type="button" onclick="closeNadiModal()"
                class="px-4 py-2 bg-red-500 text-white hover:bg-red-600 rounded">Close</button>
        </div>
    </div>
</div>

<!-- Lakshane Modal -->
<div id="lakshaneModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-md shadow-lg w-full max-w-2xl">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">लक्षणे प्रीसेट व्यवस्थापन</h2>
        <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">नवीन लक्षणे जोडा</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300">बटण टेक्स्ट</label>
                    <input type="text" id="lakshaneButtonText" placeholder="उदा. अजीर्ण"
                        class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:text-white" />
                </div>
                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300">प्रीसेट टेक्स्ट</label>
                    <input type="text" id="lakshanePresetText" placeholder="उदा. अजीर्ण - "
                        class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:text-white" />
                </div>
            </div>
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" onclick="clearLakshaneForm()"
                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 rounded">Clear</button>
                <button type="button" onclick="saveLakshanePreset()"
                    class="px-4 py-2 bg-blue-500 text-white hover:bg-blue-600 rounded">Save</button>
            </div>
        </div>
        <div class="max-h-96 overflow-y-auto">
            <table class="w-full text-left text-sm text-gray-700 dark:text-gray-300">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-700">
                        <th class="p-2">बटण टेक्स्ट</th>
                        <th class="p-2">प्रीसेट टेक्स्ट</th>
                        <th class="p-2">स्रोत</th>
                        <th class="p-2">कृती</th>
                    </tr>
                </thead>
                <tbody id="lakshanePresetList"></tbody>
            </table>
        </div>
        <div class="mt-4 flex justify-end">
            <button type="button" onclick="closeLakshaneModal()"
                class="px-4 py-2 bg-red-500 text-white hover:bg-red-600 rounded">Close</button>
        </div>
    </div>
</div>

<!-- Chikitsa Modal -->
<div id="chikitsaModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-md shadow-lg w-full max-w-2xl">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">चिकित्सा प्रीसेट व्यवस्थापन</h2>
        <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">नवीन चिकित्सा जोडा</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300">बटण टेक्स्ट</label>
                    <input type="text" id="chikitsaButtonText" placeholder="उदा. ज्वर"
                        class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:text-white" />
                </div>
                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300">प्रीसेट टेक्स्ट</label>
                    <textarea id="chikitsaPresetText" rows="2" placeholder="उदा. महासुदर्शन, वैदेही..."
                        class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:text-white"></textarea>
                </div>
            </div>
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" onclick="clearChikitsaForm()"
                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 rounded">Clear</button>
                <button type="button" onclick="saveChikitsaPreset()"
                    class="px-4 py-2 bg-blue-500 text-white hover:bg-blue-600 rounded">Save</button>
            </div>
        </div>
        <div class="max-h-96 overflow-y-auto">
            <table class="w-full text-left text-sm text-gray-700 dark:text-gray-300">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-700">
                        <th class="p-2">बटण टेक्स्ट</th>
                        <th class="p-2">प्रीसेट टेक्स्ट</th>
                        <th class="p-2">स्रोत</th>
                        <th class="p-2">कृती</th>
                    </tr>
                </thead>
                <tbody id="chikitsaPresetList"></tbody>
            </table>
        </div>
        <div class="mt-4 flex justify-end">
            <button type="button" onclick="closeChikitsaModal()"
                class="px-4 py-2 bg-red-500 text-white hover:bg-red-600 rounded">Close</button>
        </div>
    </div>
</div>

<!-- Vishesh Modal -->
<div id="visheshModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-md shadow-lg w-full max-w-2xl">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">विशेष प्रीसेट व्यवस्थापन</h2>
        <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">नवीन विशेष जोडा</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300">बटण टेक्स्ट</label>
                    <input type="text" id="visheshButtonText" placeholder="उदा. रक्तदाब"
                        class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:text-white" />
                </div>
                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300">प्रीसेट टेक्स्ट</label>
                    <input type="text" id="visheshPresetText" placeholder="उदा. रक्तदाब - "
                        class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:text-white" />
                </div>
            </div>
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" onclick="clearVisheshForm()"
                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 rounded">Clear</button>
                <button type="button" onclick="saveVisheshPreset()"
                    class="px-4 py-2 bg-blue-500 text-white hover:bg-blue-600 rounded">Save</button>
            </div>
        </div>
        <div class="max-h-96 overflow-y-auto">
            <table class="w-full text-left text-sm text-gray-700 dark:text-gray-300">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-700">
                        <th class="p-2">बटण टेक्स्ट</th>
                        <th class="p-2">प्रीसेट टेक्स्ट</th>
                        <th class="p-2">स्रोत</th>
                        <th class="p-2">कृती</th>
                    </tr>
                </thead>
                <tbody id="visheshPresetList"></tbody>
            </table>
        </div>
        <div class="mt-4 flex justify-end">
            <button type="button" onclick="closeVisheshModal()"
                class="px-4 py-2 bg-red-500 text-white hover:bg-red-600 rounded">Close</button>
        </div>
    </div>
</div>

<!-- Report Modal -->
<div id="reportModal"
    class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-md shadow-lg w-full max-w-2xl">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Add New Report</h2>
        <div class="mb-4">
            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Report Text</label>
            <textarea id="reportText" rows="4" placeholder="Enter your report here..."
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>
        <div class="flex justify-end space-x-2">
            <button type="button" onclick="closeReportModal()"
                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 rounded">Cancel</button>
            <button type="button" onclick="addReport()"
                class="px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700 rounded">Add Report</button>
        </div>
    </div>
</div>
