<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::with('major')->latest()->get();
        $majors = Major::orderBy('name')->get();
        return view('admin.templates.index', compact('templates', 'majors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'major_id' => 'required|exists:majors,id',
            'name' => 'required|string|max:255',
            'file' => 'required|mimes:docx|max:5120', // Maksimal 5MB, khusus .docx
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
        $path = $file->storeAs('templates', $filename, 'public');

        // Cek apakah sudah ada template aktif untuk jurusan ini
        $isFirstForMajor = !Template::where('major_id', $request->major_id)->exists();

        Template::create([
            'major_id' => $request->major_id,
            'name' => $request->name,
            'file_path' => $path,
            'is_active' => $isFirstForMajor,
        ]);

        return back()->with('success', 'Template baru berhasil diunggah untuk jurusan terkait!');
    }

    public function activate($id)
    {
        $template = Template::findOrFail($id);

        // Nonaktifkan semua template lain KHUSUS di jurusan yang sama
        Template::where('major_id', $template->major_id)->update(['is_active' => false]);

        // Aktifkan yang dipilih
        $template->update(['is_active' => true]);

        return back()->with('success', 'Template "' . $template->name . '" sekarang aktif digunakan untuk jurusan ini!');
    }

    public function destroy($id)
    {
        $template = Template::findOrFail($id);
        
        if ($template->is_active) {
            return back()->withErrors(['error' => 'Tidak bisa menghapus template yang sedang aktif! Aktifkan template lain terlebih dahulu.']);
        }

        if (Storage::disk('public')->exists($template->file_path)) {
            Storage::disk('public')->delete($template->file_path);
        }

        $template->delete();

        return back()->with('success', 'Template berhasil dihapus.');
    }
}