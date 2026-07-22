{{-- Script for nadi presets --}}
<script>
    const nadiFieldId = {{ \App\Models\Field::where('name', 'nadi')->first()->id ?? 0 }};
    const nadiStorageKey = 'customNadiPresets';

    function getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!token) {
            console.error(
                'CSRF token not found. Add <meta name="csrf-token" content="{{ csrf_token() }}"> to layout.');
            alert('CSRF token not found. Please check your Blade layout.');
        }
        return token || '';
    }

    async function loadNadiPresets() {
        const container = document.getElementById('nadiPresets');
        if (!container) {
            console.error('nadiPresets container not found in DOM.');
            return;
        }
        container.innerHTML = '';

        if (!nadiFieldId) {
            alert('Nadi field ID is invalid (0). Check database seeding for "nadi" in fields table.');
            return;
        }

        try {
            const response = await axios.get(`/presets?field_id=${nadiFieldId}`, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            response.data.forEach(preset => {
                createPresetButton(preset.button_text, preset.preset_text, preset.id, true);
            });
        } catch (error) {
            console.error('Error loading nadi presets:', error.response || error);
            alert(
                `Failed to load nadi presets: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`
            );
        }

        const localPresets = JSON.parse(localStorage.getItem(nadiStorageKey)) || [];
        localPresets.forEach(preset => {
            createPresetButton(preset, preset, null, false);
        });
    }

    function createPresetButton(buttonText, presetText, id, isDatabase) {
        const presetDiv = document.createElement('div');
        presetDiv.className = 'relative';

        const button = document.createElement('button');
        button.type = 'button';
        button.className =
            'nadi-box bg-gray-200 dark:bg-gray-700 p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-500 transition w-full text-centre pr-6';
        button.innerText = buttonText;
        button.onclick = () => appendNadi(presetText);

        presetDiv.appendChild(button);
        document.getElementById('nadiPresets').appendChild(presetDiv);
    }

    async function loadNadiPresetList() {
        const list = document.getElementById('nadiPresetList');
        if (!list) {
            console.error('nadiPresetList container not found in DOM.');
            return;
        }
        list.innerHTML = '';

        try {
            const response = await axios.get(`/presets?field_id=${nadiFieldId}`, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            response.data.forEach(preset => {
                createPresetRow(preset, true);
            });
        } catch (error) {
            console.error('Error loading nadi preset list:', error.response || error);
            alert(
                `Failed to load nadi preset list: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`
            );
        }

        const localPresets = JSON.parse(localStorage.getItem(nadiStorageKey)) || [];
        localPresets.forEach(preset => {
            createPresetRow({
                button_text: preset,
                preset_text: preset,
                id: null
            }, false);
        });
    }

    function createPresetRow(preset, isDatabase) {
        const row = document.createElement('tr');
        row.className = 'border-b dark:border-gray-600';

        row.innerHTML = `
        <td class="p-2">${preset.button_text}</td>
        <td class="p-2">${preset.preset_text || preset.button_text}</td>
        <td class="p-2">${isDatabase ? 'Database' : 'LocalStorage'}</td>
        <td class="p-2">
            <button type="button" class="text-red-500 hover:text-red-700" onclick="deleteNadiPreset('${preset.id || ''}', '${preset.button_text}', ${isDatabase})">Delete</button>
        </td>
    `;

        document.getElementById('nadiPresetList').appendChild(row);
    }

    function openNadiModal() {
        const modal = document.getElementById('nadiModal');
        if (!modal) {
            console.error('nadiModal not found in DOM.');
            return;
        }
        modal.classList.remove('hidden');
        loadNadiPresetList();
        clearNadiForm();
    }

    function closeNadiModal() {
        document.getElementById('nadiModal').classList.add('hidden');
    }

    function clearNadiForm() {
        const buttonText = document.getElementById('nadiButtonText');
        const presetText = document.getElementById('nadiPresetText');
        if (buttonText && presetText) {
            buttonText.value = '';
            presetText.value = '';
        }
    }

    async function saveNadiPreset() {
        const buttonText = document.getElementById('nadiButtonText').value.trim();
        const presetText = document.getElementById('nadiPresetText').value.trim();

        if (!buttonText) {
            alert('Button text is required.');
            return;
        }

        try {
            await axios.post('/presets', {
                field_id: nadiFieldId,
                button_text: buttonText,
                preset_text: presetText || buttonText,
                display_order: 0
            }, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            loadNadiPresets();
            loadNadiPresetList();
            clearNadiForm();
            closeNadiModal();
        } catch (error) {
            console.error('Error saving nadi preset:', error.response || error);
            alert(
                `Failed to save nadi preset: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`
            );
        }
    }

    async function deleteNadiPreset(id, buttonText, isDatabase) {
        if (confirm(`Are you sure you want to delete "${buttonText}"?`)) {
            try {
                if (isDatabase && id) {
                    await axios.delete(`/presets/${id}`, {
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Accept': 'application/json'
                        },
                        withCredentials: true
                    });
                } else {
                    const stored = JSON.parse(localStorage.getItem(nadiStorageKey)) || [];
                    const updated = stored.filter(item => item !== buttonText);
                    localStorage.setItem(nadiStorageKey, JSON.stringify(updated));
                }
                loadNadiPresets();
                loadNadiPresetList();
            } catch (error) {
                console.error('Error deleting nadi preset:', error.response || error);
                alert(
                    `Failed to delete nadi preset: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`
                );
            }
        }
    }

    function appendNadi(text) {
        const editor = tinymce.get('nadiInput');
        if (!editor) {
            console.error('TinyMCE editor for nadiInput not found.');
            return;
        }

        editor.focus();
        const rng = editor.selection.getRng();
        const container = rng.startContainer;
        const cursorPos = rng.startOffset;
        const nodeText = container.textContent || '';
        const beforeText = nodeText.substring(0, cursorPos);
        const afterText = nodeText.substring(cursorPos);

        const needsSpaceBefore = beforeText.trim().length > 0 && !beforeText.trim().endsWith(' ');
        const needsSpaceAfter = afterText.trim().length > 0 && !afterText.trim().startsWith(' ');

        let insertText = '';
        if (needsSpaceBefore) insertText += ' ';
        insertText += text;
        if (needsSpaceAfter) insertText += ' ';

        editor.selection.setContent(insertText);
        editor.selection.collapse(false);
    }

    document.addEventListener('DOMContentLoaded', loadNadiPresets);

    // Nadi Dots Grid Functionality
    let nadiDots = [[], [], []];

    function toggleDot(element, boxIndex, subIndex) {
        // Ensure arrays are initialized
        if (!nadiDots[boxIndex]) nadiDots[boxIndex] = [];
        nadiDots[boxIndex][subIndex] = !nadiDots[boxIndex][subIndex];

        // Toggle visual dot
        element.innerHTML = nadiDots[boxIndex][subIndex] ? '•' : '';
        element.classList.toggle('text-red-500', nadiDots[boxIndex][subIndex]);
        element.classList.toggle('text-xl', nadiDots[boxIndex][subIndex]);

        // Update hidden input
        document.getElementById('nadiDotsInput').value = JSON.stringify(nadiDots);
    }

    // Initialize dots on page load (for editing existing data)
    document.addEventListener('DOMContentLoaded', function() {
        const dotsInput = document.getElementById('nadiDotsInput');
        if (dotsInput && dotsInput.value) {
            nadiDots = JSON.parse(dotsInput.value);
            // Populate grid visually
            const boxes = document.querySelectorAll('#nadiGrid > div');
            boxes.forEach((box, boxIdx) => {
                const subs = box.querySelectorAll('div');
                subs.forEach((sub, subIdx) => {
                    if (nadiDots[boxIdx] && nadiDots[boxIdx][subIdx]) {
                        sub.innerHTML = '•';
                        sub.classList.add('text-red-500');
                        sub.classList.add('text-xl');
                    }
                });
            });
        }
    });
</script>


{{-- Script for chikitsa --}}

<script>
    const chikitsaFieldId = {{ \App\Models\Field::where('name', 'chikitsa')->first()->id ?? 0 }};
    const chikitsaStorageKey = 'customChikitsaPresets';
    const previousChikitsa = {!! json_encode($previousChikitsa ?? '') !!};

    async function loadChikitsaPresets() {
        const container = document.getElementById('chikitsaPresets');
        if (!container) {
            console.error('chikitsaPresets container not found in DOM.');
            return;
        }
        container.innerHTML = '';

        if (!chikitsaFieldId) {
            alert('Chikitsa field ID is invalid (0). Check database seeding for "chikitsa" in fields table.');
            return;
        }

        // Load database presets
        try {
            const response = await axios.get(`/presets?field_id=${chikitsaFieldId}`, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            response.data.forEach(preset => {
                const presetText = preset.button_text === 'चिकित्सा यथा पूर्व' ? previousChikitsa : preset
                    .preset_text;
                createChikitsaPresetButton(preset.button_text, presetText, preset.id, true);
            });
        } catch (error) {
            console.error('Error loading chikitsa presets:', error.response || error);
            alert(
                `Failed to load chikitsa presets: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`
            );
        }

        // Load local storage presets
        const localPresets = JSON.parse(localStorage.getItem(chikitsaStorageKey)) || [];
        localPresets.forEach(preset => {
            createChikitsaPresetButton(preset.title, preset.value, null, false);
        });
    }

    function createChikitsaPresetButton(buttonText, presetText, id, isDatabase) {
        const presetDiv = document.createElement('div');
        presetDiv.className = 'relative';

        const button = document.createElement('button');
        button.type = 'button';
        button.className =
            'border p-2 rounded cursor-pointer bg-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition w-full text-centre pr-6';
        button.innerText = buttonText;
        button.onclick = () => insertChikitsaText(presetText);

        presetDiv.appendChild(button);
        document.getElementById('chikitsaPresets').appendChild(presetDiv);
    }

    function insertChikitsaText(text) {
        const editor = tinymce.get('chikitsa');
        if (!editor) {
            console.error('TinyMCE editor for chikitsa not found.');
            return;
        }

        editor.focus();
        const rng = editor.selection.getRng();
        const container = rng.startContainer;
        const cursorPos = rng.startOffset;
        const nodeText = container.textContent || '';
        const beforeText = nodeText.substring(0, cursorPos);
        const afterText = nodeText.substring(cursorPos);

        const needsSpaceBefore = beforeText.trim().length > 0 && !beforeText.trim().endsWith(' ');
        const needsSpaceAfter = afterText.trim().length > 0 && !afterText.trim().startsWith(' ');

        let insertText = '';
        if (needsSpaceBefore) insertText += ' ';
        insertText += text.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim().replace(/,/g, '').replace(/\s+/g, ' ').trim();
        if (needsSpaceAfter) insertText += ' ';

        editor.selection.setContent(insertText);
        editor.selection.collapse(false);
    }

    async function loadChikitsaPresetList() {
        const list = document.getElementById('chikitsaPresetList');
        if (!list) {
            console.error('chikitsaPresetList container not found in DOM.');
            return;
        }
        list.innerHTML = '';

        try {
            const response = await axios.get(`/presets?field_id=${chikitsaFieldId}`, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            response.data.forEach(preset => {
                createChikitsaPresetRow(preset, true);
            });
        } catch (error) {
            console.error('Error loading chikitsa preset list:', error.response || error);
            alert(
                `Failed to load chikitsa preset list: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`
            );
        }

        const localPresets = JSON.parse(localStorage.getItem(chikitsaStorageKey)) || [];
        localPresets.forEach(preset => {
            createChikitsaPresetRow({
                button_text: preset.title,
                preset_text: preset.value,
                id: null
            }, false);
        });
    }

    function createChikitsaPresetRow(preset, isDatabase) {
        const row = document.createElement('tr');
        row.className = 'border-b dark:border-gray-600';

        row.innerHTML = `
            <td class="p-2">${preset.button_text}</td>
            <td class="p-2">${preset.preset_text || preset.button_text}</td>
            <td class="p-2">${isDatabase ? 'Database' : 'LocalStorage'}</td>
            <td class="p-2">
                <button type="button" class="text-red-500 hover:text-red-700" onclick="deleteChikitsaPreset('${preset.id || ''}', '${preset.button_text}', ${isDatabase})">Delete</button>
            </td>
        `;

        document.getElementById('chikitsaPresetList').appendChild(row);
    }

    function openChikitsaModal() {
        const modal = document.getElementById('chikitsaModal');
        if (!modal) {
            console.error('chikitsaModal not found in DOM.');
            return;
        }
        modal.classList.remove('hidden');
        loadChikitsaPresetList();
        clearChikitsaForm();
    }

    function closeChikitsaModal() {
        document.getElementById('chikitsaModal').classList.add('hidden');
    }

    function clearChikitsaForm() {
        const buttonText = document.getElementById('chikitsaButtonText');
        const presetText = document.getElementById('chikitsaPresetText');
        if (buttonText && presetText) {
            buttonText.value = '';
            presetText.value = '';
        }
    }

    async function saveChikitsaPreset() {
        const buttonText = document.getElementById('chikitsaButtonText').value.trim();
        const presetText = document.getElementById('chikitsaPresetText').value.trim();

        if (!buttonText) {
            alert('Button text is required.');
            return;
        }

        try {
            await axios.post('/presets', {
                field_id: chikitsaFieldId,
                button_text: buttonText,
                preset_text: presetText || buttonText,
                display_order: 0
            }, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });

            loadChikitsaPresets();
            loadChikitsaPresetList();
            clearChikitsaForm();
            closeChikitsaModal();
        } catch (error) {
            console.error('Error saving chikitsa preset:', error.response || error);
            alert(
                `Failed to save chikitsa preset: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`
            );
        }
    }

    async function deleteChikitsaPreset(id, buttonText, isDatabase) {
        if (confirm(`Are you sure you want to delete "${buttonText}"?`)) {
            try {
                if (id) {
                    await axios.delete(`/presets/${id}`, {
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Accept': 'application/json'
                        },
                        withCredentials: true
                    });
                }
                loadChikitsaPresetList();
            } catch (error) {
                console.error('Error deleting chikitsa preset:', error.response || error);
                alert(
                    `Failed to delete chikitsa preset: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`
                );
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadChikitsaPresets();

        // For default presets
        document.querySelectorAll('.preset-box').forEach(box => {
            const value = box.dataset.preset;
            box.addEventListener('click', () => insertChikitsaText(value));
        });
    });

    // Dravya Modal Logic
    function showDravyaPopup() {
        const popup = document.getElementById('dravyaPopup');
        popup.classList.remove('hidden');
    }

    function hideDravyaPopup() {
        const popup = document.getElementById('dravyaPopup');
        popup.classList.add('hidden');
    }

    function insertDravya(dravya) {
        const editor = tinymce.get('chikitsa');
        if (!editor) return;

        editor.focus();

        const rng = editor.selection.getRng();
        const startContainer = rng.startContainer;
        const startOffset = rng.startOffset;

        let precedingChar = '';
        if (startContainer.nodeType === 3 && startOffset > 0) {
            precedingChar = startContainer.data.substring(startOffset - 1, startOffset);
        }

        const needsSpace = precedingChar && !precedingChar.match(/\s/);
        const insertText = (needsSpace ? ' ' : '') + dravya;

        editor.selection.setContent(insertText);
        editor.selection.collapse(false);
        editor.focus();
    }
</script>

{{-- Dravya dynamic script --}}
<script>
    const dravyaFieldId = {{ \App\Models\Field::where('name', 'dravya')->first()->id ?? 0 }};
    let isDravyaEditMode = false;

    async function loadDravyaPresets() {
        const container = document.getElementById('dravyaPresets');
        if (!container) {
            console.error('dravyaPresets container not found in DOM.');
            return;
        }
        container.innerHTML = '';

        if (!dravyaFieldId) {
            alert('Dravya field ID is invalid (0). Check database seeding for "dravya" in fields table.');
            return;
        }

        try {
            const response = await axios.get(`/presets?field_id=${dravyaFieldId}`, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            response.data.forEach(preset => {
                createDravyaPresetButton(preset.button_text, preset.preset_text, preset.id);
            });

            // By using SortableJS for drag and drop
            new Sortable(container, {
                animation: 250,
                ghostClass: 'dragging',
                onEnd: function() {
                    saveNewOrder();
                }
            });
        } catch (error) {
            console.error('Error loading dravya presets:', error.response || error);
            alert(
                `Failed to load dravya presets: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`
            );
        }
    }

    function createDravyaPresetButton(buttonText, presetText, id) {
        const presetDiv = document.createElement('div');
        presetDiv.className = 'relative';
        presetDiv.dataset.id = id; // Store preset ID for ordering
        presetDiv.draggable = true; // Make draggable

        const button = document.createElement('button');
        button.type = 'button';
        button.className =
            'p-2 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition w-full text-centre text-sm';
        button.innerText = buttonText;
        button.onclick = () => insertDravya(presetText);

        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.className =
            'absolute top-0 right-1 text-red-500 hover:text-red-700 hidden dravya-delete-btn';
        deleteBtn.innerHTML = 'x';
        deleteBtn.onclick = () => deleteDravyaPreset(id, buttonText);

        presetDiv.appendChild(button);
        presetDiv.appendChild(deleteBtn);
        document.getElementById('dravyaPresets').appendChild(presetDiv);

        // Toggle delete button visibility based on edit mode
        if (isDravyaEditMode) {
            deleteBtn.classList.remove('hidden');
        }
    }

    // Set up drag-and-drop events on the container
    function setupDragAndDrop() {
        const container = document.getElementById('dravyaPresets');
        const draggables = container.querySelectorAll('div.relative');

        draggables.forEach(draggable => {
            draggable.addEventListener('dragstart', (e) => {
                draggable.classList.add('dragging'); // For styling (e.g., opacity: 0.5 in CSS)
                const clone = draggable.cloneNode(true);
                document.body.appendChild(clone);
                clone.style.position = 'absolute';
                clone.style.top = '-9999px'; // Offscreen
                e.dataTransfer.setDragImage(clone, e.offsetX, e.offsetY);
                setTimeout(() => document.body.removeChild(clone), 0); // Clean up
            });

            draggable.addEventListener('dragend', () => {
                draggable.classList.remove('dragging');
                saveNewOrder(); // Persist order after drop
            });
        });

        container.addEventListener('dragover', (e) => {
            e.preventDefault(); // Allow drop
            const afterElement = getDragAfterElement(container, e.clientX, e.clientY);
            const dragging = document.querySelector('.dragging');
            if (afterElement == null) {
                container.appendChild(dragging);
            } else {
                container.insertBefore(dragging, afterElement);
            }
        });
    }

    // Calculate where to insert based on mouse position (works for grid)
    function getDragAfterElement(container, x, y) {
        const draggableElements = [...container.querySelectorAll('div.relative:not(.dragging)')];

        let closest = draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offsetX = x - (box.left + box.width / 2);
            const offsetY = y - (box.top + box.height / 2);
            const distance = Math.sqrt(offsetX ** 2 + offsetY ** 2);

            if (distance < closest.distance) {
                return {
                    distance,
                    element: child,
                    offsetX
                };
            } else {
                return closest;
            }
        }, {
            distance: Number.POSITIVE_INFINITY,
            element: null,
            offsetX: 0
        });

        if (closest.element) {
            if (closest.offsetX > 0) {
                return closest.element.nextSibling;
            } else {
                return closest.element;
            }
        }

        return null; // Append to end
    }

    // Save new order to backend
    async function saveNewOrder() {
        const container = document.getElementById('dravyaPresets');
        const items = [...container.querySelectorAll('div.relative')];
        const orders = items.map((item, index) => ({
            id: item.dataset.id,
            order: index
        }));

        try {
            await axios.post('/presets/update-order', {
                orders
            }, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                withCredentials: true
            });
        } catch (error) {
            console.error('Error saving order:', error.response || error);
            alert(
                `Failed to save order: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`
            );
        }
    }

    function toggleEditDravyaMode() {
        isDravyaEditMode = !isDravyaEditMode;
        const deleteButtons = document.querySelectorAll('.dravya-delete-btn');
        deleteButtons.forEach(btn => {
            btn.classList.toggle('hidden');
        });
        const editBtn = document.getElementById('editDravyaBtn');
        editBtn.classList.toggle('text-blue-600');
        editBtn.classList.toggle('text-green-600');
    }

    function toggleDravyaForm() {
        const form = document.getElementById('dravyaForm');
        form.classList.toggle('hidden');
        const addBtn = document.getElementById('addDravyaBtn');
        addBtn.innerText = form.classList.contains('hidden') ? '+' : '−';
        if (!form.classList.contains('hidden')) {
            clearDravyaForm();
        }
    }

    function showDravyaPopup() {
        const popup = document.getElementById('dravyaPopup');
        popup.classList.remove('hidden');
        loadDravyaPresets();
    }

    function hideDravyaPopup() {
        const popup = document.getElementById('dravyaPopup');
        popup.classList.add('hidden');
        isDravyaEditMode = false;
        const deleteButtons = document.querySelectorAll('.dravya-delete-btn');
        deleteButtons.forEach(btn => btn.classList.add('hidden'));
        const editBtn = document.getElementById('editDravyaBtn');
        editBtn.classList.remove('text-green-600');
        editBtn.classList.add('text-blue-600');
        const form = document.getElementById('dravyaForm');
        form.classList.add('hidden');
        const addBtn = document.getElementById('addDravyaBtn');
        addBtn.innerText = '+';
    }

    function clearDravyaForm() {
        document.getElementById('dravyaButtonText').value = '';
        document.getElementById('dravyaPresetText').value = '';
    }

    async function saveDravyaPreset() {
        const buttonText = document.getElementById('dravyaButtonText').value.trim();
        const presetText = document.getElementById('dravyaPresetText').value.trim();

        if (!buttonText) {
            alert('Button text is required.');
            return;
        }

        try {
            await axios.post('/presets', {
                field_id: dravyaFieldId,
                button_text: buttonText,
                preset_text: presetText || buttonText,
                display_order: 0
            }, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            loadDravyaPresets();
            clearDravyaForm();
            toggleDravyaForm(); // Hide form after saving
        } catch (error) {
            console.error('Error saving dravya preset:', error.response || error);
            alert(
                `Failed to save dravya preset: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`
            );
        }
    }

    async function deleteDravyaPreset(id, buttonText) {
        if (confirm(`Are you sure you want to delete "${buttonText}"?`)) {
            try {
                await axios.delete(`/presets/${id}`, {
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    withCredentials: true
                });
                loadDravyaPresets();
            } catch (error) {
                console.error('Error deleting dravya preset:', error.response || error);
                alert(
                    `Failed to delete dravya preset: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`
                );
            }
        }
    }
</script>

<script>
    const lakshaneFieldId = {{ \App\Models\Field::where('name', 'lakshane')->first()->id ?? 0 }};
    const lakshaneStorageKey = 'customLakshanePresets';

    async function loadLakshanePresets() {
        const container = document.getElementById('lakshanePresets');
        if (!container) {
            console.error('lakshanePresets container not found in DOM.');
            return;
        }
        container.innerHTML = '';

        if (!lakshaneFieldId) {
            alert('Lakshane field ID is invalid (0). Check database seeding for "lakshane" in fields table.');
            return;
        }

        try {
            const response = await axios.get(`/presets?field_id=${lakshaneFieldId}`, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            response.data.forEach(preset => {
                createLakshanePresetButton(preset.button_text, preset.preset_text, preset.id, true);
            });
        } catch (error) {
            console.error('Error loading lakshane presets:', error.response || error);
            alert(
                `Failed to load lakshane presets: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`
            );
        }

        const localPresets = JSON.parse(localStorage.getItem(lakshaneStorageKey)) || [];
        localPresets.forEach(preset => {
            createLakshanePresetButton(preset, preset, null, false);
        });
    }

    function createLakshanePresetButton(buttonText, presetText, id, isDatabase) {
        const presetDiv = document.createElement('div');
        presetDiv.className = 'relative';

        const button = document.createElement('button');
        button.type = 'button';
        button.className =
            'bg-gray-200 dark:bg-gray-700 p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-500 transition w-full text-centre pr-6';
        button.innerText = buttonText;
        button.onclick = () => insertLakshaneText(presetText);

        presetDiv.appendChild(button);
        document.getElementById('lakshanePresets').appendChild(presetDiv);
    }

    function insertLakshaneText(text) {
        const editor = tinymce.get('lakshane');
        if (!editor) {
            console.error('TinyMCE editor for lakshane not found.');
            return;
        }

        editor.focus();
        const rng = editor.selection.getRng();
        const container = rng.startContainer;
        const cursorPos = rng.startOffset;
        const nodeText = container.textContent || '';
        const beforeText = nodeText.substring(0, cursorPos);
        const afterText = nodeText.substring(cursorPos);

        const needsSpaceBefore = beforeText.trim().length > 0 && !beforeText.trim().endsWith(' ');
        const needsSpaceAfter = afterText.trim().length > 0 && !afterText.trim().startsWith(' ');

        let insertText = '';
        if (needsSpaceBefore) insertText += ' ';
        insertText += text;
        if (needsSpaceAfter) insertText += ' ';

        editor.selection.setContent(insertText);
        editor.selection.collapse(false);
    }

    function insertArrow(arrow) {
        const editor = tinymce.get('lakshane');
        if (!editor) {
            console.error('TinyMCE editor for lakshane not found.');
            return;
        }

        editor.focus();
        const rng = editor.selection.getRng();
        const container = rng.startContainer;
        const cursorPos = rng.startOffset;
        const nodeText = container.textContent || '';
        const beforeText = nodeText.substring(0, cursorPos);
        const afterText = nodeText.substring(cursorPos);

        const needsSpaceBefore = beforeText.trim().length > 0 && !beforeText.trim().endsWith(' ');
        const needsSpaceAfter = afterText.trim().length > 0 && !afterText.trim().startsWith(' ');

        let insertText = '';
        if (needsSpaceBefore) insertText += ' ';
        insertText += arrow;
        if (needsSpaceAfter) insertText += ' ';

        editor.selection.setContent(insertText);
        editor.selection.collapse(false);
    }

    async function loadLakshanePresetList() {
        const list = document.getElementById('lakshanePresetList');
        if (!list) {
            console.error('lakshanePresetList container not found in DOM.');
            return;
        }
        list.innerHTML = '';

        try {
            const response = await axios.get(`/presets?field_id=${lakshaneFieldId}`, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            response.data.forEach(preset => {
                createLakshanePresetRow(preset, true);
            });
        } catch (error) {
            console.error('Error loading lakshane preset list:', error.response || error);
            alert(
                `Failed to load lakshane preset list: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`
            );
        }

        const localPresets = JSON.parse(localStorage.getItem(lakshaneStorageKey)) || [];
        localPresets.forEach(preset => {
            createLakshanePresetRow({
                button_text: preset,
                preset_text: preset,
                id: null
            }, false);
        });
    }

    function createLakshanePresetRow(preset, isDatabase) {
        const row = document.createElement('tr');
        row.className = 'border-b dark:border-gray-600';

        row.innerHTML = `
            <td class="p-2">${preset.button_text}</td>
            <td class="p-2">${preset.preset_text || preset.button_text}</td>
            <td class="p-2">${isDatabase ? 'Database' : 'LocalStorage'}</td>
            <td class="p-2">
                <button type="button" class="text-red-500 hover:text-red-700" onclick="deleteLakshanePreset('${preset.id || ''}', '${preset.button_text}', ${isDatabase})">Delete</button>
            </td>
        `;

        document.getElementById('lakshanePresetList').appendChild(row);
    }

    function openLakshaneModal() {
        const modal = document.getElementById('lakshaneModal');
        if (!modal) {
            console.error('lakshaneModal not found in DOM.');
            return;
        }
        modal.classList.remove('hidden');
        loadLakshanePresetList();
        clearLakshaneForm();
    }

    function closeLakshaneModal() {
        document.getElementById('lakshaneModal').classList.add('hidden');
    }

    function clearLakshaneForm() {
        const buttonText = document.getElementById('lakshaneButtonText');
        const presetText = document.getElementById('lakshanePresetText');
        if (buttonText && presetText) {
            buttonText.value = '';
            presetText.value = '';
        }
    }

    async function saveLakshanePreset() {
        const buttonText = document.getElementById('lakshaneButtonText').value.trim();
        const presetText = document.getElementById('lakshanePresetText').value.trim();

        if (!buttonText) {
            alert('Button text is required.');
            return;
        }

        try {
            await axios.post('/presets', {
                field_id: lakshaneFieldId,
                button_text: buttonText,
                preset_text: presetText || buttonText,
                display_order: 0
            }, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            loadLakshanePresets();
            loadLakshanePresetList();
            clearLakshaneForm();
            closeLakshaneModal();
        } catch (error) {
            console.error('Error saving lakshane preset:', error.response || error);
            alert(
                `Failed to save lakshane preset: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`
            );
        }
    }

    async function deleteLakshanePreset(id, buttonText, isDatabase) {
        if (confirm(`Are you sure you want to delete "${buttonText}"?`)) {
            try {
                if (isDatabase && id) {
                    await axios.delete(`/presets/${id}`, {
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Accept': 'application/json'
                        },
                        withCredentials: true
                    });
                } else {
                    const stored = JSON.parse(localStorage.getItem(lakshaneStorageKey)) || [];
                    const updated = stored.filter(item => item !== buttonText);
                    localStorage.setItem(lakshaneStorageKey, JSON.stringify(updated));
                }
                loadLakshanePresets();
                loadLakshanePresetList();
            } catch (error) {
                console.error('Error deleting lakshane preset:', error.response || error);
                alert(
                    `Failed to delete lakshane preset: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`
                );
            }
        }
    }

    document.addEventListener('DOMContentLoaded', loadLakshanePresets);
</script>


<script>
    const visheshFieldId = {{ \App\Models\Field::where('name', 'vishesh')->first()->id ?? 0 }};
    const visheshStorageKey = 'customVisheshPresets';

    async function loadVisheshPresets() {
        const container = document.getElementById('visheshPresets');
        if (!container) {
            console.error('visheshPresets container not found in DOM.');
            return;
        }
        container.innerHTML = '';

        if (!visheshFieldId) {
            alert('Vishesh field ID is invalid (0). Check database seeding for "vishesh" in fields table.');
            return;
        }

        try {
            const response = await axios.get(`/presets?field_id=${visheshFieldId}`, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            response.data.forEach(preset => {
                createVisheshPresetButton(preset.button_text, preset.preset_text, preset.id, true);
            });
        } catch (error) {
            console.error('Error loading vishesh presets:', error.response || error);
            alert(
                `Failed to load vishesh presets: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`
            );
        }

        const localPresets = JSON.parse(localStorage.getItem(visheshStorageKey)) || [];
        localPresets.forEach(preset => {
            createVisheshPresetButton(preset, preset, null, false);
        });
    }

    function createVisheshPresetButton(buttonText, presetText, id, isDatabase) {
        const presetDiv = document.createElement('div');
        presetDiv.className = 'relative';

        const button = document.createElement('button');
        button.type = 'button';
        button.className =
            'vishesh-box bg-gray-200 dark:bg-gray-700 p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-500 transition w-full text-centre pr-6';
        button.innerText = buttonText;
        button.onclick = () => appendVishesh(presetText);

        presetDiv.appendChild(button);
        document.getElementById('visheshPresets').appendChild(presetDiv);
    }

    async function loadVisheshPresetList() {
        const list = document.getElementById('visheshPresetList');
        if (!list) {
            console.error('visheshPresetList container not found in DOM.');
            return;
        }
        list.innerHTML = '';

        try {
            const response = await axios.get(`/presets?field_id=${visheshFieldId}`, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            response.data.forEach(preset => {
                createVisheshPresetRow(preset, true);
            });
        } catch (error) {
            console.error('Error loading vishesh preset list:', error.response || error);
            alert(
                `Failed to load vishesh preset list: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`
            );
        }

        const localPresets = JSON.parse(localStorage.getItem(visheshStorageKey)) || [];
        localPresets.forEach(preset => {
            createVisheshPresetRow({
                button_text: preset,
                preset_text: preset,
                id: null
            }, false);
        });
    }

    function createVisheshPresetRow(preset, isDatabase) {
        const row = document.createElement('tr');
        row.className = 'border-b dark:border-gray-600';

        row.innerHTML = `
        <td class="p-2">${preset.button_text}</td>
        <td class="p-2">${preset.preset_text || preset.button_text}</td>
        <td class="p-2">${isDatabase ? 'Database' : 'LocalStorage'}</td>
        <td class="p-2">
            <button type="button" class="text-red-500 hover:text-red-700" onclick="deleteVisheshPreset('${preset.id || ''}', '${preset.button_text}', ${isDatabase})">Delete</button>
        </td>
    `;

        document.getElementById('visheshPresetList').appendChild(row);
    }

    function openVisheshModal() {
        const modal = document.getElementById('visheshModal');
        if (!modal) {
            console.error('visheshModal not found in DOM.');
            return;
        }
        modal.classList.remove('hidden');
        loadVisheshPresetList();
        clearVisheshForm();
    }

    function closeVisheshModal() {
        document.getElementById('visheshModal').classList.add('hidden');
    }

    function clearVisheshForm() {
        const buttonText = document.getElementById('visheshButtonText');
        const presetText = document.getElementById('visheshPresetText');
        if (buttonText && presetText) {
            buttonText.value = '';
            presetText.value = '';
        }
    }

    async function saveVisheshPreset() {
        const buttonText = document.getElementById('visheshButtonText').value.trim();
        const presetText = document.getElementById('visheshPresetText').value.trim();

        if (!buttonText) {
            alert('Button text is required.');
            return;
        }

        try {
            await axios.post('/presets', {
                field_id: visheshFieldId,
                button_text: buttonText,
                preset_text: presetText || buttonText,
                display_order: 0
            }, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            loadVisheshPresets();
            loadVisheshPresetList();
            clearVisheshForm();
            closeVisheshModal();
        } catch (error) {
            console.error('Error saving vishesh preset:', error.response || error);
            alert(
                `Failed to save vishesh preset: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`
            );
        }
    }

    async function deleteVisheshPreset(id, buttonText, isDatabase) {
        if (confirm(`Are you sure you want to delete "${buttonText}"?`)) {
            try {
                if (isDatabase && id) {
                    await axios.delete(`/presets/${id}`, {
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Accept': 'application/json'
                        },
                        withCredentials: true
                    });
                } else {
                    const stored = JSON.parse(localStorage.getItem(visheshStorageKey)) || [];
                    const updated = stored.filter(item => item !== buttonText);
                    localStorage.setItem(visheshStorageKey, JSON.stringify(updated));
                }
                loadVisheshPresets();
                loadVisheshPresetList();
            } catch (error) {
                console.error('Error deleting vishesh preset:', error.response || error);
                alert(
                    `Failed to delete vishesh preset: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`
                );
            }
        }
    }

    function appendVishesh(text) {
        const editor = tinymce.get('vishesh');
        if (!editor) {
            console.error('TinyMCE editor for vishesh not found.');
            return;
        }

        editor.focus();
        const rng = editor.selection.getRng();
        const container = rng.startContainer;
        const cursorPos = rng.startOffset;
        const nodeText = container.textContent || '';
        const beforeText = nodeText.substring(0, cursorPos);
        const afterText = nodeText.substring(cursorPos);

        const needsSpaceBefore = beforeText.trim().length > 0 && !beforeText.trim().endsWith(' ');
        const needsSpaceAfter = afterText.trim().length > 0 && !afterText.trim().startsWith(' ');

        let insertText = '';
        if (needsSpaceBefore) insertText += ' ';
        insertText += text;
        if (needsSpaceAfter) insertText += ' ';

        editor.selection.setContent(insertText);
        editor.selection.collapse(false);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Wait for TinyMCE to initialize all editors
        const checkTinyMCE = () => {
            if (typeof tinymce !== 'undefined' && tinymce.get('vishesh')) {
                loadVisheshPresets();
            } else {
                setTimeout(checkTinyMCE, 200);
            }
        };
        checkTinyMCE();
    });
</script>

<script>
    let cameraStream = null;
    let capturedFiles = []; // Array to store captured files and their types

    const cameraModal = document.getElementById("cameraModal");
    const openCameraModal = document.getElementById("openCameraModal");
    const closeCameraModal = document.getElementById("closeCameraModal");
    const captureBtn = document.getElementById("captureBtn");
    const patientPhotosImages = document.getElementById("patientPhotosImages");
    const labReportsImages = document.getElementById("labReportsImages");
    const video = document.getElementById("cameraPreview");
    const cameraSelect = document.getElementById("cameraSelect");
    const photoType = document.getElementById("photoType");
    const photoFileInput = document.getElementById("photoFileInput");
    const photoTypesInput = document.getElementById("photoTypesInput");

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

    if (openCameraModal) {
        openCameraModal.addEventListener("click", async (e) => {
            e.preventDefault();
            cameraModal.classList.remove("hidden");
            await loadCameras();
        });
    }

    if (closeCameraModal) {
        closeCameraModal.addEventListener("click", (e) => {
            e.preventDefault();
            updateFileInput();
            cameraModal.classList.add("hidden");
            stopCamera();
        });
    }

    async function loadCameras() {
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            const videoDevices = devices.filter(device => device.kind === "videoinput");
            if (videoDevices.length === 0) {
                alert("No cameras found.");
                return;
            }
            cameraSelect.innerHTML = "";
            videoDevices.forEach((device, index) => {
                const option = document.createElement("option");
                option.value = device.deviceId;
                option.text = device.label || `Camera ${index + 1}`;
                cameraSelect.appendChild(option);
            });
            await startCamera(videoDevices[0]?.deviceId);
        } catch (error) {
            console.error("Error loading cameras:", error);
            alert("Failed to access camera. Please allow permissions.");
        }
    }

    async function startCamera(deviceId) {
        stopCamera();
        try {
            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    deviceId: deviceId ? {
                        exact: deviceId
                    } : undefined
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

    if (cameraSelect) {
        cameraSelect.addEventListener("change", () => {
            startCamera(cameraSelect.value);
        });
    }

    if (captureBtn) {
        captureBtn.addEventListener("click", (e) => {
            e.preventDefault();
            const canvas = document.createElement("canvas");
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext("2d").drawImage(video, 0, 0, canvas.width, canvas.height);

            canvas.toBlob((blob) => {
                const file = new File([blob], `photo_${Date.now()}.png`, {
                    type: "image/png"
                });
                const photoTypeValue = photoType.value;

                // Add to capturedFiles array
                capturedFiles.push({
                    file,
                    type: photoTypeValue
                });

                // Create preview container
                const previewContainer = document.createElement("div");
                previewContainer.classList.add("preview-container");

                // Create image preview
                const img = document.createElement("img");
                img.src = URL.createObjectURL(blob);
                img.classList.add("w-full", "h-full", "object-cover", "rounded", "border",
                    "border-gray-300");

                // Create delete button
                const deleteBtn = document.createElement("button");
                deleteBtn.innerHTML = "✖";
                deleteBtn.classList.add("delete-btn");
                deleteBtn.addEventListener("click", () => {
                    // Remove from capturedFiles
                    const index = capturedFiles.findIndex(f => f.file === file);
                    if (index !== -1) capturedFiles.splice(index, 1);
                    // Remove preview from DOM
                    previewContainer.remove();
                });

                // Append elements
                previewContainer.appendChild(img);
                previewContainer.appendChild(deleteBtn);

                // Append to the correct section based on photo type
                if (photoTypeValue === "patient_photo") {
                    patientPhotosImages.appendChild(previewContainer);
                } else if (photoTypeValue === "lab_report") {
                    labReportsImages.appendChild(previewContainer);
                }
            }, "image/png");
        });
    }

    function updateFileInput() {
        const dataTransfer = new DataTransfer();
        const types = [];

        capturedFiles.forEach(({
            file,
            type
        }) => {
            dataTransfer.items.add(file);
            types.push(type);
        });

        photoFileInput.files = dataTransfer.files;
        photoTypesInput.value = JSON.stringify(types); // Store types as JSON string
    }

    // Reports functionality
    let reports = [];
    let editMode = false;
    let editIndex = -1;
    let editStaticMode = false;
    let editStaticFollowupId = null;
    let editStaticReportIndex = null;

    function openReportModal() {
        const modal = document.getElementById('reportModal');
        if (!modal) {
            console.error('reportModal not found in DOM.');
            return;
        }
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('reportText').focus();

        // Update modal title and button based on mode
        const modalTitle = document.querySelector('#reportModal h2');
        const addButton = document.querySelector('#reportModal button:last-child');

        if (editMode) {
            modalTitle.textContent = 'Edit Report';
            addButton.textContent = 'Update';
        } else if (editStaticMode) {
            modalTitle.textContent = 'Edit Report';
            addButton.textContent = 'Update';
        } else {
            modalTitle.textContent = 'Add New Report';
            addButton.textContent = 'Add';
        }

        // Add click outside to close
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeReportModal();
            }
        });

        // Add keyboard support
        const handleKeydown = function(e) {
            if (e.key === 'Escape') {
                closeReportModal();
            }
        };

        document.addEventListener('keydown', handleKeydown);

        // Store the handler to remove it later
        modal._keydownHandler = handleKeydown;
    }

    function closeReportModal() {
        const modal = document.getElementById('reportModal');
        if (!modal) {
            console.error('reportModal not found in DOM.');
            return;
        }
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('reportText').value = '';

        // Reset edit mode
        editMode = false;
        editIndex = -1;
        editStaticMode = false;
        editStaticFollowupId = null;
        editStaticReportIndex = null;

        // Remove keyboard event listener
        if (modal._keydownHandler) {
            document.removeEventListener('keydown', modal._keydownHandler);
            modal._keydownHandler = null;
        }
    }

    function addReport() {
        const reportText = document.getElementById('reportText').value.trim();
        if (!reportText) {
            alert('Please enter a report.');
            return;
        }

        if (editStaticMode && editStaticFollowupId !== null && editStaticReportIndex !== null) {
            // Update static report via AJAX
            fetch(`/followups/${editStaticFollowupId}/reports/${editStaticReportIndex}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ text: reportText })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the DOM with the new report data
                    let reportItem = null;
                    const allReportItems = document.querySelectorAll('.report-item');

                    for (const item of allReportItems) {
                        if (item.dataset.followupId == editStaticFollowupId && item.dataset.reportIndex == editStaticReportIndex) {
                            reportItem = item;
                            break;
                        }
                    }

                    if (reportItem) {
                        const textDiv = reportItem.querySelector('div.text-sm.font-medium') ||
                                       reportItem.querySelector('div.text-sm') ||
                                       reportItem.querySelector('[class*="text-sm"]');

                        if (textDiv) {
                            const newHtml = reportText.replace(/\n/g, '<br>');
                            textDiv.innerHTML = newHtml;

                            // Visual feedback
                            textDiv.style.backgroundColor = '#e0f7e0'; // Light green
                            setTimeout(() => {
                                textDiv.style.backgroundColor = '';
                            }, 1000);
                        }
                        reportItem.dataset.originalText = reportText;
                        reportItem.dataset.text = reportText.toLowerCase();

                        // Force reapply search highlighting if search is active
                        const searchInput = document.getElementById('reportSearch');
                        if (searchInput && searchInput.value.trim()) {
                            const event = new Event('input', { bubbles: true });
                            searchInput.dispatchEvent(event);
                        }
                    }
                    closeReportModal();
                } else {
                    alert('Error updating report: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating report. Please try again.');
            });
        } else if (editMode && editIndex >= 0) {
            // Update existing report
            reports[editIndex].text = reportText;
        } else {
            // Add new report
            const now = new Date();
            const isoTimestamp = now.toISOString();
            const displayTimestamp = now.getDate().toString().padStart(2, '0') + '/' +
                             (now.getMonth() + 1).toString().padStart(2, '0') + '/' +
                             now.getFullYear() + ' ' +
                             now.getHours().toString().padStart(2, '0') + ':' +
                             now.getMinutes().toString().padStart(2, '0') + ':' +
                             now.getSeconds().toString().padStart(2, '0');

            const report = {
                text: reportText,
                timestamp: displayTimestamp,
                isoTimestamp: isoTimestamp
            };

            reports.push(report);
        }

        updateReportsDisplay();
        updateReportsInput();
        closeReportModal();
    }

    function updateReportsDisplay() {
        const container = document.getElementById('reportsList');
        if (!container) return;

        // Remove existing dynamic reports
        const existingDynamic = container.querySelectorAll('.dynamic-report');
        existingDynamic.forEach(el => el.remove());

        // Find the first static report to insert before
        const firstStaticReport = container.querySelector('.report-item:not(.dynamic-report)');
        const insertBeforeElement = firstStaticReport || null;

        // Add new dynamic reports at the top
        reports.forEach((report, index) => {
            const reportDiv = document.createElement('div');
            reportDiv.className = 'dynamic-report report-item bg-gray-50 dark:bg-gray-800 p-3 rounded mb-2 flex justify-between items-start';
            reportDiv.dataset.text = report.text.toLowerCase();
            reportDiv.dataset.displayTimestamp = report.timestamp;
            reportDiv.dataset.originalText = report.text;

            const contentDiv = document.createElement('div');
            contentDiv.className = 'flex-1';

            const textDiv = document.createElement('div');
            textDiv.className = 'text-sm text-gray-800 dark:text-gray-200 font-medium';
            textDiv.innerHTML = report.text.replace(/\n/g, '<br>');

            const timestampDiv = document.createElement('div');
            timestampDiv.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1';
            if (report.isoTimestamp) {
                timestampDiv.setAttribute('data-timestamp', report.isoTimestamp);
            }
            timestampDiv.textContent = report.timestamp;

            const buttonsDiv = document.createElement('div');
            buttonsDiv.className = 'flex flex-col space-y-1 ml-2';

            const editBtn = document.createElement('button');
            editBtn.type = 'button';
            editBtn.className = 'text-blue-500 hover:text-blue-700 p-1 rounded';
            editBtn.title = 'Edit';
            editBtn.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            `;
            editBtn.onclick = () => editDynamicReport(index);

            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.className = 'text-red-500 hover:text-red-700 p-1 rounded';
            deleteBtn.title = 'Delete';
            deleteBtn.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            `;
            deleteBtn.onclick = () => removeReport(index);

            buttonsDiv.appendChild(editBtn);
            buttonsDiv.appendChild(deleteBtn);

            contentDiv.appendChild(textDiv);
            contentDiv.appendChild(timestampDiv);
            reportDiv.appendChild(contentDiv);
            reportDiv.appendChild(buttonsDiv);

            // Insert at the top, before the first static report
            if (insertBeforeElement) {
                container.insertBefore(reportDiv, insertBeforeElement);
            } else {
                container.appendChild(reportDiv);
            }
        });
    }

    function removeReport(index) {
        if (confirm('Are you sure you want to delete this report?')) {
            reports.splice(index, 1);
            updateReportsDisplay();
            updateReportsInput();
        }
    }

    function updateReportsInput() {
        const input = document.getElementById('reportsInput');
        if (input) {
            input.value = JSON.stringify(reports);
        }
    }

    // Initialize reports if editing existing follow-up
    document.addEventListener('DOMContentLoaded', function() {
        const reportsInput = document.getElementById('reportsInput');
        if (reportsInput && reportsInput.value) {
            try {
                const savedReports = JSON.parse(reportsInput.value);
                if (Array.isArray(savedReports)) {
                    reports = savedReports;
                }
            } catch (e) {
                console.error('Error parsing saved reports:', e);
            }
        }

        updateReportsDisplay();
        updateReportsInput();

        // Add search functionality
        const searchInput = document.getElementById('reportSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                filterReports(this.value.trim().toLowerCase());
            });
        }

        // Ensure reports input is updated before form submission
        const form = document.getElementById('followUpForm');
        if (form) {
            form.addEventListener('submit', function() {
                updateReportsInput();
            });
        }
    });

    function filterReports(searchTerm) {
        const allReports = document.querySelectorAll('.report-item');
        allReports.forEach(report => {
            const text = report.dataset.text || '';
            const displayTimestamp = report.dataset.displayTimestamp || '';
            const followupDate = report.dataset.followupDate || '';

            const matchesSearch = !searchTerm ||
                text.includes(searchTerm) ||
                displayTimestamp.toLowerCase().includes(searchTerm) ||
                followupDate.toLowerCase().includes(searchTerm);

            report.style.display = matchesSearch ? 'block' : 'none';
        });
    }

    function editReport(button) {
        const reportItem = button.closest('.report-item');
        const reportText = reportItem.dataset.originalText;
        const followupId = reportItem.dataset.followupId;
        const reportIndex = reportItem.dataset.reportIndex;

        // Set static report editing mode
        editStaticMode = true;
        editStaticFollowupId = followupId;
        editStaticReportIndex = reportIndex;
        editMode = false;
        editIndex = -1;

        // Open the report modal and pre-fill with the text
        openReportModal();
        document.getElementById('reportText').value = reportText;
    }

    function editDynamicReport(index) {
        const report = reports[index];
        if (!report) return;

        editMode = true;
        editIndex = index;

        // Open the report modal and pre-fill with the text
        openReportModal();
        document.getElementById('reportText').value = report.text;
    }

    function deleteReport(button) {
        if (confirm('Are you sure you want to delete this report?')) {
            const reportItem = button.closest('.report-item');
            const followupId = reportItem.dataset.followupId;
            const reportIndex = reportItem.dataset.reportIndex;

            // Make AJAX call to soft delete the report
            fetch(`/followups/${followupId}/reports/${reportIndex}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    reportItem.remove();
                    console.log('Successfully deleted report from DOM');
                } else {
                    alert('Error deleting report: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting report. Please try again.');
            });
        }
    }
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('reportSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const reportItems = document.querySelectorAll('.report-item');
            reportItems.forEach(item => {
                const textDiv = item.querySelector('div.text-sm.font-medium') ||
                               item.querySelector('div.text-sm') ||
                               item.querySelector('[class*="text-sm"]');
                const dateDiv = item.querySelector('.text-xs.text-gray-500, .text-xs.text-gray-400') ||
                               item.querySelector('.text-xs');

                if (textDiv) {
                    textDiv.innerHTML = item.dataset.originalText.replace(/\n/g, '<br>');
                }
                if (dateDiv) {
                    dateDiv.innerHTML = item.dataset.timestamp + ' • Follow-up: ' + item.dataset.followupDate;
                }

                const text = item.dataset.text || '';
                const followupDate = item.dataset.followupDate || '';

                const matchesSearch = !searchTerm ||
                    text.includes(searchTerm) ||
                    followupDate.toLowerCase().includes(searchTerm);

                if (searchTerm === '') {
                    item.style.display = 'block';
                } else if (matchesSearch) {
                    item.style.display = 'block';

                    const regex = new RegExp(`(${this.value.trim()})`, 'gi');
                    if (textDiv) {
                        textDiv.innerHTML = item.dataset.originalText.replace(/\n/g, '<br>').replace(regex, '<mark>$1</mark>');
                    }

                    if (followupDate.toLowerCase().includes(searchTerm) && dateDiv) {
                        const originalDateText = item.dataset.timestamp + ' • Follow-up: ' + item.dataset.followupDate;
                        dateDiv.innerHTML = originalDateText.replace(regex, '<mark>$1</mark>');
                    }
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>
