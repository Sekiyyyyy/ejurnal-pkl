<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::latest()->get();
        return view('admin.templates.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|mimes:docx|max:5120', // Maksimal 5MB, khusus .docx
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
        $path = $file->storeAs('templates', $filename, 'public');

        // Jika ini template pertama yang diupload, otomatis aktifkan
        $isFirst = Template::count() === 0;

        Template::create([
            'name' => $request->name,
            'file_path' => $path,
            'is_active' => $isFirst,
        ]);

        return back()->with('success', 'Template baru berhasil diunggah!');
    }

    public function activate($id)
    {
        // Nonaktifkan semua template dulu
        Template::query()->update(['is_active' => false]);

        // Aktifkan yang dipilih
        $template = Template::findOrFail($id);
        $template->update(['is_active' => true]);

        return back()->with('success', 'Template "' . $template->name . '" sekarang aktif digunakan!');
    }

    public function destroy($id)
    {
        $template = Template::findOrFail($id);
        
        if ($template->is_active) {
            return back()->withErrors(['error' => 'Tidak bisa menghapus template yang sedang aktif! Aktifkan template lain terlebih dahulu.']);
        }

        // Hapus file fisiknya
        if (Storage::disk('public')->exists($template->file_path)) {
            Storage::disk('public')->delete($template->file_path);
        }

        $template->delete();

        return back()->with('success', 'Template berhasil dihapus.');
    }
}