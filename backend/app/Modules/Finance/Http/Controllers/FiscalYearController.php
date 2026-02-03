<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\Finance\Events\FiscalYearClosed;
use App\Modules\Finance\Repositories\FiscalYearRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Fiscal Year API Controller
 * 
 * Handles HTTP requests for fiscal year management.
 */
class FiscalYearController extends BaseController
{
    public function __construct(
        protected FiscalYearRepository $fiscalYearRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $fiscalYears = $this->fiscalYearRepository->paginate($perPage);
        
        return $this->successResponse($fiscalYears, 'Fiscal years retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
        
        $validated['tenant_id'] = Auth::user()->tenant_id;
        $validated['is_closed'] = false;
        
        $fiscalYear = $this->fiscalYearRepository->create($validated);
        
        return $this->createdResponse($fiscalYear, 'Fiscal year created successfully');
    }

    public function show(int $id): JsonResponse
    {
        $fiscalYear = $this->fiscalYearRepository->findOrFail($id);
        
        return $this->successResponse($fiscalYear, 'Fiscal year retrieved successfully');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $fiscalYear = $this->fiscalYearRepository->findOrFail($id);
        
        if ($fiscalYear->is_closed) {
            return $this->errorResponse('Cannot update closed fiscal year', null, 400);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
        
        $this->fiscalYearRepository->update($id, $validated);
        $fiscalYear->refresh();
        
        return $this->successResponse($fiscalYear, 'Fiscal year updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $fiscalYear = $this->fiscalYearRepository->findOrFail($id);
        
        if ($fiscalYear->is_closed) {
            return $this->errorResponse('Cannot delete closed fiscal year', null, 400);
        }
        
        $this->fiscalYearRepository->delete($id);
        
        return $this->successResponse(null, 'Fiscal year deleted successfully');
    }

    public function close(int $id): JsonResponse
    {
        $fiscalYear = $this->fiscalYearRepository->findOrFail($id);
        
        if ($fiscalYear->is_closed) {
            return $this->errorResponse('Fiscal year is already closed', null, 400);
        }
        
        $this->fiscalYearRepository->update($id, ['is_closed' => true]);
        $fiscalYear->refresh();
        
        event(new FiscalYearClosed($fiscalYear));
        
        return $this->successResponse($fiscalYear, 'Fiscal year closed successfully');
    }

    public function open(): JsonResponse
    {
        $fiscalYears = $this->fiscalYearRepository->getOpenFiscalYears();
        
        return $this->successResponse($fiscalYears, 'Open fiscal years retrieved successfully');
    }

    public function closed(): JsonResponse
    {
        $fiscalYears = $this->fiscalYearRepository->getClosedFiscalYears();
        
        return $this->successResponse($fiscalYears, 'Closed fiscal years retrieved successfully');
    }

    public function current(): JsonResponse
    {
        $fiscalYear = $this->fiscalYearRepository->getCurrentFiscalYear();
        
        if (!$fiscalYear) {
            return $this->notFoundResponse('No current fiscal year found');
        }
        
        return $this->successResponse($fiscalYear, 'Current fiscal year retrieved successfully');
    }
}
