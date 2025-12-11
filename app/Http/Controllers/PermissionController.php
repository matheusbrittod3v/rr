<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the permissions.
     */
    public function index()
    {
        $permissions = Permission::all();
        return view('permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
            'description' => 'nullable|string',
        ]);

        Permission::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id,
            'description' => 'nullable|string',
        ]);

        $permission->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
    }
}