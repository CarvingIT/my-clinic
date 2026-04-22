<?php
$pdfSource = file_get_contents('resources/views/prescriptions/pdf-download.blade.php');
$genSource = file_get_contents('resources/views/prescriptions/generate.blade.php');

// Extract styles from pdf-download
preg_match('/<style>(.*?)<\/style>/s', $pdfSource, $matches);
$pdfStyle = $matches[1];

// Convert {{ 12 * $fontScale }}px to calc(12px * var(--font-scale))
$pdfStyle = preg_replace('/\{\{\s*([0-9.]+)\s*\*\s*\$fontScale\s*\}\}px/', 'calc($1px * var(--font-scale))', $pdfStyle);

// Keep root variables in generate
$rootVars = <<<'CSS'
        :root {
            --font-scale: 1;
            --margin-top: 10mm;
            --margin-right: 10mm;
            --margin-bottom: 10mm;
            --margin-left: 10mm;
        }
        @page {
            size: A4;
            margin: var(--margin-top) var(--margin-right) var(--margin-bottom) var(--margin-left);
        }
        @media print {
            body {
                background: white;
            }
            .print-toolbar {
                display: none !important;
            }
            .prescription-wrapper {
                box-shadow: none !important;
                border: none !important;
            }
        }
        .letterhead-space {
            height: 50mm; /* Add space reserved for letterheads */
        }
CSS;

$newStyle = "<style>\n" . $rootVars . $pdfStyle . "\n    </style>";

// Replace <style> block in generate
$genSource = preg_replace('/<style>(.*?)<\/style>/s', $newStyle, $genSource);

// Extract HTML from pdf-download inside prescription-container
preg_match('/<div class="prescription-container">(.*?)<\/div>\s*<\/body>/s', $pdfSource, $htmlMatches);
$pdfHtml = $htmlMatches[1];

// Wrap it as the replacement for inside prescriptionVisual in generate
$replacementHtml = '        <div id="prescriptionVisual" style="padding: var(--margin-top) var(--margin-right) var(--margin-bottom) var(--margin-left); font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;" class="prescription-wrapper bg-white rounded-lg shadow-lg overflow-hidden border border-gray-300">
            <!-- Letterhead Space -->
            <div class="letterhead-space"></div>

            <div class="prescription-container" style="width: 100%; margin: 0 auto;">' . "\n" .
            $pdfHtml . "\n" .
            '            </div>
        </div>';

// Replace everything from <div id="prescriptionVisual" to the end of it (before </div></div> -> <script>)
$genSource = preg_replace('/<div id="prescriptionVisual".*?<\/div>\s*<\/div>\s*<\/div>\s*<script>/s', $replacementHtml . "\n    </div>\n\n    <script>", $genSource);

file_put_contents('resources/views/prescriptions/generate.blade.php', $genSource);
echo "Patched generate.blade.php\n";
