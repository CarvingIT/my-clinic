/**
 * Global Marathi / Indic Number Converter & Google Input Tools (IME) Fix
 * 
 * 1. Converts Devanagari numerals (०-९) to English numerals (0-9).
 * 2. Fixes Google Input Tools space-insertion between digits (e.g. "१ २ ३" -> "123").
 * 3. Handles decimal numbers with spaces (e.g. "१ . ५" -> "1.5").
 * 4. Works safely with Chromium IME without destroying composition buffers (e.isComposing check).
 * 5. Supports both INPUT (text, number, tel, search, etc.) and TEXTAREA fields.
 * 6. Converts type="number" to type="text" inputmode="decimal" to prevent Chromium IME blocking.
 * 7. Dispatches 'marathiConverted' custom events for dependent calculations (birthdate, dues, etc.).
 */

const marathiToEnglishMapping = {
    // Devanagari numerals
    '०': '0', '१': '1', '२': '2', '३': '3', '४': '4',
    '५': '5', '६': '6', '७': '7', '८': '8', '९': '9',
    // Devanagari punctuation
    '।': '.', '॥': '.',
    // Fullwidth digits (from some IMEs)
    '０': '0', '１': '1', '２': '2', '３': '3', '४': '4',
    '५': '5', '६': '6', '７': '7', '８': '8', '９': '9'
};

const DEVANAGARI_DIGITS_REGEX = /[०-९।॥０-９]/g;
const INVISIBLE_CHARS_REGEX = /[\u200B\u200C\u200D\uFEFF\u200E\u200F]/g;
const DIGIT_SPACES_REGEX = /(?<=\d)[\s\u00A0\u202F]+(?=\d)/g;
const DECIMAL_SPACES_REGEX = /(?<=\d)[\s\u00A0\u202F]*\.[\s\u00A0\u202F]*(?=\d)/g;

/**
 * Convert Marathi / Devanagari digits to English digits and clean whitespace.
 * 
 * @param {string|number} input - Raw input string
 * @param {boolean} isNumericOnly - If true, strips all non-numeric characters and all spaces
 * @returns {string} Converted string
 */
export function convertMarathiToEnglish(input, isNumericOnly = false) {
    if (input === null || input === undefined) return '';
    let result = String(input);

    // 1. Transliterate Devanagari / fullwidth digits and danda to English
    result = result.replace(DEVANAGARI_DIGITS_REGEX, (match) => marathiToEnglishMapping[match] || match);

    // 2. Remove invisible Unicode markers / zero-width spaces
    result = result.replace(INVISIBLE_CHARS_REGEX, '');

    if (isNumericOnly) {
        // Strip all whitespace characters completely for numeric fields
        result = result.replace(/[\s\u00A0\u202F]+/g, '');
    } else {
        // For general text / textarea:
        // Collapse spaces between consecutive digits (e.g. "1 2 3" -> "123")
        result = result.replace(DIGIT_SPACES_REGEX, '');
        // Collapse spaces around decimal points between digits (e.g. "1 . 5" -> "1.5")
        result = result.replace(DECIMAL_SPACES_REGEX, '.');
    }

    return result;
}

// Expose globally for inline scripts / views
window.convertMarathiToEnglish = convertMarathiToEnglish;

/**
 * Chromium natively disables IME (Google Input Tools) on input[type="number"].
 * We transform type="number" to type="text" with inputmode="decimal" / "numeric".
 */
export function fixNumericInput(input) {
    if (!input || input.dataset.imeFixed) return;
    if (input.tagName !== 'INPUT') return;
    if (input.classList && input.classList.contains('no-transliteration')) return;

    const isNumberType = input.type === 'number' || 
                         input.getAttribute('type') === 'number' || 
                         input.dataset.type === 'number';

    if (isNumberType) {
        input.dataset.type = 'number';
        input.dataset.imeFixed = 'true';

        const step = input.getAttribute('step');
        const allowDecimal = !step || step === 'any' || step.includes('.');
        input.dataset.allowDecimal = allowDecimal ? 'true' : 'false';

        try {
            input.type = 'text';
        } catch (e) {
            input.setAttribute('type', 'text');
        }
        input.setAttribute('inputmode', allowDecimal ? 'decimal' : 'numeric');
    }
}

/**
 * Process and convert the value of a given input / textarea element.
 */
