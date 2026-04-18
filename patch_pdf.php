<?php

$files = [
    'resources/views/prescriptions/generate.blade.php',
    'resources/views/prescriptions/pdf-download.blade.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;

    $content = file_get_contents($file);

    // Replace CSS sizes in generate.blade.php
    if (strpos($file, 'generate.blade.php') !== false) {
        $content = str_replace('<style>', "<style>\n        :root {\n            --font-scale: 1;\n        }", $content);
        
        $sizes = [13, 12, 11, 14, 10, 28];
        foreach ($sizes as $size) {
            $content = preg_replace("/font-size:\s*{$size}px;/", "font-size: calc({$size}px * var(--font-scale));", $content);
        }

        // Add UI to toolbar
        $toolbarReplacement = <<<HTML
            <div class="flex gap-3 items-center">
                <div class="flex items-center bg-gray-200 rounded-lg px-2 shadow-sm border border-gray-300">
                    <span class="text-xs font-semibold uppercase text-gray-500 mr-2 ml-1">Font Size</span>
                    <button onclick="changeFontSize(-0.1)" type="button" class="p-2 text-gray-700 hover:text-black focus:outline-none" title="Decrease Font Size">
                        <i class="fas fa-minus"></i>
                    </button>
                    <span class="mx-1 font-bold w-12 text-center" id="fontSizeDisplay">100%</span>
                    <button onclick="changeFontSize(0.1)" type="button" class="p-2 text-gray-700 hover:text-black focus:outline-none" title="Increase Font Size">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button onclick="resetFontSize()" type="button" class="ml-1 px-2 py-1 text-xs text-blue-600 hover:bg-blue-100 rounded transition" title="Reset Font Size">Reset</button>
                </div>
                <button onclick="goBack()" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition flex items-center gap-2">
HTML;
        $content = str_replace('<div class="flex gap-3">'."\n                <button onclick=\"goBack()\" class=\"px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition flex items-center gap-2\">", $toolbarReplacement, $content);

        // Add hidden input
        $content = str_replace('<button type="submit"', '<input type="hidden" name="font_scale" id="fontScaleInput" value="1">'."\n                    <button type=\"submit\"", $content);

        // Add JS script
        $js = <<<HTML
    <script>
        function goBack() {
            window.history.back();
        }
        
        let currentFontScale = 1;
        
        function changeFontSize(delta) {
            currentFontScale += delta;
            if (currentFontScale < 0.5) currentFontScale = 0.5;
            if (currentFontScale > 2.0) currentFontScale = 2.0;
            updateFontScale();
        }

        function resetFontSize() {
            currentFontScale = 1;
            updateFontScale();
        }

        function updateFontScale() {
            document.documentElement.style.setProperty('--font-scale', currentFontScale);
            document.getElementById('fontSizeDisplay').textContent = Math.round(currentFontScale * 100) + '%';
            document.getElementById('fontScaleInput').value = currentFontScale.toFixed(2);
        }
    </script>
</body>
HTML;
        
        $content = preg_replace('/<script>.*?<\/script>\s*<\/body>/s', $js, $content);
    }
    
    // Replace CSS sizes in pdf-download.blade.php
    if (strpos($file, 'pdf-download.blade.php') !== false) {
        $phpVars = <<<HTML
<head>
    <meta charset="UTF-8">
    @php
        \$fontScale = \$font_scale ?? 1;
    @endphp
HTML;
        $content = str_replace('<head>'."\n    <meta charset=\"UTF-8\">", $phpVars, $content);

        $sizes = [13, 12, 11, 14, 10, 28];
        foreach ($sizes as $size) {
            $content = preg_replace("/font-size:\s*{$size}px;/", "font-size: {{ {$size} * \$fontScale }}px;", $content);
        }
    }

    file_put_contents($file, $content);
}

