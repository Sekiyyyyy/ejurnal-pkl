<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Services\ValidationService;
use Illuminate\Http\Request;

class ValidationController extends Controller
{
    protected $validationService;

    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function index(Request $request)
    {
        $majors = Major::orderBy('name')->get();
        $selectedMajorId = $request->major_id;
        $selectedType = $request->type ?? 'all';
        $selectedStatus = $request->status ?? 'all';
        $search = $request->search;

        $result = $this->validationService->getValidations(
            $selectedMajorId,
            $selectedType,
            $selectedStatus,
            $search,
            12
        );

        $validations = $result['paginated'];
        $stats = $result['stats'];

        return view('admin.validations.index', compact(
            'validations',
            'stats',
            'majors',
            'selectedMajorId',
            'selectedType',
            'selectedStatus',
            'search'
        ));
    }

    public function destroy($type, $id)
    {
        $message = $this->validationService->deleteValidation($type, $id);
        return back()->with('success', $message);
    }
}
