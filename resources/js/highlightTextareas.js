function initializeHighlightableTextareas() {
    const textareas = document.querySelectorAll('.highlightable-textarea');
    const stateMap = new Map(); // Store previous content for each textarea

    textareas.forEach((textarea, index) => {
        // Create container for buttons
        const container = document.createElement('div');
        container.className = 'relative';
        textarea.parentNode.insertBefore(container, textarea);
        container.appendChild(textarea);

        // Create Highlight button
        const highlightButton = document.createElement('button');
        highlightButton.type = 'button';
        highlightButton.className = 'hidden absolute top-0 right-0 mt-2 mr-2 px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition';
        highlightButton.innerHTML = '<i class="fas fa-highlighter"></i>';
        highlightButton.title = 'Highlight';
        container.appendChild(highlightButton);

        // Create Undo button
        const undoButton = document.createElement('button');
        undoButton.type = 'button';
        undoButton.className = 'hidden absolute top-0 right-8 mt-2 mr-2 px-2 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 transition';
        undoButton.innerHTML = '<i class="fas fa-undo"></i>';
        undoButton.title = 'Undo';
        container.appendChild(undoButton);

        // Show/hide highlight button on text selection
        textarea.addEventListener('mouseup', () => {
            const selection = textarea.value.substring(
                textarea.selectionStart,
                textarea.selectionEnd
            );
            highlightButton.classList.toggle('hidden', !selection);
        });

        // Handle highlight button click
        highlightButton.addEventListener('click', (e) => {
            e.preventDefault();
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const selectedText = textarea.value.substring(start, end);
            if (!selectedText) return;

            // Store current content for undo
            stateMap.set(textarea, textarea.value);

            // Wrap selected text with [h]...[/h]
            const before = textarea.value.substring(0, start);
            const after = textarea.value.substring(end);
            textarea.value = before + `[h]${selectedText}[/h]` + after;

            // Show undo button and hide highlight button
            undoButton.classList.remove('hidden');
            highlightButton.classList.add('hidden');
            textarea.focus();
        });

        // Handle undo button click
        undoButton.addEventListener('click', (e) => {
            e.preventDefault();
            const previousContent = stateMap.get(textarea);
            if (previousContent !== undefined) {
                textarea.value = previousContent;
                stateMap.delete(textarea); // Clear undo history
                undoButton.classList.add('hidden');
                textarea.focus();
            }
        });
    });
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', initializeHighlightableTextareas);
