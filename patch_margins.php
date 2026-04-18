<?php

$files = [
    'resources/views/prescriptions/generate.blade.php',
    'app/Http/Controllers/PrescriptionController.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;

    $content = file_get_contents($file);

    // Replace Controller
    if (strpos($file, 'PrescriptionController.php') !== false) {
        $setupMargins = <<<PHP
        \$pdf->setOption('page-size', 'A4');
        \$pdf->setOption('margin-top', \$request->get('margin_top', 10) . 'mm');
        \$pdf->setOption('margin-bottom', \$request->get('margin_bottom', 10) . 'mm');
        \$pdf->setOption('margin-left', \$request->get('margin_left', 10) . 'mm');
        \$pdf->setOption('margin-right', \$request->get('margin_right', 10) . 'mm');
PHP;
        $content = preg_replace("/\\\$pdf->setOption\('margin-top', '10mm'\);[\r\n\s]+\\\$pdf->setOption\('margin-bottom', '10mm'\);[\r\n\s]+\\\$pdf->setOption\('margin-left', '10mm'\);[\r\n\s]+\\\$pdf->setOption\('margin-right', '10mm'\);/s", "        // Dynamic Margins\n        \$pdf->setOption('margin-top', \$request->get('margin_top', 10) . 'mm');\n        \$pdf->setOption('margin-bottom', \$request->get('margin_bottom', 10) . 'mm');\n        \$pdf->setOption('margin-left', \$request->get('margin_left', 10) . 'mm');\n        \$pdf->setOption('margin-right', \$request->get('margin_right', 10) . 'mm');", $content);
    }
    
    // Replace View
    if (strpos($file, 'generate.blade.php') !== false) {
        // Add variables for margin
        $content = str_replace('<style>', "<style>\n        :root {\n            --font-scale: 1;\n            --margin-top: 10mm;\n            --margin-right: 10mm;\n            --margin-bottom: 10mm;\n            --margin-left: 10mm;\n        }", $content);

        // Remove old :root just in case we have duplicates now
        $content = preg_replace("/<style>\n        :root {\n            --font-scale: 1;\n        }/", "<style>\n        :root {\n            --font-scale: 1;\n            --margin-top: 10mm;\n            --margin-right: 10mm;\n            --margin-bottom: 10mm;\n            --margin-left: 10mm;\n        }", $content);
        
        $content = preg_replace("/@page {\s*size: A4;\s*margin: 10mm;\s*}/", "@page {\n            size: A4;\n            margin: var(--margin-top) var(--margin-right) var(--margin-bottom) var(--margin-left);\n        }", $content);
        
        // Let's add UI
        $toolbarReplacement = <<<HTML
            <div class="flex gap-3 items-center flex-wrap">
                <!-- Margins -->
                <div class="flex items-center bg-gray-200 rounded-lg px-2 py-1 shadow-sm border border-gray-300 gap-2">
                    <span class="text-xs font-semibold uppercase text-gray-500">Margin(mm):</span>
                    <input type="number" id="marginTop" value="10" min="0" max="100" class="w-12 text-center text-sm border-gray-300 rounded p-1" title="Top Margin" onchange="updateMargins()">
                    <input type="number" id="marginRight" value="10" min="0" max="100" class="w-12 text-center text-sm border-gray-300 rounded p-1" title="Right Margin" onchange="updateMargins()">
                    <input type="number" id="marginBottom" value="10" min="0" max="100" class="w-12 text-center text-sm border-gray-300 rounded p-1" title="Bottom Margin" onchange="updateMargins()">
                    <input type="number" id="marginLeft" value="10" min="0" max="100" class="w-12 text-center text-sm border-gray-300 rounded p-1" title="Left Margin" onchange="updateMargins()">
                </div>
                <!-- Fonts -->
                <div class="flex items-center bg-gray-200 rounded-lg px-2 shadow-sm border border-gray-300">
                    <span class="text-xs font-semibold uppercase text-gray-500 mr-2 ml-1">Font Size</span>
HTML;
        $content = preg_replace('/<div class="flex gap-3 items-center">[\s\n]*<div class="flex items-center bg-gray-200 rounded-lg px-2 shadow-sm border border-gray-300">\s*<span class="text-xs font-semibold uppercase text-gray-500 mr-2 ml-1">Font Size<\/span>/s', $toolbarReplacement, $content);
        
        // Let's replace the visual margin on the screen as well to represent it better
        // "prescription-wrapper bg-white rounded-lg shadow-lg overflow-hidden border border-gray-300 p-6" 
        $content = str_replace('prescription-wrapper bg-white rounded-lg shadow-lg overflow-hidden border border-gray-300 p-6', 'prescription-wrapper bg-white rounded-lg shadow-lg overflow-hidden border border-gray-300', $content);
        // add inline style for the wrapper to preview margins on screen
        $content = str_replace('<div class="prescription-wrapper', '<div id="prescriptionVisual" style="padding: var(--margin-top) var(--margin-right) var(--margin-bottom) var(--margin-left);" class="prescription-wrapper', $content);

        // Add hidden inputs
        $hiddenInputs = <<<HTML
                    <input type="hidden" name="margin_top" id="input_margin_top" value="10">
                    <input type="hidden" name="margin_right" id="input_margin_right" value="10">
                    <input type="hidden" name="margin_bottom" id="input_margin_bottom" value="10">
                    <input type="hidden" name="margin_left" id="input_margin_left" value="10">
                    <input type="hidden" name="font_scale" id="fontScaleInput" value="1">
HTML;
        $content = preg_replace('/<input type="hidden" name="font_scale" id="fontScaleInput" value="1">/s', $hiddenInputs, $content);

        // Add JS script
        $js = <<<HTML
        function updateMargins() {
            const mt = document.getElementById('marginTop').value || 0;
            const mr = document.getElementById('marginRight').value || 0;
            const mb = document.getElementById('marginBottom').value || 0;
            const ml = document.getElementById('marginLeft').value || 0;

            document.documentElement.style.setProperty('--margin-top', mt + 'mm');
            document.documentElement.style.setProperty('--margin-right', mr + 'mm');
            document.documentElement.style.setProperty('--margin-bottom', mb + 'mm');
            document.documentElement.style.setProperty('--margin-left', ml + 'mm');

            document.getElementById('input_margin_top').value = mt;
            document.getElementById('input_margin_right').value = mr;
            document.getElementById('input_margin_bottom').value = mb;
            document.getElementById('input_margin_left').value = ml;
        }

        function updateFontScale() {
HTML;
        $content = str_replace('function updateFontScale() {', $js, $content);
    }
    
    file_put_contents($file, $content);
}