export function handleConversion(target) {
    if (!target) return;
    const tagName = target.tagName ? target.tagName.toUpperCase() : '';
    if (tagName !== 'INPUT' && tagName !== 'TEXTAREA') return;
    if (target.classList && target.classList.contains('no-transliteration')) return;

    const inputType = target.type ? target.type.toLowerCase() : 'text';
    // Skip file, checkbox, radio, button, password, color, etc.
    if (tagName === 'INPUT' && !['text', 'number', 'tel', 'email', 'search', 'url'].includes(inputType)) {
        return;
    }

    const isNumericOnly = target.dataset.imeFixed === 'true' || 
                          target.dataset.type === 'number' || 
                          target.getAttribute('inputmode') === 'numeric' || 
                          target.getAttribute('inputmode') === 'decimal';

    const val = target.value;
    if (!val && val !== 0) return;

    let converted = convertMarathiToEnglish(val, isNumericOnly);

    // If numeric field, enforce numeric formatting
    if (isNumericOnly) {
        const allowDecimal = target.dataset.allowDecimal !== 'false';
        if (allowDecimal) {
            converted = converted.replace(/[^0-9.]/g, '');
            const parts = converted.split('.');
            if (parts.length > 2) {
                converted = parts[0] + '.' + parts.slice(1).join('');
            }
        } else {
            converted = converted.replace(/[^0-9]/g, '');
        }
    }

    if (converted !== val) {
        // Preserve cursor position if possible
        let start = null;
        let end = null;
        try {
            if (typeof target.selectionStart === 'number') {
                start = target.selectionStart;
                end = target.selectionEnd;
                // Adjust cursor position if length decreased due to space removal
                const diff = val.length - converted.length;
                if (diff > 0 && start !== null) {
                    start = Math.max(0, start - diff);
                    end = Math.max(0, end - diff);
                }
            }
        } catch (err) {
            // Some input types do not support selectionStart
        }

        target.value = converted;

        if (start !== null && end !== null && document.activeElement === target) {
            try {
                target.setSelectionRange(start, end);
            } catch (err) {
                // Ignore if not supported
            }
        }

        // Dispatch events for Livewire, Alpine.js, and custom calculation listeners
        target.dispatchEvent(new CustomEvent('marathiConverted', { 
            bubbles: true, 
            detail: { original: val, converted: converted } 
        }));
    }
}

/**
 * Initialize all input hooks and observers
 */
function initMarathiInputFix() {
    function processAllInputs() {
        document.querySelectorAll('input[type="number"]').forEach(fixNumericInput);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', processAllInputs);
    } else {
        processAllInputs();
    }

    // Capture focusin/pointerdown to fix number inputs before user types
    ['focusin', 'pointerdown', 'mouseenter'].forEach(eventType => {
        document.addEventListener(eventType, function(e) {
            if (e.target && e.target.tagName === 'INPUT') {
                if (e.target.type === 'number' || e.target.getAttribute('type') === 'number') {
                    fixNumericInput(e.target);
                }
            }
        }, true);
    });

    // Observe newly added DOM nodes (Modals, Livewire, Alpine.js, dynamic rows)
    if (window.MutationObserver) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) {
                        if (node.tagName === 'INPUT' && (node.type === 'number' || node.getAttribute('type') === 'number')) {
                            fixNumericInput(node);
                        } else if (node.querySelectorAll) {
                            node.querySelectorAll('input[type="number"]').forEach(fixNumericInput);
                        }
                    }
                });
            });
        });
        observer.observe(document.body || document.documentElement, { childList: true, subtree: true });
    }

const HAS_DEVANAGARI_REGEX = /[०-९।॥０-９]/;

// 1. Live conversion on 'input' event - converts digits instantly as you type
    document.addEventListener('input', function(e) {
        const target = e.target;
        if (!target) return;
        
        const tagName = target.tagName ? target.tagName.toUpperCase() : '';
        if (tagName !== 'INPUT' && tagName !== 'TEXTAREA') return;

        const isNumericOnly = target.dataset.imeFixed === 'true' || 
                              target.dataset.type === 'number' || 
                              target.getAttribute('inputmode') === 'numeric' || 
                              target.getAttribute('inputmode') === 'decimal';

        // If numeric field, ALWAYS convert immediately on every keystroke
        if (isNumericOnly) {
            handleConversion(target);
            return;
        }

        // For text / textarea fields:
        // If it contains Devanagari digits (०-९), convert live immediately without waiting for space!
        if (HAS_DEVANAGARI_REGEX.test(target.value)) {
            handleConversion(target);
            return;
        }

        // Only skip if active IME is in the middle of transliterating pure alphabetic Marathi words
        if (e.isComposing) return;
        handleConversion(target);
    }, true);

    // 2. compositionend: Google Input Tools finishes word / number transliteration
    document.addEventListener('compositionend', function(e) {
        const target = e.target;
        handleConversion(target);
        setTimeout(function() {
            handleConversion(target);
        }, 10);
    }, true);

    // 3. Fallback triggers for paste, blur, change, keyup
    ['blur', 'change', 'paste'].forEach(eventType => {
        document.addEventListener(eventType, function(e) {
            handleConversion(e.target);
        }, true);
    });

    document.addEventListener('keyup', function(e) {
        handleConversion(e.target);
    }, true);
}

// Auto-run on load
initMarathiInputFix();
