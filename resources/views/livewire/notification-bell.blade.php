<div x-data="{ open: false }" class="fi-dropdown" style="display: inline-block;">
    <!-- Botón de notificaciones -->
    <button
        @click="open = !open"
        type="button"
        class="fi-icon-btn relative inline-flex items-center justify-center outline-none transition duration-75 -m-1.5 h-9 w-9 rounded-lg text-gray-400 hover:text-gray-500 focus-visible:bg-gray-500/10 dark:text-gray-500 dark:hover:text-gray-400 dark:focus-visible:bg-gray-400/10"
    >
        <svg class="fi-icon-btn-icon h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>

        @if($noLeidas > 0)
            <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center h-4 min-w-[1rem] px-1 text-[0.625rem] font-bold text-white bg-danger-600 rounded-full">
                {{ $noLeidas > 9 ? '9+' : $noLeidas }}
            </span>
        @endif
    </button>

    <!-- Dropdown de notificaciones -->
    <div
        x-show="open"
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fi-dropdown-panel absolute z-10 mt-2 origin-top-right divide-y divide-gray-100 rounded-lg bg-white shadow-lg ring-1 ring-gray-950/5 dark:divide-white/5 dark:bg-gray-900 dark:ring-white/10"
        style="position: absolute; right: 3rem; width: 480px; min-width: 480px;"
        x-cloak
    >
        <!-- Header -->
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-950 dark:text-white">
                    Notificaciones
                    @if($noLeidas > 0)
                        <span class="ml-2 text-xs font-medium text-gray-500 dark:text-gray-400">
                            ({{ $noLeidas }})
                        </span>
                    @endif
                </h3>
                @if($noLeidas > 0)
                    <button
                        wire:click="marcarTodasComoLeidas"
                        class="text-xs font-medium text-primary-600 hover:underline dark:text-primary-400"
                    >
                        Marcar todas
                    </button>
                @endif
            </div>
        </div>

        <!-- Lista de notificaciones -->
        <div class="overflow-y-auto">
            @forelse($notificaciones as $notificacion)
                <div style="position: relative !important; padding: 0.875rem 1rem !important; transition: all 0.2s !important; border-bottom: 1px solid #f3f4f6 !important; {{ !$notificacion['leida'] ? 'background-color: #eff6ff !important;' : 'background-color: #ffffff !important;' }}" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='{{ !$notificacion['leida'] ? '#eff6ff' : '#ffffff' }}'">
                    <!-- Indicador de no leída (borde izquierdo) -->
                    @if(!$notificacion['leida'])
                        <div style="position: absolute !important; left: 0 !important; top: 0 !important; bottom: 0 !important; width: 4px !important; background-color: #3b82f6 !important;"></div>
                    @endif

                    <!-- Contenido -->
                    <div style="display: flex !important; align-items: flex-start !important; justify-content: space-between !important; gap: 0.75rem !important;">
                        <div style="flex: 1 !important; min-width: 0 !important;">
                            <!-- Título con badge -->
                            <div style="display: flex !important; align-items: center !important; gap: 0.5rem !important; margin-bottom: 0.375rem !important;">
                                <h4 style="font-size: 0.875rem !important; font-weight: 600 !important; color: #111827 !important; margin: 0 !important;">
                                    {{ $notificacion['titulo'] }}
                                </h4>
                                @if(!$notificacion['leida'])
                                    <span style="display: inline-flex !important; align-items: center !important; padding: 0.125rem 0.5rem !important; border-radius: 9999px !important; font-size: 0.75rem !important; font-weight: 500 !important; background-color: #dbeafe !important; color: #1e40af !important;">
                                        Nueva
                                    </span>
                                @endif
                            </div>

                            <!-- Mensaje -->
                            <p style="font-size: 0.875rem !important; color: #4b5563 !important; line-height: 1.5 !important; margin-bottom: 0.5rem !important;">
                                {{ $notificacion['mensaje'] }}
                            </p>

                            <!-- Footer: Fecha y acciones -->
                            <div style="display: flex !important; align-items: center !important; justify-content: space-between !important;">
                                <div style="display: flex !important; align-items: center !important; gap: 0.5rem !important; font-size: 0.75rem !important; color: #6b7280 !important;">
                                    <svg style="width: 0.875rem !important; height: 0.875rem !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ \Carbon\Carbon::parse($notificacion['created_at'])->diffForHumans() }}</span>
                                </div>

                                <div style="display: flex !important; align-items: center !important; gap: 0.5rem !important;">
                                    @if(!$notificacion['leida'])
                                        <button
                                            wire:click="marcarComoLeida({{ $notificacion['id'] }})"
                                            style="padding: 0.25rem 0.625rem !important; border-radius: 0.375rem !important; font-size: 0.75rem !important; font-weight: 500 !important; transition: all 0.2s !important; background-color: #dbeafe !important; color: #1e40af !important; border: none !important; cursor: pointer !important;"
                                            onmouseover="this.style.backgroundColor='#bfdbfe'"
                                            onmouseout="this.style.backgroundColor='#dbeafe'"
                                        >
                                            Marcar leída
                                        </button>
                                    @endif
                                    <button
                                        wire:click="eliminar({{ $notificacion['id'] }})"
                                        style="padding: 0.25rem 0.625rem !important; border-radius: 0.375rem !important; font-size: 0.75rem !important; font-weight: 500 !important; transition: all 0.2s !important; background-color: #fee2e2 !important; color: #991b1b !important; border: none !important; cursor: pointer !important;"
                                        onmouseover="this.style.backgroundColor='#fecaca'"
                                        onmouseout="this.style.backgroundColor='#fee2e2'"
                                    >
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center px-4 py-12 text-center">
                    <p class="text-sm font-medium text-gray-950 dark:text-white">Sin notificaciones</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">No tienes notificaciones nuevas</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
