<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DataAdminController extends Controller
{
    // Show list of users (Read)
    public function index()
    {
        $users = User::all();  // Fetch all users from the database
        return view('pages.data-admin.index', compact('users'));
    }

    // Show the form for creating a new user (Create)
    public function create()
    {
        return view('pages.data-admin.create');
    }

    // Store the newly created user in the database (Store)
    public function store(Request $request)
    {
        // Validate the data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Create the new user
        User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        // Redirect with success message
        return redirect()->route('data-admin.index')->with('success', 'User created successfully!');
    }

    // Show the form for editing the specified user (Edit)
    public function edit($id)
    {
        $user = User::findOrFail($id);  // Find the user by ID
        return view('pages.data-admin.edit', compact('user'));
    }

    // Update the specified user in the database (Update)
    public function update(Request $request, $id)
    {
        // Validate the data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,  // Allow the current email
            'password' => 'nullable|string|min:6|confirmed',  // Optional password field
        ]);

        $user = User::findOrFail($id);  // Find the user by ID

        // Update the user
        $user->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => $request->filled('password') ? bcrypt($validatedData['password']) : $user->password,  // Update password if provided
        ]);

        // Redirect with success message
        return redirect()->route('data-admin.index')->with('success', 'User updated successfully!');
    }

    // Delete the specified user (Delete)
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('data-admin.index')->with('success', 'User deleted successfully!');
    }
}