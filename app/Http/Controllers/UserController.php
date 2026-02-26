<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // =========================
    // LIST + SEARCH + PAGINATION
    // =========================
    public function index(Request $request)
    {
        $search  = $request->search;
        $perPage = $request->per_page ?? 10;

        $users = User::with('roles')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        return view('users.index', compact('users', 'search', 'perPage'));
    }

    // =========================
    // CREATE
    // =========================
    public function create()
    {
        return view('users.create');
    }

    // =========================
    // STORE (FIX ROLE SPATIE)
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,superuser,user',
        ]);

        // Buat user
        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role'     => $request->role, // optional legacy column
        ]);

        // 🔥 WAJIB: assign role ke Spatie
        $user->assignRole($request->role);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    // =========================
    // EDIT
    // =========================
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    // =========================
    // UPDATE + SYNC ROLE
    // =========================
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'role'     => 'required|in:admin,superuser,user',
            'password' => 'nullable|min:6',
        ]);

        $user->name     = $request->name;
        $user->username = $request->username;
        $user->role     = $request->role; // optional legacy column

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // 🔥 sinkronkan role spatie
        $user->syncRoles([$request->role]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui');
    }

    // =========================
    // RESET PASSWORD
    // =========================
    public function resetPassword(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tidak bisa reset password sendiri');
        }

        $user->update([
            'password' => Hash::make('password123'),
        ]);

        return back()->with('success', 'Password direset ke: password123');
    }

    // =========================
    // DELETE USER
    // =========================
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri');
        }

        // hapus role relation juga (rapih)
        $user->syncRoles([]);

        $user->delete();

        return back()->with('success', 'User berhasil dihapus');
    }
}