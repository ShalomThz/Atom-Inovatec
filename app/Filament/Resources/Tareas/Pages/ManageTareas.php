<?php

namespace App\Filament\Resources\Tareas\Pages;

use App\Filament\Resources\Tareas\TareaResource;
use App\Models\User;
use App\Services\NotificacionService;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageTareas extends ManageRecords
{
    protected static string $resource = TareaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(function ($record) {
                    // Notificar al usuario asignado cuando se crea una nueva tarea
                    if ($record->user_id) {
                        NotificacionService::notificarTareaAsignada($record, auth()->user());
                    }
                }),
        ];
    }
}
