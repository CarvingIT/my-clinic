<?php

namespace App\Http\Controllers;

use App\Models\Preset;
use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $field_id = $request->query('field_id');
        $presets = Preset::where('field_id', $field_id)
            ->orderBy('display_order')
            ->get();

        return response()->json($presets);
    }

    public function store(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'button_text' => 'required|string|max:255',
            'preset_text' => 'nullable|string',
            'display_order' => 'integer|min:0',
        ]);

        $preset = Preset::create([
            'field_id' => $request->field_id,
            'button_text' => $request->button_text,
            'preset_text' => $request->preset_text,
            'display_order' => $request->display_order ?? (Preset::where('field_id', $request->field_id)->max('display_order') + 1),
        ]);

        return response()->json($preset, 201);
    }

    public function update(Request $request, Preset $preset)
    {
        $request->validate([
            'button_text' => 'required|string|max:255',
            'preset_text' => 'nullable|string',
            'display_order' => 'integer|min:0',
        ]);

        $preset->update([
            'button_text' => $request->button_text,
            'preset_text' => $request->preset_text,
            'display_order' => $request->display_order,
        ]);

        return response()->json($preset);
    }

    public function destroy(Preset $preset)
    {
        $preset->delete();
        return response()->json(['message' => 'Preset deleted']);
    }
}
