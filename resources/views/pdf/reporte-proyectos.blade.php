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

        @page {
            margin: 2cm 1.5cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            border-bottom: 3px solid #1e40af;
            padding: 20px;
            margin-bottom: 25px;
        }

        .header-top {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .header-logo {
            display: table-cell;
            width: 100px;
            vertical-align: middle;
        }

        .header-logo img {
            max-width: 80px;
            max-height: 80px;
        }

        .header-title {
            display: table-cell;
            vertical-align: middle;
            padding-left: 20px;
        }

        .header-title h1 {
            font-size: 26px;
            color: #1e40af;
            margin-bottom: 3px;
            font-weight: bold;
        }

        .header-title .subtitle {
            font-size: 12px;
            color: #6b7280;
        }

        .header-info {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            margin-top: 15px;
        }

        .header-info-grid {
            display: table;
            width: 100%;
        }

        .header-info-item {
            display: table-cell;
            width: 50%;
            padding: 5px 10px;
        }

        .header-info-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .header-info-value {
            font-size: 11px;
            color: #111827;
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
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }

        .footer-logo {
            margin-bottom: 8px;
            opacity: 0.5;
        }

        .footer-logo img {
            max-width: 40px;
            max-height: 40px;
        }

        .page-break {
            page-break-after: always;
        }

        .no-proyectos {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }

        /* Estilos para el resumen ejecutivo */
        .resumen-ejecutivo {
            margin-bottom: 30px;
            page-break-after: avoid;
        }

        .seccion-titulo {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #1e40af;
        }

        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .stat-row {
            display: table-row;
        }

        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }

        .stat-numero {
            font-size: 28px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
        }

        /* Estilos para tablas */
        .tabla {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .tabla th {
            background-color: #1e40af;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
        }

        .tabla td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }

        .tabla tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .seccion {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .progreso-bar-container {
            background-color: #e5e7eb;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 3px;
        }

        .progreso-bar {
            height: 100%;
            background-color: #10b981;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-top">
            <div class="header-logo">
                <img src="{{ public_path('logo.svg') }}" alt="Logo Empresa">
            </div>
            <div class="header-title">
                <h1>Reporte de Proyectos</h1>
                <div class="subtitle">Sistema de Gestión de Proyectos</div>
            </div>
        </div>

        <div class="header-info">
            <div class="header-info-grid">
                <div class="header-info-item">
                    <div class="header-info-label">Fecha de Generación</div>
                    <div class="header-info-value">{{ $fecha_generacion }}</div>
                </div>
                <div class="header-info-item">
                    <div class="header-info-label">Total de Proyectos</div>
                    <div class="header-info-value">{{ $total_proyectos }} proyecto(s)</div>
                </div>
            </div>
            <div class="header-info-grid">
                <div class="header-info-item">
                    <div class="header-info-label">Dirigido a</div>
                    <div class="header-info-value">{{ $dirigido_a }} ({{ $usuario_rol }})</div>
                </div>
                <div class="header-info-item">
                    <div class="header-info-label">Generado por</div>
                    <div class="header-info-value">{{ $usuario }} - {{ $usuario_email }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- RESUMEN EJECUTIVO -->
    <div class="resumen-ejecutivo seccion">
        <h2 class="seccion-titulo">1. Resumen Ejecutivo</h2>

        <div class="stats-grid">
            <div class="stat-row">
                <div class="stat-box">
                    <div class="stat-numero">{{ $total_proyectos }}</div>
                    <div class="stat-label">Total Proyectos</div>
                </div>
                <div class="stat-box">
                    <div class="stat-numero">{{ $proyectos_completados }}</div>
                    <div class="stat-label">Completados</div>
                </div>
                <div class="stat-box">
                    <div class="stat-numero">{{ $proyectos_en_progreso }}</div>
                    <div class="stat-label">En Progreso</div>
                </div>
                <div class="stat-box">
                    <div class="stat-numero" style="color: #ef4444;">{{ $proyectos_atrasados_count }}</div>
                    <div class="stat-label">Atrasados</div>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-row">
                <div class="stat-box">
                    <div class="stat-numero">{{ $total_tareas }}</div>
                    <div class="stat-label">Total Tareas</div>
                </div>
                <div class="stat-box">
                    <div class="stat-numero" style="color: #10b981;">{{ $tareas_completadas }}</div>
                    <div class="stat-label">Completadas</div>
                </div>
                <div class="stat-box">
                    <div class="stat-numero" style="color: #3b82f6;">{{ $tareas_en_progreso }}</div>
                    <div class="stat-label">En Progreso</div>
                </div>
                <div class="stat-box">
                    <div class="stat-numero" style="color: #f59e0b;">{{ $tareas_pendientes }}</div>
                    <div class="stat-label">Pendientes</div>
                </div>
            </div>
        </div>

        <div style="background-color: #dbeafe; padding: 15px; border-left: 4px solid #1e40af; margin-top: 15px;">
            <strong>Porcentaje de Cumplimiento General:</strong> {{ $porcentaje_cumplimiento }}%
            <div class="progreso-bar-container">
                <div class="progreso-bar" style="width: {{ $porcentaje_cumplimiento }}%;"></div>
            </div>
        </div>
    </div>

    <!-- PROYECTOS ATRASADOS -->
    @if(count($proyectos_atrasados) > 0)
    <div class="seccion">
        <h2 class="seccion-titulo">2. Proyectos Atrasados</h2>
        <table class="tabla">
            <thead>
                <tr>
                    <th>Proyecto</th>
                    <th>Líder</th>
                    <th>Fecha Límite</th>
                    <th class="text-center">Progreso</th>
                    <th class="text-center">Días de Retraso</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proyectos_atrasados as $proyecto)
                <tr>
                    <td><strong>{{ $proyecto['nombre'] }}</strong></td>
                    <td>{{ $proyecto['lider'] }}</td>
                    <td>{{ $proyecto['fecha_fin'] }}</td>
                    <td class="text-center">
                        {{ number_format($proyecto['progreso'], 1) }}%
                        <div class="progreso-bar-container">
                            <div class="progreso-bar" style="width: {{ $proyecto['progreso'] }}%;"></div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-danger">{{ $proyecto['dias_retraso'] }} días</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- EMPLEADOS DESTACADOS -->
    @if(count($empleados_destacados) > 0)
    <div class="seccion">
        <h2 class="seccion-titulo">3. Empleados Destacados (≥80% Cumplimiento)</h2>
        <table class="tabla">
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Rol</th>
                    <th class="text-center">Total Tareas</th>
                    <th class="text-center">Completadas</th>
                    <th class="text-center">% Cumplimiento</th>
                </tr>
            </thead>
            <tbody>
                @foreach($empleados_destacados as $empleado)
                <tr>
                    <td><strong>{{ $empleado['nombre'] }}</strong><br><small style="color: #6b7280;">{{ $empleado['email'] }}</small></td>
                    <td>{{ $empleado['rol'] }}</td>
                    <td class="text-center">{{ $empleado['total_tareas'] }}</td>
                    <td class="text-center">{{ $empleado['tareas_completadas'] }}</td>
                    <td class="text-center">
                        <span class="badge badge-success">{{ $empleado['porcentaje_completadas'] }}%</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- EMPLEADOS CON TAREAS PENDIENTES O ATRASADAS -->
    @if(count($empleados_pendientes) > 0)
    <div class="seccion">
        <h2 class="seccion-titulo">4. Empleados con Tareas Pendientes o Atrasadas</h2>
        <table class="tabla">
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Rol</th>
                    <th class="text-center">Total Tareas</th>
                    <th class="text-center">Pendientes</th>
                    <th class="text-center">Atrasadas</th>
                    <th class="text-center">% Completadas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($empleados_pendientes as $empleado)
                <tr>
                    <td><strong>{{ $empleado['nombre'] }}</strong><br><small style="color: #6b7280;">{{ $empleado['email'] }}</small></td>
                    <td>{{ $empleado['rol'] }}</td>
                    <td class="text-center">{{ $empleado['total_tareas'] }}</td>
                    <td class="text-center">
                        @if($empleado['tareas_pendientes'] > 0)
                        <span class="badge badge-warning">{{ $empleado['tareas_pendientes'] }}</span>
                        @else
                        0
                        @endif
                    </td>
                    <td class="text-center">
                        @if($empleado['tareas_atrasadas'] > 0)
                        <span class="badge badge-danger">{{ $empleado['tareas_atrasadas'] }}</span>
                        @else
                        0
                        @endif
                    </td>
                    <td class="text-center">{{ $empleado['porcentaje_completadas'] }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- DETALLE DE PROYECTOS -->
    <div class="seccion">
        <h2 class="seccion-titulo">5. Detalle de Proyectos</h2>
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
                        Líder: {{ $proyecto['lider'] }}
                        @if($proyecto['fecha_inicio'])
                            | Período: {{ $proyecto['fecha_inicio'] }} - {{ $proyecto['fecha_fin'] ?? 'Sin fecha' }}
                        @endif
                        @if($proyecto['presupuesto'])
                            | Presupuesto: ${{ number_format($proyecto['presupuesto'], 2) }}
                        @endif
                    </div>

                    <div>
                        <span class="proyecto-estado
                            @if($proyecto['estado'] === 'completado') estado-completado
                            @elseif($proyecto['estado'] === 'en_progreso') estado-en-progreso
                            @else estado-pendiente
                            @endif">
                            @if($proyecto['estado'] === 'completado') Completado
                            @elseif($proyecto['estado'] === 'en_progreso') En Progreso
                            @elseif($proyecto['estado'] === 'pendiente') Pendiente
                            @else Cancelado
                            @endif
                        </span>

                        @if($proyecto['esta_retrasado'])
                            <span class="proyecto-estado estado-retrasado">Retrasado</span>
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
        <div class="footer-logo">
            <img src="{{ public_path('logo.svg') }}" alt="Logo">
        </div>
        <p><strong>Sistema de Gestión de Proyectos</strong></p>
        <p>Este reporte fue generado automáticamente el {{ $fecha_generacion }}</p>
        <p>Documento confidencial - Para uso interno únicamente</p>
    </div>
</body>
</html>
