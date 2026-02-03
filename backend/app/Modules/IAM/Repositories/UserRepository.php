<?php

namespace App\Modules\IAM\Repositories;

use App\Models\User;
use App\Modules\IAM\Models\Role;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * User Repository
 * 
 * Handles data access for users.
 */
class UserRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    protected function model(): string
    {
        return User::class;
    }

    /**
     * Find user by email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Get active users
     *
     * @return Collection
     */
    public function getActiveUsers(): Collection
    {
        return $this->model->where('is_active', true)->get();
    }

    /**
     * Get users by tenant
     *
     * @param int $tenantId
     * @return Collection
     */
    public function getByTenant(int $tenantId): Collection
    {
        return $this->model->where('tenant_id', $tenantId)->get();
    }

    /**
     * Get users by role
     *
     * @param int $roleId
     * @return Collection
     */
    public function getUsersByRole(int $roleId): Collection
    {
        return $this->model
            ->whereHas('roles', function ($query) use ($roleId) {
                $query->where('roles.id', $roleId);
            })
            ->get();
    }

    /**
     * Assign role to user
     *
     * @param User $user
     * @param Role $role
     * @return void
     */
    public function assignRole(User $user, Role $role): void
    {
        $user->roles()->syncWithoutDetaching([$role->id]);
    }

    /**
     * Remove role from user
     *
     * @param User $user
     * @param Role $role
     * @return void
     */
    public function removeRole(User $user, Role $role): void
    {
        $user->roles()->detach($role->id);
    }

    /**
     * Sync user roles
     *
     * @param User $user
     * @param array $roleIds
     * @return void
     */
    public function syncRoles(User $user, array $roleIds): void
    {
        $user->roles()->sync($roleIds);
    }

    /**
     * Search users
     *
     * @param string $search
     * @return Collection
     */
    public function search(string $search): Collection
    {
        return $this->model
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->get();
    }
}
