<div class="space-y-6">
    @php
        $auditorias = $this->getAuditorias();
    @endphp

    @if($auditorias->isNotEmpty())
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                📋 Auditorías Registradas
            </h3>

            @foreach($auditorias as $auditoria)
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 space-y-2">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $auditoria->observaciones }}
                            </p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium @switch($auditoria->estado)
                            @case('aprobado')
                                bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @break
                            @case('observado')
                                bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                            @break
                            @case('rechazado')
                                bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                            @break
                        @endswitch">
                            @switch($auditoria->estado)
                                @case('aprobado')
                                    ✅ Aprobado
                                @break
                                @case('observado')
                                    ⏳ Observado
                                @break
                                @case('rechazado')
                                    ❌ Rechazado
                                @break
                            @endswitch
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-xs text-gray-600 dark:text-gray-400">
                        <div>
                            <span class="font-medium">Auditor:</span>
                            {{ $auditoria->usuario?->name ?? 'N/A' }}
                        </div>
                        <div>
                            <span class="font-medium">Fecha:</span>
                            {{ $auditoria->fecha_auditoria?->format('d/m/Y H:i') }}
                        </div>
                        @if($auditoria->fecha_implementacion)
                            <div>
                                <span class="font-medium">Implementado:</span>
                                {{ $auditoria->fecha_implementacion->format('d/m/Y') }}
                            </div>
                        @endif
                    </div>

                    @if($auditoria->notas_implementacion)
                        <div class="mt-2 p-2 bg-gray-50 dark:bg-gray-800 rounded text-xs text-gray-700 dark:text-gray-300">
                            <span class="font-medium">Notas:</span>
                            {{ $auditoria->notas_implementacion }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                📝 No hay auditorías registradas para esta tarea.
            </p>
        </div>
    @endif
</div>
