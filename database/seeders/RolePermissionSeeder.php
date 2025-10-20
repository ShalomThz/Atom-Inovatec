<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('🔐 Creando permisos del sistema...');

        // ============================================================
        // PERMISOS DE USUARIOS
        // ============================================================
        $permisosUsuarios = [
            'ver_usuarios',
            'crear_usuarios',
            'editar_usuarios',
            'eliminar_usuarios',
            'asignar_roles',
        ];

        // ============================================================
        // PERMISOS DE PROYECTOS
        // ============================================================
        $permisosProyectos = [
            'ver_proyectos',
            'crear_proyectos',
            'editar_proyectos',
            'eliminar_proyectos',
            'ver_todos_proyectos', // Ver proyectos de otros
            'archivar_proyectos',
            'gestionar_presupuesto',
            'ver_reportes_proyectos',
        ];

        // ============================================================
        // PERMISOS DE TAREAS (KANBAN)
        // ============================================================
        $permisosTareas = [
            'ver_tareas',
            'crear_tareas',
            'editar_tareas',
            'eliminar_tareas',
            'ver_todas_tareas', // Ver tareas de otros
            'asignar_tareas',
            'cambiar_estado_tareas', // Mover en Kanban
            'cambiar_prioridad_tareas',
            'comentar_tareas',
            'completar_tareas',
        ];

        // ============================================================
        // PERMISOS DE TABLERO KANBAN
        // ============================================================
        $permisosKanban = [
            'ver_tablero_kanban',
            'gestionar_tablero_kanban',
            'crear_columnas_kanban',
            'eliminar_columnas_kanban',
            'mover_tareas_kanban',
        ];

        // ============================================================
        // PERMISOS DE SPRINT (SCRUM)
        // ============================================================
        $permisosSprint = [
            'ver_sprints',
            'crear_sprints',
            'editar_sprints',
            'eliminar_sprints',
            'iniciar_sprints',
            'finalizar_sprints',
            'gestionar_backlog',
        ];

        // ============================================================
        // PERMISOS DE REPORTES Y MÉTRICAS
        // ============================================================
        $permisosReportes = [
            'ver_dashboard',
            'ver_metricas',
            'exportar_reportes',
            'ver_analytics',
            'ver_burndown_chart',
            'ver_velocity_chart',
        ];

        // ============================================================
        // PERMISOS DE EQUIPO
        // ============================================================
        $permisosEquipo = [
            'ver_equipo',
            'gestionar_equipo',
            'asignar_miembros_proyecto',
            'remover_miembros_proyecto',
        ];

        // Crear todos los permisos
        $todosLosPermisos = array_merge(
            $permisosUsuarios,
            $permisosProyectos,
            $permisosTareas,
            $permisosKanban,
            $permisosSprint,
            $permisosReportes,
            $permisosEquipo
        );

        foreach ($todosLosPermisos as $permiso) {
            Permission::create(['name' => $permiso]);
        }

        $this->command->info('✓ ' . count($todosLosPermisos) . ' permisos creados');
        $this->command->newLine();

        // ============================================================
        // CREAR ROLES
        // ============================================================
        $this->command->info('👥 Creando roles del sistema...');

        // ------------------------------------------------------------
        // ROL: SUPER ADMIN
        // ------------------------------------------------------------
        $superAdmin = Role::create(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());
        $this->command->info('✓ Super Admin (todos los permisos)');

        // ------------------------------------------------------------
        // ROL: LÍDER DE PROYECTO (Project Owner/Manager)
        // ------------------------------------------------------------
        $liderProyecto = Role::create(['name' => 'lider_proyecto']);
        $liderProyecto->givePermissionTo([
            // Proyectos - Control total
            'ver_proyectos',
            'crear_proyectos',
            'editar_proyectos',
            'eliminar_proyectos',
            'archivar_proyectos',
            'gestionar_presupuesto',
            'ver_reportes_proyectos',

            // Tareas - Control total
            'ver_tareas',
            'crear_tareas',
            'editar_tareas',
            'eliminar_tareas',
            'ver_todas_tareas',
            'asignar_tareas',
            'cambiar_estado_tareas',
            'cambiar_prioridad_tareas',
            'comentar_tareas',
            'completar_tareas',

            // Kanban - Control total
            'ver_tablero_kanban',
            'gestionar_tablero_kanban',
            'crear_columnas_kanban',
            'eliminar_columnas_kanban',
            'mover_tareas_kanban',

            // Sprint - Control total
            'ver_sprints',
            'crear_sprints',
            'editar_sprints',
            'eliminar_sprints',
            'iniciar_sprints',
            'finalizar_sprints',
            'gestionar_backlog',

            // Equipo - Control total
            'ver_equipo',
            'gestionar_equipo',
            'asignar_miembros_proyecto',
            'remover_miembros_proyecto',

            // Reportes
            'ver_dashboard',
            'ver_metricas',
            'exportar_reportes',
            'ver_analytics',
            'ver_burndown_chart',
            'ver_velocity_chart',
        ]);
        $this->command->info('✓ Líder de Proyecto (control total del proyecto)');

        // ------------------------------------------------------------
        // ROL: SCRUM MASTER
        // ------------------------------------------------------------
        $scrumMaster = Role::create(['name' => 'scrum_master']);
        $scrumMaster->givePermissionTo([
            // Proyectos - Solo visualización y edición
            'ver_proyectos',
            'editar_proyectos',
            'ver_reportes_proyectos',

            // Tareas - Gestión completa
            'ver_tareas',
            'crear_tareas',
            'editar_tareas',
            'ver_todas_tareas',
            'asignar_tareas',
            'cambiar_estado_tareas',
            'cambiar_prioridad_tareas',
            'comentar_tareas',

            // Kanban - Gestión completa
            'ver_tablero_kanban',
            'gestionar_tablero_kanban',
            'mover_tareas_kanban',

            // Sprint - Gestión completa
            'ver_sprints',
            'crear_sprints',
            'editar_sprints',
            'iniciar_sprints',
            'finalizar_sprints',
            'gestionar_backlog',

            // Equipo
            'ver_equipo',
            'asignar_miembros_proyecto',

            // Reportes
            'ver_dashboard',
            'ver_metricas',
            'ver_burndown_chart',
            'ver_velocity_chart',
            'exportar_reportes',
        ]);
        $this->command->info('✓ Scrum Master (facilitador ágil)');

        // ------------------------------------------------------------
        // ROL: PRODUCT OWNER
        // ------------------------------------------------------------
        $productOwner = Role::create(['name' => 'product_owner']);
        $productOwner->givePermissionTo([
            // Proyectos
            'ver_proyectos',
            'editar_proyectos',
            'ver_reportes_proyectos',

            // Tareas - Énfasis en priorización
            'ver_tareas',
            'crear_tareas',
            'editar_tareas',
            'ver_todas_tareas',
            'cambiar_prioridad_tareas',
            'comentar_tareas',

            // Kanban
            'ver_tablero_kanban',
            'mover_tareas_kanban',

            // Sprint - Gestión de backlog
            'ver_sprints',
            'gestionar_backlog',

            // Equipo
            'ver_equipo',

            // Reportes
            'ver_dashboard',
            'ver_metricas',
            'ver_analytics',
            'ver_burndown_chart',
            'ver_velocity_chart',
        ]);
        $this->command->info('✓ Product Owner (dueño del producto)');

        // ------------------------------------------------------------
        // ROL: DESARROLLADOR (Developer)
        // ------------------------------------------------------------
        $desarrollador = Role::create(['name' => 'desarrollador']);
        $desarrollador->givePermissionTo([
            // Proyectos - Solo ver
            'ver_proyectos',

            // Tareas - Sus tareas asignadas
            'ver_tareas',
            'editar_tareas',
            'cambiar_estado_tareas',
            'comentar_tareas',
            'completar_tareas',

            // Kanban - Mover sus propias tareas
            'ver_tablero_kanban',
            'mover_tareas_kanban',

            // Sprint
            'ver_sprints',

            // Equipo
            'ver_equipo',

            // Reportes - Básico
            'ver_dashboard',
        ]);
        $this->command->info('✓ Desarrollador (miembro del equipo)');

        // ------------------------------------------------------------
        // ROL: DISEÑADOR
        // ------------------------------------------------------------
        $disenador = Role::create(['name' => 'disenador']);
        $disenador->givePermissionTo([
            // Proyectos
            'ver_proyectos',

            // Tareas
            'ver_tareas',
            'editar_tareas',
            'cambiar_estado_tareas',
            'comentar_tareas',
            'completar_tareas',

            // Kanban
            'ver_tablero_kanban',
            'mover_tareas_kanban',

            // Sprint
            'ver_sprints',

            // Equipo
            'ver_equipo',

            // Reportes
            'ver_dashboard',
        ]);
        $this->command->info('✓ Diseñador (diseño UI/UX)');

        // ------------------------------------------------------------
        // ROL: TESTER / QA
        // ------------------------------------------------------------
        $tester = Role::create(['name' => 'tester']);
        $tester->givePermissionTo([
            // Proyectos
            'ver_proyectos',

            // Tareas - Puede ver todas para testing
            'ver_tareas',
            'ver_todas_tareas',
            'editar_tareas',
            'comentar_tareas',
            'cambiar_estado_tareas',

            // Kanban
            'ver_tablero_kanban',
            'mover_tareas_kanban',

            // Sprint
            'ver_sprints',

            // Equipo
            'ver_equipo',

            // Reportes
            'ver_dashboard',
            'ver_metricas',
        ]);
        $this->command->info('✓ Tester/QA (control de calidad)');

        // ------------------------------------------------------------
        // ROL: STAKEHOLDER / CLIENTE
        // ------------------------------------------------------------
        $stakeholder = Role::create(['name' => 'stakeholder']);
        $stakeholder->givePermissionTo([
            // Proyectos - Solo visualización
            'ver_proyectos',
            'ver_reportes_proyectos',

            // Tareas - Solo visualización
            'ver_tareas',
            'comentar_tareas',

            // Kanban - Solo visualización
            'ver_tablero_kanban',

            // Sprint
            'ver_sprints',

            // Reportes - Vista completa
            'ver_dashboard',
            'ver_metricas',
            'ver_analytics',
            'ver_burndown_chart',
            'ver_velocity_chart',
            'exportar_reportes',
        ]);
        $this->command->info('✓ Stakeholder (interesado externo)');

        // ------------------------------------------------------------
        // ROL: OBSERVADOR
        // ------------------------------------------------------------
        $observador = Role::create(['name' => 'observador']);
        $observador->givePermissionTo([
            'ver_proyectos',
            'ver_tareas',
            'ver_tablero_kanban',
            'ver_equipo',
            'ver_dashboard',
        ]);
        $this->command->info('✓ Observador (solo lectura)');

        $this->command->newLine();
        $this->command->info('✅ Sistema de roles y permisos creado exitosamente');
        $this->command->newLine();

        // Mostrar resumen
        $this->command->table(
            ['Rol', 'Permisos', 'Descripción'],
            [
                ['Super Admin', Permission::count(), 'Control total del sistema'],
                ['Líder de Proyecto', $liderProyecto->permissions->count(), 'Responsable del proyecto completo'],
                ['Scrum Master', $scrumMaster->permissions->count(), 'Facilitador de la metodología ágil'],
                ['Product Owner', $productOwner->permissions->count(), 'Define prioridades y requisitos'],
                ['Desarrollador', $desarrollador->permissions->count(), 'Implementa funcionalidades'],
                ['Diseñador', $disenador->permissions->count(), 'Diseño UI/UX'],
                ['Tester/QA', $tester->permissions->count(), 'Control de calidad y testing'],
                ['Stakeholder', $stakeholder->permissions->count(), 'Cliente o interesado externo'],
                ['Observador', $observador->permissions->count(), 'Solo lectura del proyecto'],
            ]
        );
    }
}
