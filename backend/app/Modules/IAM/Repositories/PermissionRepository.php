<?php

namespace App\Modules\IAM\Repositories;

use App\Modules\IAM\Models\Permission;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * Permission Repository
 * 
 * Handles data access for permissions.
 */
class PermissionRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    protected function model(): string
    {
        return Permission::class;
    }

    /**
     * Find permission by slug
     *
     * @param string $slug
     * @return Permission|null
     */
    public function findBySlug(string $slug): ?Permission
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Get permissions by module
     *
     * @param string $module
     * @return Collection
     */
    public function getByModule(string $module): Collection
    {
        return $this->model->where('module', $module)->get();
    }

    /**
     * Get permission roles
     *
     * @param int $permissionId
     * @return Collection
     */
    public function getPermissionRoles(int $permissionId): Collection
    {
        $permission = $this->findOrFail($permissionId);
        return $permission->roles;
    }

    /**
     * Get all permissions grouped by module
     *
     * @return Collection
     */
    public function getAllGroupedByModule(): Collection
    {
        return $this->model->get()->groupBy('module');
    }
}
