<?php

namespace App\Modules\Procurement\Repositories;

use App\Modules\Procurement\Enums\GoodsReceiptStatus;
use App\Modules\Procurement\Models\GoodsReceiptNote;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * GoodsReceiptNoteRepository
 * 
 * Repository for managing goods receipt note data access.
 */
class GoodsReceiptNoteRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    protected function model(): string
    {
        return GoodsReceiptNote::class;
    }

    /**
     * Get GRNs with related data
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedWithRelations(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()
            ->with(['purchaseOrder', 'supplier', 'items.product'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find GRN by GRN number
     *
     * @param string $grnNumber
     * @return GoodsReceiptNote|null
     */
    public function findByGrnNumber(string $grnNumber): ?GoodsReceiptNote
    {
        return $this->query()
            ->where('grn_number', $grnNumber)
            ->first();
    }

    /**
     * Get GRNs by purchase order
     *
     * @param int $purchaseOrderId
     * @return Collection
     */
    public function getByPurchaseOrder(int $purchaseOrderId): Collection
    {
        return $this->query()
            ->where('purchase_order_id', $purchaseOrderId)
            ->with(['items.product'])
            ->get();
    }

    /**
     * Get GRNs by supplier
     *
     * @param int $supplierId
     * @return Collection
     */
    public function getBySupplier(int $supplierId): Collection
    {
        return $this->query()
            ->where('supplier_id', $supplierId)
            ->with(['purchaseOrder', 'items.product'])
            ->latest()
            ->get();
    }

    /**
     * Get GRNs by status
     *
     * @param GoodsReceiptStatus $status
     * @return Collection
     */
    public function getByStatus(GoodsReceiptStatus $status): Collection
    {
        return $this->query()
            ->where('status', $status)
            ->with(['purchaseOrder', 'supplier'])
            ->latest()
            ->get();
    }

    /**
     * Get pending GRNs
     *
     * @return Collection
     */
    public function getPending(): Collection
    {
        return $this->getByStatus(GoodsReceiptStatus::PENDING);
    }

    /**
     * Get completed GRNs
     *
     * @return Collection
     */
    public function getCompleted(): Collection
    {
        return $this->getByStatus(GoodsReceiptStatus::COMPLETED);
    }

    /**
     * Get GRNs with discrepancies
     *
     * @return Collection
     */
    public function getWithDiscrepancies(): Collection
    {
        return $this->query()
            ->whereHas('items', function ($query) {
                $query->whereColumn('quantity_received', '!=', 'quantity_ordered');
            })
            ->with(['purchaseOrder', 'supplier', 'items.product'])
            ->latest()
            ->get();
    }

    /**
     * Search GRNs by keyword
     *
     * @param string $keyword
     * @return Collection
     */
    public function search(string $keyword): Collection
    {
        return $this->query()
            ->where(function ($query) use ($keyword) {
                $query->where('grn_number', 'like', "%{$keyword}%")
                    ->orWhere('notes', 'like', "%{$keyword}%")
                    ->orWhere('discrepancy_notes', 'like', "%{$keyword}%")
                    ->orWhereHas('supplier', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('purchaseOrder', function ($q) use ($keyword) {
                        $q->where('po_number', 'like', "%{$keyword}%");
                    });
            })
            ->with(['purchaseOrder', 'supplier'])
            ->get();
    }

    /**
     * Get GRNs by date range
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->query()
            ->whereBetween('received_date', [$startDate, $endDate])
            ->with(['purchaseOrder', 'supplier', 'items.product'])
            ->latest('received_date')
            ->get();
    }
}
