<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any roles.
     */
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_role');
    }

    /**
     * Determine if the user can view the role.
     */
    public function view(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('view_role');
    }

    /**
     * Determine if the user can create roles.
     */
    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_role');
    }

    /**
     * Determine if the user can update the role.
     */
    public function update(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('update_role');
    }

    /**
     * Determine if the user can delete the role.
     */
    public function delete(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('delete_role');
    }

    /**
     * Determine if the user can restore the role.
     */
    public function restore(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('restore_role');
    }

    /**
     * Determine if the user can permanently delete the role.
     */
    public function forceDelete(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('force_delete_role');
    }

    /**
     * Determine if the user can permanently delete any role.
     */
    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_role');
    }

    /**
     * Determine if the user can restore any role.
     */
    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_role');
    }

    /**
     * Determine if the user can replicate the role.
     */
    public function replicate(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('replicate_role');
    }

    /**
     * Determine if the user can reorder roles.
     */
    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_role');
    }

}