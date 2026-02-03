<?php

namespace App\Modules\IAM\Services;

use App\Models\User;
use App\Modules\IAM\DTOs\UserDTO;
use App\Modules\IAM\Repositories\RoleRepository;
use App\Modules\IAM\Repositories\UserRepository;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

/**
 * User Service
 * 
 * Handles business logic for user management including
 * role assignments and permissions.
 */
class UserService extends BaseService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected RoleRepository $roleRepository
    ) {}

    /**
     * Get all users
     *
     * @param int|null $tenantId
     * @return Collection
     */
    public function getAllUsers(?int $tenantId = null): Collection
    {
        if ($tenantId) {
            return $this->userRepository->getByTenant($tenantId);
        }
        
        return $this->userRepository->all();
    }

    /**
     * Get paginated users
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginatedUsers(int $perPage = 15)
    {
        return $this->userRepository->paginate($perPage);
    }

    /**
     * Get user by ID
     *
     * @param int $id
     * @return User
     */
    public function getUserById(int $id): User
    {
        return $this->userRepository->findOrFail($id);
    }

    /**
     * Get user by email
     *
     * @param string $email
     * @return User|null
     */
    public function getUserByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    /**
     * Create a new user
     *
     * @param UserDTO $dto
     * @return User
     * @throws \Throwable
     */
    public function createUser(UserDTO $dto): User
    {
        return $this->transaction(function () use ($dto) {
            // Check if user with email already exists
            if ($this->userRepository->findByEmail($dto->email)) {
                throw new \InvalidArgumentException('User with this email already exists');
            }

            $userData = $dto->toArray();
            
            // Hash password if provided
            if ($dto->password) {
                $userData['password'] = Hash::make($dto->password);
            }

            $user = $this->userRepository->create($userData);

            $this->logInfo('User created', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return $user;
        });
    }

    /**
     * Update user
     *
     * @param int $id
     * @param array $data
     * @return User
     * @throws \Throwable
     */
    public function updateUser(int $id, array $data): User
    {
        return $this->transaction(function () use ($id, $data) {
            // Check if email is being updated and already exists
            if (isset($data['email'])) {
                $existingUser = $this->userRepository->findByEmail($data['email']);
                if ($existingUser && $existingUser->id !== $id) {
                    throw new \InvalidArgumentException('User with this email already exists');
                }
            }

            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $this->userRepository->update($id, $data);
            $user = $this->userRepository->findOrFail($id);

            $this->logInfo('User updated', [
                'user_id' => $user->id,
            ]);

            return $user;
        });
    }

    /**
     * Delete user
     *
     * @param int $id
     * @return bool
     * @throws \Throwable
     */
    public function deleteUser(int $id): bool
    {
        return $this->transaction(function () use ($id) {
            $user = $this->userRepository->findOrFail($id);
            
            $result = $this->userRepository->delete($id);

            $this->logInfo('User deleted', [
                'user_id' => $id,
            ]);

            return $result;
        });
    }

    /**
     * Assign roles to user
     *
     * @param int $userId
     * @param array $roleIds
     * @return User
     * @throws \Throwable
     */
    public function assignRoles(int $userId, array $roleIds): User
    {
        return $this->transaction(function () use ($userId, $roleIds) {
            $user = $this->userRepository->findOrFail($userId);

            // Validate all roles exist and fetch them
            $roles = [];
            foreach ($roleIds as $roleId) {
                $roles[] = $this->roleRepository->findOrFail($roleId);
            }

            // Assign each role
            foreach ($roles as $role) {
                $this->userRepository->assignRole($user, $role);
            }

            $this->logInfo('Roles assigned to user', [
                'user_id' => $userId,
                'role_ids' => $roleIds,
            ]);

            return $user->fresh(['roles']);
        });
    }

    /**
     * Sync user roles (replace all existing roles)
     *
     * @param int $userId
     * @param array $roleIds
     * @return User
     * @throws \Throwable
     */
    public function syncRoles(int $userId, array $roleIds): User
    {
        return $this->transaction(function () use ($userId, $roleIds) {
            $user = $this->userRepository->findOrFail($userId);

            // Validate all roles exist
            foreach ($roleIds as $roleId) {
                $this->roleRepository->findOrFail($roleId);
            }

            $this->userRepository->syncRoles($user, $roleIds);

            $this->logInfo('User roles synchronized', [
                'user_id' => $userId,
                'role_ids' => $roleIds,
            ]);

            return $user->fresh(['roles']);
        });
    }

    /**
     * Get user roles
     *
     * @param int $userId
     * @return Collection
     */
    public function getUserRoles(int $userId): Collection
    {
        $user = $this->userRepository->findOrFail($userId);
        return $user->roles;
    }

    /**
     * Get user permissions (via roles)
     *
     * @param int $userId
     * @return Collection
     */
    public function getUserPermissions(int $userId): Collection
    {
        $user = $this->userRepository->findOrFail($userId);
        
        // Get all unique permissions from all user roles
        return $user->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->unique('id')
            ->values();
    }

    /**
     * Search users
     *
     * @param string $search
     * @return Collection
     */
    public function searchUsers(string $search): Collection
    {
        return $this->userRepository->search($search);
    }

    /**
     * Get active users
     *
     * @return Collection
     */
    public function getActiveUsers(): Collection
    {
        return $this->userRepository->getActiveUsers();
    }

    /**
     * Get users by role
     *
     * @param int $roleId
     * @return Collection
     */
    public function getUsersByRole(int $roleId): Collection
    {
        return $this->userRepository->getUsersByRole($roleId);
    }
}
