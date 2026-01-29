<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of templates
     */
    public function index()
    {
        $templates = Template::orderBy('name')->get();
        return view('admin.templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new template
     */
    public function create()
    {
        return view('admin.templates.form');
    }

    /**
     * Store a newly created template
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:templates,slug',
            'type' => 'required|string|max:50',
            'content' => 'required|string',
            'placeholders' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Convert comma-separated placeholders to array
        $validated['placeholders'] = $this->parsePlaceholders($request->placeholders);
        $validated['is_active'] = $request->has('is_active');

        $template = Template::create($validated);

        return redirect()->route('admin.templates.edit', $template)
            ->with('success', 'Template created successfully.');
    }

    /**
     * Show the form for editing a template
     */
    public function edit(Template $template)
    {
        return view('admin.templates.form', compact('template'));
    }

    /**
     * Update the specified template
     */
    public function update(Request $request, Template $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:templates,slug,' . $template->id,
            'type' => 'required|string|max:50',
            'content' => 'required|string',
            'placeholders' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Convert comma-separated placeholders to array
        $validated['placeholders'] = $this->parsePlaceholders($request->placeholders);
        $validated['is_active'] = $request->has('is_active');

        $template->update($validated);

        return redirect()->route('admin.templates.edit', $template)
            ->with('success', 'Template saved successfully.');
    }

    /**
     * Remove the specified template
     */
    public function destroy(Template $template)
    {
        $template->delete();

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template deleted successfully.');
    }

    /**
     * Toggle template active status
     */
    public function toggleStatus(Template $template)
    {
        $template->update(['is_active' => !$template->is_active]);

        return redirect()->back()
            ->with('success', 'Template status updated successfully.');
    }

    /**
     * Parse comma-separated placeholders into array
     */
    private function parsePlaceholders(?string $placeholders): array
    {
        if (empty($placeholders)) {
            return [];
        }

        return array_map('trim', explode(',', $placeholders));
    }
}
