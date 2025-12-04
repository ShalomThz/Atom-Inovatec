<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Proyectos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            background-color: #1e40af;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header .info {
            font-size: 11px;
            opacity: 0.9;
        }

        .proyecto {
            page-break-inside: avoid;
            margin-bottom: 25px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        .proyecto-header {
            background-color: #f3f4f6;
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        .proyecto-titulo {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 5px;
        }

        .proyecto-info {
            font-size: 10px;
            color: #6b7280;
            margin-top: 5px;
        }

        .proyecto-estado {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 5px;
        }

        .estado-completado {
            background-color: #d1fae5;
            color: #065f46;
        }

        .estado-en-progreso {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .estado-pendiente {
            background-color: #f3f4f6;
            color: #374151;
        }

        .estado-retrasado {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .proyecto-body {
            padding: 15px;
        }

        .metricas {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .metrica {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
        }

        .metrica-valor {
            font-size: 20px;
            font-weight: bold;
            color: #111827;
        }

        .metrica-label {
            font-size: 9px;
            color: #6b7280;
            margin-top: 3px;
        }

        .distribución {
            margin-top: 15px;
        }

        .distribución-titulo {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #374151;
        }

        .barra-container {
            margin-bottom: 8px;
        }

        .barra-label {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 3px;
            display: flex;
            justify-content: space-between;
        }

        .barra-fondo {
            background-color: #e5e7eb;
            height: 12px;
            border-radius: 6px;
            overflow: hidden;
        }

        .barra-progreso {
            height: 100%;
            border-radius: 6px;
        }

        .barra-pendiente {
            background-color: #9ca3af;
        }

        .barra-en-progreso {
            background-color: #3b82f6;
        }

        .barra-completada {
            background-color: #10b981;
        }

        .barra-cancelada {
            background-color: #ef4444;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }

        .no-proyectos {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Reporte de Proyectos</h1>
        <div class="info">
            Generado el: {{ $fecha_generacion }} | Por: {{ $usuario }}
        </div>
    </div>

    @if(count($proyectos) === 0)
        <div class="no-proyectos">
            <p>No hay proyectos disponibles para mostrar.</p>
        </div>
    @else
        @foreach($proyectos as $proyecto)
            <div class="proyecto">
                <div class="proyecto-header">
                    <div class="proyecto-titulo">{{ $proyecto['nombre'] }}</div>

                    @if($proyecto['descripcion'])
                        <div class="proyecto-info">{{ $proyecto['descripcion'] }}</div>
                    @endif

                    <div class="proyecto-info">
                        👤 {{ $proyecto['lider'] }}
                        @if($proyecto['fecha_inicio'])
                            | 📅 {{ $proyecto['fecha_inicio'] }} - {{ $proyecto['fecha_fin'] ?? 'Sin fecha' }}
                        @endif
                        @if($proyecto['presupuesto'])
                            | 💰 ${{ number_format($proyecto['presupuesto'], 2) }}
                        @endif
                    </div>

                    <div>
                        <span class="proyecto-estado
                            @if($proyecto['estado'] === 'completado') estado-completado
                            @elseif($proyecto['estado'] === 'en_progreso') estado-en-progreso
                            @else estado-pendiente
                            @endif">
                            @if($proyecto['estado'] === 'completado') ✅ Completado
                            @elseif($proyecto['estado'] === 'en_progreso') 🔄 En Progreso
                            @elseif($proyecto['estado'] === 'pendiente') ⏸️ Pendiente
                            @else ❌ Cancelado
                            @endif
                        </span>

                        @if($proyecto['esta_retrasado'])
                            <span class="proyecto-estado estado-retrasado">⚠️ Retrasado</span>
                        @endif
                    </div>
                </div>

                <div class="proyecto-body">
                    <!-- Métricas -->
                    <div class="metricas">
                        <div class="metrica">
                            <div class="metrica-valor">{{ number_format($proyecto['progreso_general'], 1) }}%</div>
                            <div class="metrica-label">PROGRESO GENERAL</div>
                        </div>
                        <div class="metrica">
                            <div class="metrica-valor">{{ $proyecto['estadisticas']['total'] }}</div>
                            <div class="metrica-label">TOTAL TAREAS</div>
                        </div>
                        <div class="metrica">
                            <div class="metrica-valor" style="color: #10b981;">{{ $proyecto['estadisticas']['completadas'] }}</div>
                            <div class="metrica-label">COMPLETADAS</div>
                        </div>
                        <div class="metrica">
                            <div class="metrica-valor" style="color: #f59e0b;">{{ $proyecto['estadisticas']['pendientes'] }}</div>
                            <div class="metrica-label">PENDIENTES</div>
                        </div>
                    </div>

                    <!-- Distribución de Tareas -->
                    <div class="distribución">
                        <div class="distribución-titulo">Distribución de Tareas</div>

                        <!-- Pendientes -->
                        <div class="barra-container">
                            <div class="barra-label">
                                <span>Pendientes</span>
                                <span><strong>{{ $proyecto['estadisticas']['pendientes'] }}</strong></span>
                            </div>
                            <div class="barra-fondo">
                                <div class="barra-progreso barra-pendiente"
                                     style="width: {{ $proyecto['estadisticas']['total'] > 0 ? ($proyecto['estadisticas']['pendientes'] / $proyecto['estadisticas']['total'] * 100) : 0 }}%">
                                </div>
                            </div>
                        </div>

                        <!-- En Progreso -->
                        <div class="barra-container">
                            <div class="barra-label">
                                <span>En Progreso</span>
                                <span><strong>{{ $proyecto['estadisticas']['en_progreso'] }}</strong></span>
                            </div>
                            <div class="barra-fondo">
                                <div class="barra-progreso barra-en-progreso"
                                     style="width: {{ $proyecto['estadisticas']['total'] > 0 ? ($proyecto['estadisticas']['en_progreso'] / $proyecto['estadisticas']['total'] * 100) : 0 }}%">
                                </div>
                            </div>
                        </div>

                        <!-- Completadas -->
                        <div class="barra-container">
                            <div class="barra-label">
                                <span>Completadas ({{ number_format($proyecto['porcentaje_completadas'], 1) }}%)</span>
                                <span><strong>{{ $proyecto['estadisticas']['completadas'] }}</strong></span>
                            </div>
                            <div class="barra-fondo">
                                <div class="barra-progreso barra-completada"
                                     style="width: {{ $proyecto['porcentaje_completadas'] }}%">
                                </div>
                            </div>
                        </div>

                        @if($proyecto['estadisticas']['canceladas'] > 0)
                        <!-- Canceladas -->
                        <div class="barra-container">
                            <div class="barra-label">
                                <span>Canceladas</span>
                                <span><strong>{{ $proyecto['estadisticas']['canceladas'] }}</strong></span>
                            </div>
                            <div class="barra-fondo">
                                <div class="barra-progreso barra-cancelada"
                                     style="width: {{ $proyecto['estadisticas']['total'] > 0 ? ($proyecto['estadisticas']['canceladas'] / $proyecto['estadisticas']['total'] * 100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <div class="footer">
        <p>Este reporte fue generado automáticamente por el Sistema de Gestión de Proyectos</p>
        <p>Total de proyectos: {{ count($proyectos) }}</p>
    </div>
</body>
</html>
