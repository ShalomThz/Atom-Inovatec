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
                    'nombre' => 'Diseño de wireframes y mockups',
                    'descripcion' => 'Crear wireframes de baja fidelidad y mockups de alta fidelidad para todas las páginas del sitio web',
                    'user_id' => $usuarios[6]->id, // Laura Sánchez (Desarrollador)
                    'estado' => 'completada',
                    'fecha_inicio' => now()->subMonths(3),
                    'fecha_fin' => now()->subMonths(2)->subWeeks(2),
                    'prioridad' => 3,
                    'progreso' => 100,
                ],
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
                    'nombre' => 'Desarrollo del backend y API',
                    'descripcion' => 'Desarrollar API RESTful con Laravel para gestión de contenidos y autenticación de usuarios',
                    'user_id' => $usuarios[4]->id, // Ana Martínez
                    'estado' => 'en_progreso',
                    'fecha_inicio' => now()->subMonths(2),
                    'fecha_fin' => now()->addMonth(),
                    'prioridad' => 3,
                    'progreso' => 70,
                ],
                [
                    'proyecto_id' => $proyecto1->id,
                    'nombre' => 'Integración de CMS',
                    'descripcion' => 'Integrar sistema de gestión de contenidos para que el cliente pueda administrar el sitio',
                    'user_id' => $usuarios[5]->id, // Luis Fernández
                    'estado' => 'pendiente',
                    'fecha_inicio' => now()->addWeeks(2),
                    'fecha_fin' => now()->addMonths(2),
                    'prioridad' => 2,
                    'progreso' => 0,
                ],
                [
                    'proyecto_id' => $proyecto1->id,
                    'nombre' => 'Optimización SEO',
                    'descripcion' => 'Implementar mejores prácticas de SEO: meta tags, sitemap, robots.txt, schema markup',
                    'user_id' => $usuarios[3]->id, // Carlos Rodríguez
                    'estado' => 'pendiente',
                    'fecha_inicio' => now()->addMonths(2),
                    'fecha_fin' => now()->addMonths(3),
                    'prioridad' => 2,
                    'progreso' => 0,
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
                    'nombre' => 'Análisis de requerimientos',
                    'descripcion' => 'Reuniones con stakeholders para recopilar y documentar todos los requerimientos del sistema',
                    'user_id' => $usuarios[1]->id, // Juan Pérez
                    'estado' => 'completada',
                    'fecha_inicio' => now()->subMonths(1),
                    'fecha_fin' => now()->subWeeks(3),
                    'prioridad' => 3,
                    'progreso' => 100,
                ],
                [
                    'proyecto_id' => $proyecto2->id,
                    'nombre' => 'Diseño de arquitectura del sistema',
                    'descripcion' => 'Definir arquitectura de microservicios, base de datos y diagramas UML',
                    'user_id' => $usuarios[4]->id, // Ana Martínez
                    'estado' => 'completada',
                    'fecha_inicio' => now()->subWeeks(3),
                    'fecha_fin' => now()->subWeeks(1),
                    'prioridad' => 3,
                    'progreso' => 100,
                ],
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
                [
                    'proyecto_id' => $proyecto2->id,
                    'nombre' => 'Módulo de Recursos Humanos',
                    'descripcion' => 'Desarrollar módulo de RRHH: empleados, nómina, vacaciones, evaluaciones',
                    'user_id' => $usuarios[3]->id, // Carlos Rodríguez
                    'estado' => 'pendiente',
                    'fecha_inicio' => now()->addMonths(5),
                    'fecha_fin' => now()->addMonths(8),
                    'prioridad' => 2,
                    'progreso' => 0,
                ],
                [
                    'proyecto_id' => $proyecto2->id,
                    'nombre' => 'Sistema de reportería y dashboards',
                    'descripcion' => 'Implementar sistema de reportes personalizables y dashboards interactivos con gráficos',
                    'user_id' => $usuarios[4]->id, // Ana Martínez
                    'estado' => 'pendiente',
                    'fecha_inicio' => now()->addMonths(8),
                    'fecha_fin' => now()->addMonths(10),
                    'prioridad' => 2,
                    'progreso' => 0,
                ],
            ];

            foreach ($tareas2 as $tarea) {
                Tarea::create($tarea);
            }
        }

        // Proyecto 3: Aplicación Móvil de Ventas
        $proyecto3 = $proyectos->where('nombre', 'Aplicación Móvil de Ventas')->first();
        if ($proyecto3) {
            $tareas3 = [
                [
                    'proyecto_id' => $proyecto3->id,
                    'nombre' => 'Configuración del proyecto React Native',
                    'descripcion' => 'Setup inicial del proyecto con React Native, configuración de dependencias y estructura de carpetas',
                    'user_id' => $usuarios[3]->id, // Carlos Rodríguez
                    'estado' => 'completada',
                    'fecha_inicio' => now()->subMonths(2),
                    'fecha_fin' => now()->subMonths(2)->addDays(3),
                    'prioridad' => 3,
                    'progreso' => 100,
                ],
                [
                    'proyecto_id' => $proyecto3->id,
                    'nombre' => 'Diseño UI/UX de la aplicación',
                    'descripcion' => 'Diseñar interfaz de usuario siguiendo Material Design y Human Interface Guidelines',
                    'user_id' => $usuarios[4]->id, // Ana Martínez (Desarrollador)
                    'estado' => 'completada',
                    'fecha_inicio' => now()->subMonths(2),
                    'fecha_fin' => now()->subMonth()->subWeeks(2),
                    'prioridad' => 3,
                    'progreso' => 100,
                ],
                [
                    'proyecto_id' => $proyecto3->id,
                    'nombre' => 'Módulo de autenticación',
                    'descripcion' => 'Implementar login, registro, recuperación de contraseña y autenticación biométrica',
                    'user_id' => $usuarios[5]->id, // Luis Fernández
                    'estado' => 'completada',
                    'fecha_inicio' => now()->subMonth()->subWeeks(2),
                    'fecha_fin' => now()->subWeeks(3),
                    'prioridad' => 3,
                    'progreso' => 100,
                ],
                [
                    'proyecto_id' => $proyecto3->id,
                    'nombre' => 'Gestión de clientes',
                    'descripcion' => 'Pantallas para crear, editar, buscar y visualizar información de clientes',
                    'user_id' => $usuarios[6]->id, // Laura Sánchez
                    'estado' => 'en_progreso',
                    'fecha_inicio' => now()->subWeeks(3),
                    'fecha_fin' => now()->addMonth(),
                    'prioridad' => 3,
                    'progreso' => 75,
                ],
                [
                    'proyecto_id' => $proyecto3->id,
                    'nombre' => 'Catálogo de productos',
                    'descripcion' => 'Implementar listado de productos con filtros, búsqueda y visualización de detalles',
                    'user_id' => $usuarios[3]->id, // Carlos Rodríguez
                    'estado' => 'en_progreso',
                    'fecha_inicio' => now()->subWeeks(2),
                    'fecha_fin' => now()->addMonths(2),
                    'prioridad' => 3,
                    'progreso' => 60,
                ],
                [
                    'proyecto_id' => $proyecto3->id,
                    'nombre' => 'Sistema de pedidos',
                    'descripcion' => 'Funcionalidad para crear, modificar y gestionar pedidos de venta',
                    'user_id' => $usuarios[5]->id, // Luis Fernández
                    'estado' => 'pendiente',
                    'fecha_inicio' => now()->addMonths(2),
                    'fecha_fin' => now()->addMonths(4),
                    'prioridad' => 3,
                    'progreso' => 0,
                ],
                [
                    'proyecto_id' => $proyecto3->id,
                    'nombre' => 'Sincronización offline',
                    'descripcion' => 'Implementar sistema de caché y sincronización para trabajar sin conexión',
                    'user_id' => $usuarios[4]->id, // Ana Martínez
                    'estado' => 'pendiente',
                    'fecha_inicio' => now()->addMonths(4),
                    'fecha_fin' => now()->addMonths(6),
                    'prioridad' => 2,
                    'progreso' => 0,
                ],
                [
                    'proyecto_id' => $proyecto3->id,
                    'nombre' => 'Testing y deployment',
                    'descripcion' => 'Pruebas en dispositivos reales y publicación en App Store y Google Play',
                    'user_id' => $usuarios[6]->id, // Laura Sánchez (Desarrollador)
                    'estado' => 'pendiente',
                    'fecha_inicio' => now()->addMonths(6),
                    'fecha_fin' => now()->addMonths(7),
                    'prioridad' => 3,
                    'progreso' => 0,
                ],
            ];

            foreach ($tareas3 as $tarea) {
                Tarea::create($tarea);
            }
        }

        // Proyecto 6: Dashboard Analítico BI (Completado)
        $proyecto6 = $proyectos->where('nombre', 'Dashboard Analítico BI')->first();
        if ($proyecto6) {
            $tareas6 = [
                [
                    'proyecto_id' => $proyecto6->id,
                    'nombre' => 'Conexión a fuentes de datos',
                    'descripcion' => 'Configurar conexiones a bases de datos, APIs y archivos CSV/Excel',
                    'user_id' => $usuarios[4]->id, // Ana Martínez
                    'estado' => 'completada',
                    'fecha_inicio' => now()->subMonths(4),
                    'fecha_fin' => now()->subMonths(3)->subWeeks(2),
                    'prioridad' => 3,
                    'progreso' => 100,
                ],
                [
                    'proyecto_id' => $proyecto6->id,
                    'nombre' => 'Diseño de KPIs',
                    'descripcion' => 'Definir y diseñar indicadores clave de rendimiento relevantes para el negocio',
                    'user_id' => $usuarios[1]->id, // Juan Pérez
                    'estado' => 'completada',
                    'fecha_inicio' => now()->subMonths(3)->subWeeks(2),
                    'fecha_fin' => now()->subMonths(3),
                    'prioridad' => 3,
                    'progreso' => 100,
                ],
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
                [
                    'proyecto_id' => $proyecto6->id,
                    'nombre' => 'Generación de reportes PDF',
                    'descripcion' => 'Implementar exportación de dashboards a PDF con programación automática',
                    'user_id' => $usuarios[6]->id, // Laura Sánchez
                    'estado' => 'completada',
                    'fecha_inicio' => now()->subMonth(),
                    'fecha_fin' => now()->subWeeks(2),
                    'prioridad' => 2,
                    'progreso' => 100,
                ],
            ];

            foreach ($tareas6 as $tarea) {
                Tarea::create($tarea);
            }
        }

        // Proyecto 7: API RESTful
        $proyecto7 = $proyectos->where('nombre', 'API RESTful para Integración')->first();
        if ($proyecto7) {
            $tareas7 = [
                [
                    'proyecto_id' => $proyecto7->id,
                    'nombre' => 'Diseño de endpoints',
                    'descripcion' => 'Definir estructura de URLs, métodos HTTP y respuestas de la API',
                    'user_id' => $usuarios[4]->id, // Ana Martínez
                    'estado' => 'completada',
                    'fecha_inicio' => now()->subMonths(2),
                    'fecha_fin' => now()->subMonth()->subWeeks(2),
                    'prioridad' => 3,
                    'progreso' => 100,
                ],
                [
                    'proyecto_id' => $proyecto7->id,
                    'nombre' => 'Implementación de autenticación OAuth2',
                    'descripcion' => 'Implementar sistema de autenticación con tokens JWT y refresh tokens',
                    'user_id' => $usuarios[5]->id, // Luis Fernández
                    'estado' => 'completada',
                    'fecha_inicio' => now()->subMonth()->subWeeks(2),
                    'fecha_fin' => now()->subWeeks(3),
                    'prioridad' => 3,
                    'progreso' => 100,
                ],
                [
                    'proyecto_id' => $proyecto7->id,
                    'nombre' => 'Desarrollo de endpoints CRUD',
                    'descripcion' => 'Crear endpoints para operaciones Create, Read, Update, Delete de todas las entidades',
                    'user_id' => $usuarios[3]->id, // Carlos Rodríguez
                    'estado' => 'en_progreso',
                    'fecha_inicio' => now()->subWeeks(3),
                    'fecha_fin' => now()->addWeeks(3),
                    'prioridad' => 3,
                    'progreso' => 80,
                ],
                [
                    'proyecto_id' => $proyecto7->id,
                    'nombre' => 'Documentación con Swagger/OpenAPI',
                    'descripcion' => 'Documentar toda la API usando especificación OpenAPI 3.0 y Swagger UI',
                    'user_id' => $usuarios[6]->id, // Laura Sánchez
                    'estado' => 'en_progreso',
                    'fecha_inicio' => now()->subWeeks(1),
                    'fecha_fin' => now()->addMonth(),
                    'prioridad' => 2,
                    'progreso' => 45,
                ],
                [
                    'proyecto_id' => $proyecto7->id,
                    'nombre' => 'Rate limiting y throttling',
                    'descripcion' => 'Implementar límites de tasa para prevenir abuso de la API',
                    'user_id' => $usuarios[4]->id, // Ana Martínez
                    'estado' => 'pendiente',
                    'fecha_inicio' => now()->addWeeks(3),
                    'fecha_fin' => now()->addMonth(),
                    'prioridad' => 2,
                    'progreso' => 0,
                ],
            ];

            foreach ($tareas7 as $tarea) {
                Tarea::create($tarea);
            }
        }

        $this->command->info('✓ Tareas creadas exitosamente para todos los proyectos');
    }
}
