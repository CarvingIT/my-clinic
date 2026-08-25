<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:branches,name'],
        ]);

        Branch::create([
            'name' => trim($request->name),
        ]);

        return redirect()->to(route('users.index') . '#branches')->with('success', 'Branch created successfully.');
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:branches,name,' . $branch->id],
        ]);

        $branch->update([
            'name' => trim($request->name),
        ]);

        return redirect()->to(route('users.index') . '#branches')->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return redirect()->to(route('users.index') . '#branches')->with('success', 'Branch deleted successfully.');
    }
}
