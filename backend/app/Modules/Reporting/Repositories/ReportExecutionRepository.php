<?php

namespace App\Modules\Reporting\Repositories;

use App\Modules\Reporting\Enums\ReportExecutionStatus;
use App\Modules\Reporting\Models\ReportExecution;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * ReportExecutionRepository
 * 
 * Repository for managing report execution data access.
 */
class ReportExecutionRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    protected function model(): string
    {
        return ReportExecution::class;
    }

    /**
     * Get executions by report ID
     *
     * @param int $reportId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByReport(int $reportId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()
            ->where('report_id', $reportId)
            ->with(['executedBy'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get executions by user ID
     *
     * @param int $userId
     * @return Collection
     */
    public function getByUser(int $userId): Collection
    {
        return $this->query()
            ->where('executed_by_id', $userId)
            ->with(['report'])
            ->latest()
            ->get();
    }

    /**
     * Get executions by status
     *
     * @param ReportExecutionStatus $status
     * @return Collection
     */
    public function getByStatus(ReportExecutionStatus $status): Collection
    {
        return $this->query()
            ->where('status', $status)
            ->with(['report', 'executedBy'])
            ->latest()
            ->get();
    }

    /**
     * Get running executions
     *
     * @return Collection
     */
    public function getRunning(): Collection
    {
        return $this->getByStatus(ReportExecutionStatus::RUNNING);
    }

    /**
     * Get failed executions
     *
     * @return Collection
     */
    public function getFailed(): Collection
    {
        return $this->getByStatus(ReportExecutionStatus::FAILED);
    }

    /**
     * Get completed executions
     *
     * @return Collection
     */
    public function getCompleted(): Collection
    {
        return $this->getByStatus(ReportExecutionStatus::COMPLETED);
    }

    /**
     * Get recent executions
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->query()
            ->with(['report', 'executedBy'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get average execution time for a report
     *
     * @param int $reportId
     * @return float
     */
    public function getAverageExecutionTime(int $reportId): float
    {
        return $this->query()
            ->where('report_id', $reportId)
            ->where('status', ReportExecutionStatus::COMPLETED)
            ->avg('execution_time') ?? 0.0;
    }

    /**
     * Get execution statistics for a report
     *
     * @param int $reportId
     * @return array
     */
    public function getStatistics(int $reportId): array
    {
        $executions = $this->query()
            ->where('report_id', $reportId)
            ->get();

        return [
            'total' => $executions->count(),
            'completed' => $executions->where('status', ReportExecutionStatus::COMPLETED)->count(),
            'failed' => $executions->where('status', ReportExecutionStatus::FAILED)->count(),
            'running' => $executions->where('status', ReportExecutionStatus::RUNNING)->count(),
            'average_time' => $this->getAverageExecutionTime($reportId),
            'last_execution' => $executions->sortByDesc('created_at')->first(),
        ];
    }

    /**
     * Clean up old executions
     *
     * @param int $daysOld
     * @return int Number of deleted records
     */
    public function cleanupOldExecutions(int $daysOld = 90): int
    {
        return $this->query()
            ->where('created_at', '<', now()->subDays($daysOld))
            ->delete();
    }
}
