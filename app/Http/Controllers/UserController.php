<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // 1. Menampilkan daftar pegawai
    public function index()
    {
        $users = User::latest()->get();
        return view('pages.users.index', compact('users'));
    }

    // 2. Menyimpan akun pegawai baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,kasir',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return back()->with('success', 'Akun pegawai berhasil ditambahkan!');
    }

    // 3. Mengubah data / reset password pegawai
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'role' => 'required|in:admin,kasir',
            'password' => 'nullable|string|min:8',
        ]);

        return DB::transaction(function () use ($id, $validated) {
            $admins = User::where('role', 'admin')->orderBy('id')->lockForUpdate()->get();
            $user = User::lockForUpdate()->findOrFail($id);

            if ($user->id === auth()->id() && $validated['role'] !== $user->role) {
                return back()->with('error', 'Peran akun yang sedang digunakan tidak dapat diubah.');
            }

            if ($user->role === 'admin' && $validated['role'] !== 'admin' && $admins->count() <= 1) {
                return back()->with('error', 'Admin terakhir tidak dapat diubah menjadi kasir.');
            }

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->role = $validated['role'];

            if (! empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            return back()->with('success', 'Data pegawai berhasil diperbarui!');
        });
    }

    // 4. Menghapus akun pegawai
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $admins = User::where('role', 'admin')->orderBy('id')->lockForUpdate()->get();
            $user = User::lockForUpdate()->findOrFail($id);

            if (auth()->id() === $user->id) {
                return back()->with('error', 'Gunakan pengelolaan profil untuk menghapus akun yang sedang digunakan.');
            }

            if ($user->role === 'admin' && $admins->count() <= 1) {
                return back()->with('error', 'Admin terakhir tidak dapat dihapus.');
            }

            $user->delete();

            return back()->with('success', 'Akun pegawai berhasil dihapus!');
        });
    }
}
