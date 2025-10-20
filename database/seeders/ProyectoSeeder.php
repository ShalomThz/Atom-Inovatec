<?php

namespace Database\Seeders;

use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProyectoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = User::all();

        $proyectos = [
            [
                'nombre' => 'Desarrollo Web Corporativo',
                'descripcion' => 'Desarrollo completo del sitio web institucional de la empresa, incluyendo diseño responsive, integración con CMS y optimización SEO.',
                'user_id' => $usuarios[1]->id, // Juan Pérez
                'fecha_inicio' => now()->subMonths(3),
                'fecha_fin' => now()->addMonths(3),
                'estado' => 'en_progreso',
                'presupuesto' => 50000.00,
                'prioridad' => 3,
            ],
            [
                'nombre' => 'Sistema de Gestión Interna (ERP)',
                'descripcion' => 'Implementación de sistema ERP para gestión de recursos empresariales, incluyendo módulos de inventario, finanzas, recursos humanos y reportería.',
                'user_id' => $usuarios[1]->id, // Juan Pérez
                'fecha_inicio' => now()->subMonths(1),
                'fecha_fin' => now()->addMonths(11),
                'estado' => 'en_progreso',
                'presupuesto' => 120000.00,
                'prioridad' => 3,
            ],
            [
                'nombre' => 'Aplicación Móvil de Ventas',
                'descripcion' => 'Desarrollo de aplicación móvil nativa para iOS y Android que permita al equipo de ventas gestionar clientes, productos y pedidos en tiempo real.',
                'user_id' => $usuarios[2]->id, // María García
                'fecha_inicio' => now()->subMonths(2),
                'fecha_fin' => now()->addMonths(7),
                'estado' => 'en_progreso',
                'presupuesto' => 75000.00,
                'prioridad' => 3,
            ],
            [
                'nombre' => 'Plataforma E-Learning',
                'descripcion' => 'Plataforma de aprendizaje en línea con videoconferencias, evaluaciones automáticas, seguimiento de progreso y gamificación.',
                'user_id' => $usuarios[2]->id, // María García
                'fecha_inicio' => now()->addWeeks(2),
                'fecha_fin' => now()->addMonths(10),
                'estado' => 'pendiente',
                'presupuesto' => 90000.00,
                'prioridad' => 2,
            ],
            [
                'nombre' => 'Sistema de Control de Accesos',
                'descripcion' => 'Sistema biométrico de control de accesos integrado con sistema de nómina y gestión de horarios.',
                'user_id' => $usuarios[1]->id, // Juan Pérez
                'fecha_inicio' => now()->addMonths(1),
                'fecha_fin' => now()->addMonths(5),
                'estado' => 'pendiente',
                'presupuesto' => 35000.00,
                'prioridad' => 2,
            ],
            [
                'nombre' => 'Dashboard Analítico BI',
                'descripcion' => 'Dashboard interactivo de Business Intelligence con visualizaciones en tiempo real, KPIs y reportes automatizados.',
                'user_id' => $usuarios[2]->id, // María García
                'fecha_inicio' => now()->subMonths(4),
                'fecha_fin' => now()->subWeeks(2),
                'estado' => 'completado',
                'presupuesto' => 45000.00,
                'prioridad' => 3,
            ],
            [
                'nombre' => 'API RESTful para Integración',
                'descripcion' => 'Desarrollo de API RESTful para integración con sistemas de terceros, incluyendo documentación completa y ejemplos de uso.',
                'user_id' => $usuarios[1]->id, // Juan Pérez
                'fecha_inicio' => now()->subMonths(2),
                'fecha_fin' => now()->addMonth(),
                'estado' => 'en_progreso',
                'presupuesto' => 28000.00,
                'prioridad' => 2,
            ],
            [
                'nombre' => 'Sistema de Tickets de Soporte',
                'descripcion' => 'Sistema de gestión de tickets de soporte técnico con asignación automática, SLA y base de conocimientos.',
                'user_id' => $usuarios[2]->id, // María García
                'fecha_inicio' => now()->addMonths(2),
                'fecha_fin' => now()->addMonths(6),
                'estado' => 'pendiente',
                'presupuesto' => 32000.00,
                'prioridad' => 1,
            ],
            [
                'nombre' => 'Migración a la Nube',
                'descripcion' => 'Migración de infraestructura on-premise a AWS, incluyendo configuración de servicios, seguridad y automatización.',
                'user_id' => $usuarios[1]->id, // Juan Pérez
                'fecha_inicio' => now()->subMonths(6),
                'fecha_fin' => now()->subMonths(1),
                'estado' => 'completado',
                'presupuesto' => 65000.00,
                'prioridad' => 3,
            ],
            [
                'nombre' => 'Rediseño de Identidad Corporativa',
                'descripcion' => 'Rediseño completo de la identidad corporativa, incluyendo logo, paleta de colores, tipografía y manual de marca.',
                'user_id' => $usuarios[2]->id, // María García
                'fecha_inicio' => now()->subMonths(5),
                'fecha_fin' => now()->subMonths(4),
                'estado' => 'cancelado',
                'presupuesto' => 15000.00,
                'prioridad' => 1,
            ],
        ];

        foreach ($proyectos as $proyecto) {
            Proyecto::create($proyecto);
        }

        $this->command->info('✓ 10 proyectos creados exitosamente');
    }
}
