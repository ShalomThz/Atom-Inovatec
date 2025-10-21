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
        // PERMISOS GENERADOS POR SHIELD (Formato: action_model)
        // ============================================================

        // Shield genera automáticamente estos permisos, pero los creamos manualmente para tener control

        // Permisos de User
        $permisosUser = [
            'view_any_user',
            'view_user',
            'create_user',
            'update_user',
            'delete_user',
            'restore_user',
            'force_delete_user',
            'restore_any_user',
            'force_delete_any_user',
            'replicate_user',
            'reorder_user',
        ];

        // Permisos de Proyecto
        $permisosProyecto = [
            'view_any_proyecto',
            'view_proyecto',
            'create_proyecto',
            'update_proyecto',
            'delete_proyecto',
            'restore_proyecto',
            'force_delete_proyecto',
            'restore_any_proyecto',
            'force_delete_any_proyecto',
            'replicate_proyecto',
            'reorder_proyecto',
        ];

        // Permisos de Tarea
        $permisosTarea = [
            'view_any_tarea',
            'view_tarea',
            'create_tarea',
            'update_tarea',
            'delete_tarea',
            'restore_tarea',
            'force_delete_tarea',
            'restore_any_tarea',
            'force_delete_any_tarea',
            'replicate_tarea',
            'reorder_tarea',
        ];

        // Permisos de Role (para gestión de roles con Shield)
        $permisosRole = [
            'view_any_role',
            'view_role',
            'create_role',
            'update_role',
            'delete_role',
            'restore_role',
            'force_delete_role',
            'restore_any_role',
            'force_delete_any_role',
            'replicate_role',
            'reorder_role',
        ];

        // Permisos adicionales personalizados
        $permisosPersonalizados = [
            'ver_dashboard',
            'ver_tablero_kanban',
            'gestionar_tablero_kanban',
            'ver_reportes',
            'exportar_reportes',
            'gestionar_equipo',
        ];

        // Crear todos los permisos
        $todosLosPermisos = array_merge(
            $permisosUser,
            $permisosProyecto,
            $permisosTarea,
            $permisosRole,
            $permisosPersonalizados
        );

        foreach ($todosLosPermisos as $permiso) {
            Permission::create(['name' => $permiso, 'guard_name' => 'web']);
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
        $liderProyecto = Role::create(['name' => 'lider_proyecto', 'guard_name' => 'web']);
        $liderProyecto->givePermissionTo([
            // Proyectos - Control total
            'view_any_proyecto',
            'view_proyecto',
            'create_proyecto',
            'update_proyecto',
            'delete_proyecto',
            'restore_proyecto',
            'force_delete_proyecto',
            'restore_any_proyecto',
            'force_delete_any_proyecto',
            'replicate_proyecto',
            'reorder_proyecto',

            // Tareas - Control total
            'view_any_tarea',
            'view_tarea',
            'create_tarea',
            'update_tarea',
            'delete_tarea',
            'restore_tarea',
            'force_delete_tarea',
            'restore_any_tarea',
            'force_delete_any_tarea',
            'replicate_tarea',
            'reorder_tarea',

            // Usuarios - Solo visualizar
            'view_any_user',
            'view_user',

            // Permisos personalizados
            'ver_dashboard',
            'ver_tablero_kanban',
            'gestionar_tablero_kanban',
            'ver_reportes',
            'exportar_reportes',
            'gestionar_equipo',
        ]);
        $this->command->info('✓ Líder de Proyecto (control total del proyecto)');

        // ------------------------------------------------------------
        // ROL: DESARROLLADOR
        // ------------------------------------------------------------
        $desarrollador = Role::create(['name' => 'desarrollador', 'guard_name' => 'web']);
        $desarrollador->givePermissionTo([
            // Proyectos - Solo ver
            'view_any_proyecto',
            'view_proyecto',

            // Tareas - Ver y editar las propias
            'view_any_tarea',
            'view_tarea',
            'update_tarea',

            // Permisos personalizados
            'ver_dashboard',
            'ver_tablero_kanban',
        ]);
        $this->command->info('✓ Desarrollador (miembro del equipo)');

        $this->command->newLine();
        $this->command->info('✅ Sistema de roles y permisos creado exitosamente');
        $this->command->newLine();

        // Mostrar resumen
        $this->command->table(
            ['Rol', 'Permisos', 'Descripción'],
            [
                ['super_admin', Permission::count(), 'Control total del sistema'],
                ['lider_proyecto', $liderProyecto->permissions->count(), 'Responsable del proyecto completo'],
                ['desarrollador', $desarrollador->permissions->count(), 'Miembro del equipo de desarrollo'],
            ]
        );
    }
}
