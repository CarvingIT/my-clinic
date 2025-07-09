<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Field;
use App\Models\Preset;

class PresetSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Fields
        $fields = [
            [
                'name' => 'nadi',
                'category' => 'checkup-info',
                'display_order' => 1,
                'extra_attributes' => json_encode([
                    'type' => 'textarea',
                    'required' => false,
                    'validation' => 'string'
                ])
            ],
            [
                'name' => 'lakshane',
                'category' => 'diagnosis',
                'display_order' => 1,
                'extra_attributes' => json_encode([
                    'type' => 'textarea',
                    'required' => false,
                    'validation' => 'string'
                ])
            ],

            [
                'name' => 'chikitsa',
                'category' => 'treatment',
                'display_order' => 1,
                'extra_attributes' => json_encode([
                    'type' => 'textarea',
                    'required' => false,
                    'validation' => 'string'
                ])
            ],

            [
                'name' => 'dravya',
                'category' => 'treatment',
                'display_order' => 2,
                'extra_attributes' => json_encode([
                    'type' => 'button',
                    'required' => false,
                    'validation' => 'string'
                ])
            ],
        ];

        foreach ($fields as $field) {
            Field::updateOrCreate(
                [
                    'name' => $field['name'],
                    'category' => $field['category'],
                ],
                [
                    'display_order' => $field['display_order'],
                    'extra_attributes' => $field['extra_attributes'],
                ]
            );
        }

        // Seed Presets
        $nadiField = Field::where('name', 'nadi')->first();
        $lakshaneField = Field::where('name', 'lakshane')->first();
        $chikitsaField = Field::where('name', 'chikitsa')->first();
        $dravyaField = Field::where('name', 'dravya')->first();

        // Nadi Presets
        $nadiPresets = [
            'वात', 'पित्त', 'कफ', 'सूक्ष्म', 'कठीण', 'साम', 'वेग', 'प्राण', 'व्यान', 'स्थूल',
            'अल्प स्थूल', 'अनियमित', 'तीक्ष्ण', 'वेगवती'
        ];
        foreach ($nadiPresets as $index => $preset) {
            Preset::updateOrCreate(
                [
                    'field_id' => $nadiField->id,
                    'button_text' => $preset,
                ],
                [
                    'preset_text' => $preset,
                    'display_order' => $index + 1,
                ]
            );
        }

        // Lakshane Presets
        $lakshanePresets = ['मल', 'मूत्र', 'जिव्हा', 'निद्रा', 'क्षुधा'];
        foreach ($lakshanePresets as $index => $preset) {
            Preset::updateOrCreate(
                [
                    'field_id' => $lakshaneField->id,
                    'button_text' => $preset,
                ],
                [
                    'preset_text' => $preset . ' - ',
                    'display_order' => $index + 1,
                ]
            );
        }

        // Chikitsa Presets
        $chikitsaPresets = [
            [
                'button_text' => 'ज्वर',
                'preset_text' => 'महासुदर्शन, वैदेही, बिभितक, यष्टी, तालीसादी',
                'display_order' => 1,
            ],
            [
                'button_text' => 'संधिशूल',
                'preset_text' => 'वरा, गुग्गुळ, विश्व, अश्वकपी, वत्स, गोक्षुर, गोदंती',
                'display_order' => 2,
            ],
            [
                'button_text' => 'अर्श',
                'preset_text' => 'हरीतकी, अमृता, सारिवा',
                'display_order' => 3,
            ],
            [
                'button_text' => 'ग्रहणी',
                'preset_text' => 'कुटज, मुस्ता, विश्व',
                'display_order' => 4,
            ],
            [
                'button_text' => 'चिकित्सा यथा पूर्व',
                'preset_text' => '', // Will be dynamically fetched in UI
                'display_order' => 5,
            ],
        ];
        foreach ($chikitsaPresets as $preset) {
            Preset::updateOrCreate(
                [
                    'field_id' => $chikitsaField->id,
                    'button_text' => $preset['button_text'],
                ],
                [
                    'preset_text' => $preset['preset_text'],
                    'display_order' => $preset['display_order'],
                ]
            );
        }

        // Dravya Presets
        $dravyaPresets = [
            'वरा', 'हरितकी', 'आमलकी', 'धात्री', 'बिभीतक', 'यष्टी', 'विडंग', 'त्रिकटु', 'लवंगादी', 'तालीसादी',
            'बला', 'अश्वगंधा', 'पुनर्नवा', 'गोक्षुर', 'मुंडी', 'वरुण', 'पाषाणभेद', 'उशीर', 'उदीच्या', 'अमृता',
            'पटोल', 'सारिवा', 'महासुदर्शन', 'षडंग', 'मुस्ता', 'कुटज', 'अर्जुन', 'निंब', 'मंजिष्ठा', 'खदिर',
            'विश्व', 'गुग्गुळु', 'वैदेही', 'कुटकी', 'शतवीर्यादी', 'क्षीरबला', 'अश्वकपी', 'शतदारी', 'पाददारी', 'सिंदुवार',
            'शताश्वपर्णी', 'सारस्वता', 'भृंगराज', 'वासा क.', 'प्ररोहा क.', 'शंख', 'शुक्ती', 'कपर्द', 'गोदंती', 'जटर मोहरा',
            'निशा', 'दार्वी', 'वत्सनाभ', 'श्रीखंड', 'ल. मा. व.', 'चंद्रप्रभा', 'कंपिल्लक', 'बिल्व', 'इंद्रयव', 'शतावरी'
        ];
        foreach ($dravyaPresets as $index => $preset) {
            Preset::updateOrCreate(
                [
                    'field_id' => $dravyaField->id,
                    'button_text' => $preset,
                ],
                [
                    'preset_text' => $preset,
                    'display_order' => $index + 1,
                ]
            );
        }
    }
}
