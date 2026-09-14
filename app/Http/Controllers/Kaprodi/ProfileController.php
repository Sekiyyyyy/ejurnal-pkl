<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $kaprodi = Auth::user()->kaprodi;
        return view('kaprodi.profile', compact('kaprodi'));
    }

    public function update(Request $request)
    {
        $kaprodi = Auth::user()->kaprodi;

        $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'signature_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'signature_base64' => 'nullable|string',
        ]);

        $data = [
            'name' => $request->name,
            'nip' => $request->nip,
        ];

        if ($request->hasFile('signature_file')) {
            if ($kaprodi->signature && Storage::disk('public')->exists($kaprodi->signature)) {
                Storage::disk('public')->delete($kaprodi->signature);
            }
            $filePath = $request->file('signature_file')->store('signatures', 'public');
            $data['signature'] = $filePath;
        } elseif ($request->filled('signature_base64')) {
            if ($kaprodi->signature && Storage::disk('public')->exists($kaprodi->signature)) {
                Storage::disk('public')->delete($kaprodi->signature);
            }
            
            $image_parts = explode(";base64,", $request->signature_base64);
            $image_base64 = base64_decode($image_parts[1]);
            
            $fileName = uniqid() . '.png';
            $filePath = 'signatures/' . $fileName;
            Storage::disk('public')->put($filePath, $image_base64);
            
            $data['signature'] = $filePath;
        }

        $kaprodi->update($data);
        Auth::user()->update(['name' => $request->name]); // Update user name as well

        return back()->with('success', 'Profil dan tanda tangan berhasil diperbarui.');
    }
}
