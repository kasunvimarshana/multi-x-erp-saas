<?php

namespace App\Modules\Reporting\Repositories;

use App\Modules\Reporting\Models\ScheduledReport;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * ScheduledReportRepository
 * 
 * Repository for managing scheduled report data access.
 */
class ScheduledReportRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    protected function model(): string
    {
        return ScheduledReport::class;
    }

    /**
     * Get active scheduled reports
     *
     * @return Collection
     */
    public function getActive(): Collection
    {
        return $this->query()
            ->where('is_active', true)
            ->with(['report'])
            ->get();
    }

    /**
     * Get scheduled reports by report ID
     *
     * @param int $reportId
     * @return Collection
     */
    public function getByReport(int $reportId): Collection
    {
        return $this->query()
            ->where('report_id', $reportId)
            ->get();
    }

    /**
     * Get due scheduled reports
     *
     * @return Collection
     */
    public function getDueReports(): Collection
    {
        return $this->query()
            ->where('is_active', true)
            ->where('next_run_at', '<=', now())
            ->with(['report'])
            ->get();
    }

    /**
     * Update last run timestamp
     *
     * @param int $id
     * @return bool
     */
    public function updateLastRun(int $id): bool
    {
        return $this->update($id, [
            'last_run_at' => now(),
        ]);
    }

    /**
     * Update next run timestamp
     *
     * @param int $id
     * @param \DateTime $nextRunAt
     * @return bool
     */
    public function updateNextRun(int $id, \DateTime $nextRunAt): bool
    {
        return $this->update($id, [
            'next_run_at' => $nextRunAt,
        ]);
    }

    /**
     * Activate scheduled report
     *
     * @param int $id
     * @return bool
     */
    public function activate(int $id): bool
    {
        return $this->update($id, [
            'is_active' => true,
        ]);
    }

    /**
     * Deactivate scheduled report
     *
     * @param int $id
     * @return bool
     */
    public function deactivate(int $id): bool
    {
        return $this->update($id, [
            'is_active' => false,
        ]);
    }

    /**
     * Get scheduled reports by recipient email
     *
     * @param string $email
     * @return Collection
     */
    public function getByRecipient(string $email): Collection
    {
        return $this->query()
            ->whereJsonContains('recipients', $email)
            ->with(['report'])
            ->get();
    }

    /**
     * Get upcoming scheduled reports
     *
     * @param int $hours Number of hours to look ahead
     * @return Collection
     */
    public function getUpcoming(int $hours = 24): Collection
    {
        return $this->query()
            ->where('is_active', true)
            ->whereBetween('next_run_at', [now(), now()->addHours($hours)])
            ->with(['report'])
            ->orderBy('next_run_at')
            ->get();
    }
}
