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
                <div class="fi-dropdown-list-item flex items-start gap-3 px-4 py-2.5 transition {{ !$notificacion['leida'] ? 'bg-primary-50/50 dark:bg-primary-950/50' : '' }} hover:bg-gray-50 dark:hover:bg-white/5">
                    <!-- Contenido -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-medium text-gray-950 dark:text-white">
                                {{ $notificacion['titulo'] }}
                            </p>
                            @if(!$notificacion['leida'])
                                <div class="flex-shrink-0 mt-1.5 h-2 w-2 rounded-full bg-primary-600"></div>
                            @endif
                        </div>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                            {{ $notificacion['mensaje'] }}
                        </p>
                        <div class="mt-2 flex items-center justify-between">
                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                {{ \Carbon\Carbon::parse($notificacion['created_at'])->diffForHumans() }}
                            </p>
                            <div class="flex gap-2">
                                @if(!$notificacion['leida'])
                                    <button
                                        wire:click="marcarComoLeida({{ $notificacion['id'] }})"
                                        style="font-size: 0.75rem !important; font-weight: 500 !important; color: #4f46e5 !important; transition: all 0.2s !important;"
                                        onmouseover="this.style.color='#4338ca'"
                                        onmouseout="this.style.color='#4f46e5'"
                                    >
                                        Marcar leída
                                    </button>
                                @endif
                                <button
                                    wire:click="eliminar({{ $notificacion['id'] }})"
                                    style="font-size: 0.75rem !important; font-weight: 500 !important; color: #dc2626 !important; transition: all 0.2s !important;"
                                    onmouseover="this.style.color='#b91c1c'"
                                    onmouseout="this.style.color='#dc2626'"
                                >
                                    Eliminar
                                </button>
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
