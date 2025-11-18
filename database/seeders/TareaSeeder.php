<?php

namespace Database\Seeders;

use App\Models\Proyecto;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Database\Seeder;

class TareaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = User::all();
        $proyectos = Proyecto::all();

        // Proyecto 1: Desarrollo Web Corporativo
        $proyecto1 = $proyectos->where('nombre', 'Desarrollo Web Corporativo')->first();
        if ($proyecto1) {
            $tareas1 = [
                [
                    'proyecto_id' => $proyecto1->id,
                    'nombre' => 'Desarrollo del frontend',
                    'descripcion' => 'Implementar las vistas con HTML, CSS y JavaScript. Asegurar diseño responsive para móviles y tablets',
                    'user_id' => $usuarios[3]->id, // Carlos Rodríguez
                    'estado' => 'en_progreso',
                    'fecha_inicio' => now()->subMonths(2),
                    'fecha_fin' => now()->addMonth(),
                    'prioridad' => 3,
                    'progreso' => 65,
                ],
                [
                    'proyecto_id' => $proyecto1->id,
                    'nombre' => 'Testing y control de calidad',
                    'descripcion' => 'Realizar pruebas de funcionalidad, compatibilidad cross-browser y performance',
                    'user_id' => $usuarios[5]->id, // Luis Fernández (Desarrollador)
                    'estado' => 'pendiente',
                    'fecha_inicio' => now()->addMonths(2)->addWeeks(2),
                    'fecha_fin' => now()->addMonths(3),
                    'prioridad' => 3,
                    'progreso' => 0,
                ],
            ];

            foreach ($tareas1 as $tarea) {
                Tarea::create($tarea);
            }
        }

        // Proyecto 2: Sistema de Gestión Interna (ERP)
        $proyecto2 = $proyectos->where('nombre', 'Sistema de Gestión Interna (ERP)')->first();
        if ($proyecto2) {
            $tareas2 = [
                [
                    'proyecto_id' => $proyecto2->id,
                    'nombre' => 'Módulo de Inventario',
                    'descripcion' => 'Desarrollar módulo para gestión de inventario: productos, categorías, proveedores, stock',
                    'user_id' => $usuarios[5]->id, // Luis Fernández
                    'estado' => 'en_progreso',
                    'fecha_inicio' => now()->subWeeks(1),
                    'fecha_fin' => now()->addMonths(2),
                    'prioridad' => 3,
                    'progreso' => 35,
                ],
                [
                    'proyecto_id' => $proyecto2->id,
                    'nombre' => 'Módulo de Finanzas',
                    'descripcion' => 'Desarrollar módulo financiero: cuentas por cobrar/pagar, facturación, reportes contables',
                    'user_id' => $usuarios[6]->id, // Laura Sánchez
                    'estado' => 'pendiente',
                    'fecha_inicio' => now()->addMonths(2),
                    'fecha_fin' => now()->addMonths(5),
                    'prioridad' => 3,
                    'progreso' => 0,
                ],
            ];

            foreach ($tareas2 as $tarea) {
                Tarea::create($tarea);
            }
        }

        // Proyecto 6: Dashboard Analítico BI (Completado)
        $proyecto6 = $proyectos->where('nombre', 'Dashboard Analítico BI')->first();
        if ($proyecto6) {
            $tareas6 = [
                [
                    'proyecto_id' => $proyecto6->id,
                    'nombre' => 'Desarrollo de visualizaciones',
                    'descripcion' => 'Crear gráficos interactivos: líneas, barras, tortas, mapas de calor, etc.',
                    'user_id' => $usuarios[3]->id, // Carlos Rodríguez
                    'estado' => 'completada',
                    'fecha_inicio' => now()->subMonths(3),
                    'fecha_fin' => now()->subMonths(2),
                    'prioridad' => 3,
                    'progreso' => 100,
                ],
                [
                    'proyecto_id' => $proyecto6->id,
                    'nombre' => 'Sistema de alertas automáticas',
                    'descripcion' => 'Configurar alertas por email/SMS cuando KPIs alcancen umbrales específicos',
                    'user_id' => $usuarios[5]->id, // Luis Fernández
                    'estado' => 'completada',
                    'fecha_inicio' => now()->subMonths(2),
                    'fecha_fin' => now()->subMonth(),
                    'prioridad' => 2,
                    'progreso' => 100,
                ],
            ];

            foreach ($tareas6 as $tarea) {
                Tarea::create($tarea);
            }
        }

        $this->command->info('✓ Tareas creadas exitosamente para todos los proyectos');
    }
}
