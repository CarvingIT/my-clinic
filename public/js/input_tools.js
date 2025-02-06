// resources/js/input_tools.js
document.addEventListener('DOMContentLoaded', function() { // Wait for DOM to load

    // Function to initialize transliteration for given field IDs
    window.initializeTransliteration = function(fieldIds, languageCode = google.elements.transliteration.LanguageCode.MARATHI, shortcut = 'ctrl+g') {
      google.elements.transliteration.loadDefaultTransliteration();

      var options = {
        sourceLanguage: google.elements.transliteration.LanguageCode.ENGLISH,
        destinationLanguage: [languageCode],
        shortcutKey: shortcut,
        transliterationEnabled: true
      };

      var control = new google.elements.transliteration.TransliterationControl(options);
      control.makeTransliteratable(fieldIds);
    };

  });
