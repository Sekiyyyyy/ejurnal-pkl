<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardDispatcherController extends Controller
{
    /**
     * Dispatch user to their respective role-based dashboard.
     * Menggantikan route closure agar Laravel dapat menjalankan route:cache dengan optimal.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'super_admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'kaprodi') {
            return redirect()->route('kaprodi.dashboard');
        }

        // Siswa
        return app()->make(JournalController::class)->dashboard();
    }
}
