<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Services\ValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValidationController extends Controller
{
    protected $validationService;

    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function index(Request $request)
    {
        $kaprodi = Auth::user()->kaprodi;
        if (!$kaprodi) {
            abort(403, 'Akses ditolak. Anda bukan Kaprodi.');
        }

        $majorId = $kaprodi->major_id;
        $selectedType = $request->type ?? 'all';
        $selectedStatus = $request->status ?? 'all';
        $search = $request->search;

        $result = $this->validationService->getValidations(
            $majorId,
            $selectedType,
            $selectedStatus,
            $search,
            12
        );

        $validations = $result['paginated'];
        $stats = $result['stats'];

        return view('kaprodi.validations.index', compact(
            'validations',
            'stats',
            'kaprodi',
            'selectedType',
            'selectedStatus',
            'search'
        ));
    }

    public function destroy($type, $id)
    {
        $kaprodi = Auth::user()->kaprodi;
        if (!$kaprodi) {
            abort(403, 'Akses ditolak.');
        }

        $message = $this->validationService->deleteValidation($type, $id, $kaprodi->major_id);
        return back()->with('success', $message);
    }
}
