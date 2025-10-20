<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Tarea;
use Illuminate\Auth\Access\HandlesAuthorization;

class TareaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Tarea');
    }

    public function view(AuthUser $authUser, Tarea $tarea): bool
    {
        return $authUser->can('View:Tarea');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Tarea');
    }

    public function update(AuthUser $authUser, Tarea $tarea): bool
    {
        return $authUser->can('Update:Tarea');
    }

    public function delete(AuthUser $authUser, Tarea $tarea): bool
    {
        return $authUser->can('Delete:Tarea');
    }

    public function restore(AuthUser $authUser, Tarea $tarea): bool
    {
        return $authUser->can('Restore:Tarea');
    }

    public function forceDelete(AuthUser $authUser, Tarea $tarea): bool
    {
        return $authUser->can('ForceDelete:Tarea');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Tarea');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Tarea');
    }

    public function replicate(AuthUser $authUser, Tarea $tarea): bool
    {
        return $authUser->can('Replicate:Tarea');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Tarea');
    }

}