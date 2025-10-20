<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any users.
     */
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ver_usuarios');
    }

    /**
     * Determine if the user can view the user.
     */
    public function view(AuthUser $authUser, User $user): bool
    {
        return $authUser->can('ver_usuarios');
    }

    /**
     * Determine if the user can create users.
     */
    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('crear_usuarios');
    }

    /**
     * Determine if the user can update the user.
     */
    public function update(AuthUser $authUser, User $user): bool
    {
        return $authUser->can('editar_usuarios');
    }

    /**
     * Determine if the user can delete the user.
     */
    public function delete(AuthUser $authUser, User $user): bool
    {
        return $authUser->can('eliminar_usuarios');
    }

    /**
     * Determine if the user can restore the user.
     */
    public function restore(AuthUser $authUser, User $user): bool
    {
        return $authUser->can('eliminar_usuarios'); // Same permission as delete
    }

    /**
     * Determine if the user can permanently delete the user.
     */
    public function forceDelete(AuthUser $authUser, User $user): bool
    {
        return $authUser->can('eliminar_usuarios');
    }

    /**
     * Determine if the user can permanently delete any user.
     */
    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('eliminar_usuarios');
    }

    /**
     * Determine if the user can restore any user.
     */
    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('eliminar_usuarios');
    }

    /**
     * Determine if the user can replicate the user.
     */
    public function replicate(AuthUser $authUser, User $user): bool
    {
        return $authUser->can('crear_usuarios');
    }

    /**
     * Determine if the user can reorder users.
     */
    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('editar_usuarios');
    }

}