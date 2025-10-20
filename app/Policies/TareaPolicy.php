<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Tarea;
use Illuminate\Auth\Access\HandlesAuthorization;

class TareaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any tasks.
     */
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ver_tareas');
    }

    /**
     * Determine if the user can view the task.
     */
    public function view(AuthUser $authUser, Tarea $tarea): bool
    {
        return $authUser->can('ver_tareas');
    }

    /**
     * Determine if the user can create tasks.
     */
    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('crear_tareas');
    }

    /**
     * Determine if the user can update the task.
     */
    public function update(AuthUser $authUser, Tarea $tarea): bool
    {
        return $authUser->can('editar_tareas');
    }

    /**
     * Determine if the user can delete the task.
     */
    public function delete(AuthUser $authUser, Tarea $tarea): bool
    {
        return $authUser->can('eliminar_tareas');
    }

    /**
     * Determine if the user can restore the task.
     */
    public function restore(AuthUser $authUser, Tarea $tarea): bool
    {
        return $authUser->can('eliminar_tareas'); // Same permission as delete
    }

    /**
     * Determine if the user can permanently delete the task.
     */
    public function forceDelete(AuthUser $authUser, Tarea $tarea): bool
    {
        return $authUser->can('eliminar_tareas');
    }

    /**
     * Determine if the user can permanently delete any task.
     */
    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('eliminar_tareas');
    }

    /**
     * Determine if the user can restore any task.
     */
    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('eliminar_tareas');
    }

    /**
     * Determine if the user can replicate the task.
     */
    public function replicate(AuthUser $authUser, Tarea $tarea): bool
    {
        return $authUser->can('crear_tareas');
    }

    /**
     * Determine if the user can reorder tasks.
     */
    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('editar_tareas');
    }

}