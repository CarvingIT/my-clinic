<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientGroup;
use Illuminate\Http\Request;

class PatientGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of patient groups.
     */
    public function index(Request $request)
    {
        return redirect()->route('payments.index', ['tab' => 'groups']);
    }

    /**
     * Show the form for creating a new patient group.
     */
    public function create()
    {
        return view('groups.create');
    }

    /**
     * Store a newly created patient group in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'patient_ids' => ['nullable', 'array'],
            'patient_ids.*' => ['exists:patients,id'],
        ]);

        $group = PatientGroup::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (!empty($validated['patient_ids'])) {
            // Assign these patients to this group
            Patient::whereIn('id', $validated['patient_ids'])
                ->update(['patient_group_id' => $group->id]);
        }

        return redirect()->route('payments.index', ['tab' => 'groups'])->with('success', 'Group created successfully.');
    }

    /**
     * Show the form for editing the specified patient group.
     */
    public function edit(PatientGroup $group)
    {
        $group->load('members:id,name,patient_id,mobile_phone');
        return view('groups.edit', compact('group'));
    }

    /**
     * Update the specified patient group in storage.
     */
    public function update(Request $request, PatientGroup $group)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'patient_ids' => ['nullable', 'array'],
            'patient_ids.*' => ['exists:patients,id'],
        ]);

        $group->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        // Get selected patient IDs (or empty array if none)
        $newPatientIds = $validated['patient_ids'] ?? [];

        // 1. Remove patients that are no longer in this group
        Patient::where('patient_group_id', $group->id)
            ->whereNotIn('id', $newPatientIds)
            ->update(['patient_group_id' => null]);

        // 2. Add newly selected patients to this group
        if (!empty($newPatientIds)) {
            Patient::whereIn('id', $newPatientIds)
                ->update(['patient_group_id' => $group->id]);
        }

        return redirect()->route('payments.index', ['tab' => 'groups'])->with('success', 'Group updated successfully.');
    }

    /**
     * Remove the specified patient group from storage.
     */
    public function destroy(PatientGroup $group)
    {
        // Nullify associations (handled by foreign key nullOnDelete in DB, but update Laravel side just to be safe)
        Patient::where('patient_group_id', $group->id)->update(['patient_group_id' => null]);
        
        $group->delete();

        return redirect()->route('payments.index', ['tab' => 'groups'])->with('success', 'Group deleted successfully.');
    }

    /**
     * Search groups for payment page.
     */
    public function searchGroups(Request $request)
    {
        $term = trim((string) $request->input('q', ''));

        if ($term === '') {
            return response()->json([]);
        }

        $groups = PatientGroup::with('members:id,name,patient_id,mobile_phone')
            ->where('name', 'like', "%{$term}%")
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json($groups);
    }
}
