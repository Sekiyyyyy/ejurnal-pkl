<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kaprodi;
use App\Models\User;
use App\Models\Major;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class KaprodiController extends Controller
{
    public function index()
    {
        $kaprodis = Kaprodi::with(['user', 'major'])->get();
        $majors = Major::all();
        return view('admin.kaprodi.index', compact('kaprodis', 'majors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'major_id' => 'required|exists:majors,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_KAPRODI,
        ]);

        Kaprodi::create([
            'user_id' => $user->id,
            'major_id' => $request->major_id,
            'name' => $request->name,
            'nip' => $request->nip,
        ]);

        return redirect()->route('admin.kaprodi.index')->with('success', 'Akun Kaprodi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kaprodi = Kaprodi::findOrFail($id);
        $majors = Major::all();
        // Since we are showing a modal usually, we might just return JSON or have a separate view. 
        // Let's assume we'll just handle it directly or via modal. For simplicity, we can do edit view or just pass it in index if we use modal.
        // I will use a separate edit view to be consistent with majors or return data.
        return view('admin.kaprodi.edit', compact('kaprodi', 'majors'));
    }

    public function update(Request $request, $id)
    {
        $kaprodi = Kaprodi::findOrFail($id);
        $user = $kaprodi->user;

        $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'major_id' => 'required|exists:majors,id',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6']);
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        $kaprodi->update([
            'major_id' => $request->major_id,
            'name' => $request->name,
            'nip' => $request->nip,
        ]);

        return redirect()->route('admin.kaprodi.index')->with('success', 'Akun Kaprodi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kaprodi = Kaprodi::findOrFail($id);
        $kaprodi->user->delete(); // This will cascade delete kaprodi
        return redirect()->route('admin.kaprodi.index')->with('success', 'Akun Kaprodi berhasil dihapus.');
    }
}
