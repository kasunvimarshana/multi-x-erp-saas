<?php

namespace App\Modules\Finance\Repositories;

use App\Modules\Finance\Models\CostCenter;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * CostCenterRepository
 * 
 * Repository for managing cost center data access.
 */
class CostCenterRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    protected function model(): string
    {
        return CostCenter::class;
    }

    /**
     * Get active cost centers
     *
     * @return Collection
     */
    public function getActive(): Collection
    {
        return $this->query()->active()->get();
    }

    /**
     * Find cost center by code
     *
     * @param string $code
     * @return CostCenter|null
     */
    public function findByCode(string $code): ?CostCenter
    {
        return $this->query()->where('code', $code)->first();
    }

    /**
     * Search cost centers by keyword
     *
     * @param string $keyword
     * @return Collection
     */
    public function search(string $keyword): Collection
    {
        return $this->query()
            ->where(function ($query) use ($keyword) {
                $query->where('code', 'like', "%{$keyword}%")
                    ->orWhere('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            })
            ->get();
    }

    /**
     * Get cost centers with journal entry line count
     *
     * @return Collection
     */
    public function withUsageCount(): Collection
    {
        return $this->query()
            ->withCount('journalEntryLines')
            ->get();
    }
}
