<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Routing\Controller;
use CarvingIT\LaravelUserRoles\App\Models\Role;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    // {
    //     $users = User::all()->load('roles');
    //     return view('users.index', compact('users'));
    // }
    {
        $users = User::all();
        foreach ($users as $user) {
            // $user->setAttribute('roles', $user->roles()->get()); // Manually attach roles
            $user->setAttribute('roles', $user->roles()); // Use the roles relationship to get roles
        }
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(10),
        ]);

        return redirect()->route('users.index')->with('success', 'User Created Successfully.');
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255',  'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'],
        ]);
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        // Update roles
        $user->unassignRoles($user->roles()->pluck('name')->toArray());
        if ($request->roles) {
            $user->assignRoles($request->roles);
        }

        return redirect()->route('users.index')->with('success', 'User Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return Redirect::route('users.index')->with('success', 'User Deleted Successfully');
    }
}
