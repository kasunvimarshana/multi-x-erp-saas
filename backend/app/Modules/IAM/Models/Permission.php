<?php

namespace App\Modules\IAM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Permission Model
 * 
 * Represents a permission in the RBAC system.
 */
class Permission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'module',
        'description',
    ];

    /**
     * The roles that have this permission
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role')
                    ->withTimestamps();
    }

    /**
     * Grant this permission to a role
     *
     * @param Role $role
     * @return void
     */
    public function assignToRole(Role $role): void
    {
        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    /**
     * Revoke this permission from a role
     *
     * @param Role $role
     * @return void
     */
    public function removeFromRole(Role $role): void
    {
        $this->roles()->detach($role->id);
    }
}
