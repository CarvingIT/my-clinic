document.addEventListener('DOMContentLoaded', function() {
    function checkGoogleApiAndInitialize() {
      if (typeof google !== 'undefined' && google.elements && google.elements.transliteration) {
        // condition for google api
        console.log("Google API is ready. Initializing...");
        initializeTransliterationInner(); // Call the inner function
      } else {

        console.log("Google API not ready yet. Checking again...");
        setTimeout(checkGoogleApiAndInitialize, 50); // Check every 50ms
      }
    }

    function initializeTransliterationInner(fieldIds, languageCode = google.elements.transliteration.LanguageCode.MARATHI, shortcut = 'ctrl+q'){
     google.elements.transliteration.loadDefaultTransliteration();
     var options = {
       sourceLanguage: google.elements.transliteration.LanguageCode.ENGLISH,
       destinationLanguage: [google.elements.transliteration.LanguageCode.MARATHI],
       shortcutKey: shortcut,
       transliterationEnabled: true
     };

     var control = new google.elements.transliteration.TransliterationControl(options);
     control.makeTransliteratable(fieldIds);
    }

    checkGoogleApiAndInitialize(); // check when dom is loaded
  });
