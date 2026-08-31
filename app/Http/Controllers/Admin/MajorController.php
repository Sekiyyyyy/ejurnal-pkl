<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use Illuminate\Http\Request;

class MajorController extends Controller
{
    public function index()
    {
        $majors = Major::orderBy('name', 'asc')->get();
        return view('admin.majors.index', compact('majors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:majors,code',
            'name' => 'required|string|max:255|unique:majors,name'
        ], [
            'code.unique' => 'Kode/Singkatan jurusan ini sudah terpakai.',
            'name.unique' => 'Jurusan ini sudah ada di dalam sistem.'
        ]);

        Major::create([
            'code' => strtoupper($request->code), // Otomatis mengubah jadi huruf besar (RPL, TKJ)
            'name' => $request->name
        ]);

        return back()->with('success', 'Jurusan baru berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        try {
            Major::findOrFail($id)->delete();
            return back()->with('success', 'Jurusan berhasil dihapus!');
        } catch (\Exception $e) {
            // Error ini akan muncul jika kita menghapus jurusan yang sudah ada siswanya (Restrict On Delete)
            return back()->withErrors(['error' => 'Gagal menghapus! Jurusan ini sedang digunakan oleh akun siswa.']);
        }
    }
}