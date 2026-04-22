<?php
$file = 'resources/views/prescriptions/generate.blade.php';
$content = file_get_contents($file);

// Extract the top part until <div class="max-w-7xl
$topParts = explode('<div class="print-toolbar', $content);
$part1 = $topParts[0];

$bottomParts = explode('</form>', $topParts[1]);
$part2 = $bottomParts[1]; // from </div> </div> <!-- Prescription Document -->

// Original hiddens grabber
preg_match('/<form action="([^"]+)" method="POST" style="display: inline;">(.*?)<button type="submit"/s', $topParts[1], $formMatches);
$formAction = $formMatches[1] ?? "{{ route('followups.prescription.download', ['followup' => \$followup->id]) }}";
$formInnerHtml = $formMatches[2] ?? '';

$toolbarReplacement = <<<HTML
<div class="print-toolbar sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm">
        <div class="w-full px-4 py-3 flex items-center justify-between gap-4">
            <!-- Left side Title -->
            <div class="shrink-0">
                <h1 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-prescription text-blue-600 mr-2"></i> Prescription Preview
                </h1>
                <p class="text-xs text-gray-500 font-medium mt-0.5">{{ \$patient->name }} &bull; {{ now()->format('d M Y') }}</p>
            </div>
            
            <!-- Right side Controls -->
            <div class="flex gap-2 items-center flex-nowrap shrink-0 overflow-x-auto pb-1 md:pb-0">
                
                <!-- Margins Control Group -->
                <div class="flex items-center bg-gray-50 rounded-lg p-1 border border-gray-200 shadow-inner">
                    <div class="px-2 border-r border-gray-200">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Margin</span>
                    </div>
                    <div class="flex items-center px-1" title="Top Margin">
                        <i class="fas fa-arrow-up text-gray-400 text-[10px] ml-1"></i>
                        <input type="number" id="marginTop" value="10" min="0" max="100" class="w-10 h-7 bg-transparent border-none text-center text-sm font-semibold text-gray-700 focus:ring-0 p-0" onchange="updateMargins()">
                    </div>
                    <div class="flex items-center px-1 border-l border-gray-200" title="Right Margin">
                        <i class="fas fa-arrow-right text-gray-400 text-[10px] ml-1"></i>
                        <input type="number" id="marginRight" value="10" min="0" max="100" class="w-10 h-7 bg-transparent border-none text-center text-sm font-semibold text-gray-700 focus:ring-0 p-0" onchange="updateMargins()">
                    </div>
                    <div class="flex items-center px-1 border-l border-gray-200" title="Bottom Margin">
                        <i class="fas fa-arrow-down text-gray-400 text-[10px] ml-1"></i>
                        <input type="number" id="marginBottom" value="10" min="0" max="100" class="w-10 h-7 bg-transparent border-none text-center text-sm font-semibold text-gray-700 focus:ring-0 p-0" onchange="updateMargins()">
                    </div>
                    <div class="flex items-center px-1 border-l border-gray-200" title="Left Margin">
                        <i class="fas fa-arrow-left text-gray-400 text-[10px] ml-1"></i>
                        <input type="number" id="marginLeft" value="10" min="0" max="100" class="w-10 h-7 bg-transparent border-none text-center text-sm font-semibold text-gray-700 focus:ring-0 p-0" onchange="updateMargins()">
                    </div>
                </div>

                <!-- Font Control Group -->
                <div class="flex items-center bg-gray-50 rounded-lg p-1 border border-gray-200 shadow-inner">
                    <div class="px-2 border-r border-gray-200">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Font</span>
                    </div>
                    <button onclick="changeFontSize(-0.1)" type="button" class="w-7 h-7 flex items-center justify-center text-gray-600 hover:text-gray-900 hover:bg-gray-200 rounded transition" title="Decrease Font">
                        <i class="fas fa-minus text-[10px]"></i>
                    </button>
                    <span class="w-12 text-center text-sm font-bold text-gray-700" id="fontSizeDisplay">100%</span>
                    <button onclick="changeFontSize(0.1)" type="button" class="w-7 h-7 flex items-center justify-center text-gray-600 hover:text-gray-900 hover:bg-gray-200 rounded transition mr-1" title="Increase Font">
                        <i class="fas fa-plus text-[10px]"></i>
                    </button>
                    <div class="border-l border-gray-200 pl-1">
                        <button onclick="resetFontSize()" type="button" class="px-2 py-1 text-[10px] font-bold text-blue-600 hover:bg-blue-100 rounded transition">RESET</button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2 pl-2 border-l border-gray-300">
                    <button onclick="goBack()" class="h-9 px-4 bg-white border border-gray-300 hover:bg-gray-50 hover:text-blue-600 text-gray-700 rounded-lg text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    
                    <button onclick="window.print()" class="h-9 px-4 bg-gray-800 hover:bg-black text-white rounded-lg text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                        <i class="fas fa-print"></i> Print
                    </button>
                    
                    <button type="button" onclick="document.getElementById('downloadPdfForm').submit()" class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                        <i class="fas fa-download"></i> PDF
                    </button>
                </div>
                
                <form id="downloadPdfForm" action="{$formAction}" method="POST" style="display: none;">
                    {$formInnerHtml}
                </form>
            </div>
        </div>
    </div>
HTML;

file_put_contents($file, $part1 . $toolbarReplacement . $part2);
?>
