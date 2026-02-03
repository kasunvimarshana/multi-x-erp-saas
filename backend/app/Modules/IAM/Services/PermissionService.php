<?php

namespace App\Modules\IAM\Services;

use App\Modules\IAM\Models\Permission;
use App\Modules\IAM\Repositories\PermissionRepository;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;

/**
 * Permission Service
 * 
 * Handles business logic for permission management.
 * Note: Permissions are system-defined and not created via API.
 */
class PermissionService extends BaseService
{
    public function __construct(
        protected PermissionRepository $permissionRepository
    ) {}

    /**
     * Get all permissions
     *
     * @return Collection
     */
    public function getAllPermissions(): Collection
    {
        return $this->permissionRepository->all();
    }

    /**
     * Get paginated permissions
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginatedPermissions(int $perPage = 15)
    {
        return $this->permissionRepository->paginate($perPage);
    }

    /**
     * Get permission by ID
     *
     * @param int $id
     * @return Permission
     */
    public function getPermissionById(int $id): Permission
    {
        return $this->permissionRepository->findOrFail($id);
    }

    /**
     * Get permission by slug
     *
     * @param string $slug
     * @return Permission|null
     */
    public function getPermissionBySlug(string $slug): ?Permission
    {
        return $this->permissionRepository->findBySlug($slug);
    }

    /**
     * Get permissions by module
     *
     * @param string $module
     * @return Collection
     */
    public function getPermissionsByModule(string $module): Collection
    {
        return $this->permissionRepository->getByModule($module);
    }

    /**
     * Get all permissions grouped by module
     *
     * @return Collection
     */
    public function getPermissionsGroupedByModule(): Collection
    {
        return $this->permissionRepository->getAllGroupedByModule();
    }

    /**
     * Get permission roles
     *
     * @param int $permissionId
     * @return Collection
     */
    public function getPermissionRoles(int $permissionId): Collection
    {
        return $this->permissionRepository->getPermissionRoles($permissionId);
    }
}
