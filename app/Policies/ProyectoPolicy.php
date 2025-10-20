<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Proyecto;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProyectoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any projects.
     */
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ver_proyectos');
    }

    /**
     * Determine if the user can view the project.
     */
    public function view(AuthUser $authUser, Proyecto $proyecto): bool
    {
        return $authUser->can('ver_proyectos');
    }

    /**
     * Determine if the user can create projects.
     */
    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('crear_proyectos');
    }

    /**
     * Determine if the user can update the project.
     */
    public function update(AuthUser $authUser, Proyecto $proyecto): bool
    {
        return $authUser->can('editar_proyectos');
    }

    /**
     * Determine if the user can delete the project.
     */
    public function delete(AuthUser $authUser, Proyecto $proyecto): bool
    {
        return $authUser->can('eliminar_proyectos');
    }

    /**
     * Determine if the user can restore the project.
     */
    public function restore(AuthUser $authUser, Proyecto $proyecto): bool
    {
        return $authUser->can('eliminar_proyectos'); // Same permission as delete
    }

    /**
     * Determine if the user can permanently delete the project.
     */
    public function forceDelete(AuthUser $authUser, Proyecto $proyecto): bool
    {
        return $authUser->can('eliminar_proyectos');
    }

    /**
     * Determine if the user can permanently delete any project.
     */
    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('eliminar_proyectos');
    }

    /**
     * Determine if the user can restore any project.
     */
    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('eliminar_proyectos');
    }

    /**
     * Determine if the user can replicate the project.
     */
    public function replicate(AuthUser $authUser, Proyecto $proyecto): bool
    {
        return $authUser->can('crear_proyectos');
    }

    /**
     * Determine if the user can reorder projects.
     */
    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('editar_proyectos');
    }

}