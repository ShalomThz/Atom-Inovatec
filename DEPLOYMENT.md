# Guía de Despliegue - AtomInovatec

## 🚀 Despliegue en Laravel Cloud

### Opción 1: Script Automático

Ejecuta el script de despliegue en el terminal de Laravel Cloud:

```bash
bash deploy-production.sh
```

### Opción 2: Comandos Manuales

Si prefieres ejecutar los comandos manualmente:

```bash
# 1. Limpiar cachés
php artisan optimize:clear --no-interaction

# 2. Ejecutar migraciones y seeders
php artisan migrate:fresh --seed --force --no-interaction

# 3. Limpiar caché de permisos
php artisan permission:cache-reset --no-interaction

# 4. Optimizar para producción
php artisan optimize --no-interaction
```

## 📧 Credenciales de Acceso

Después del despliegue, puedes acceder con estas credenciales:

- **Email:** admin@atominovatec.com
- **Password:** password

## 👥 Usuarios de Prueba

El sistema crea 7 usuarios con diferentes roles:

### Super Admin (1 usuario)
- admin@atominovatec.com

### Líderes de Proyecto (2 usuarios)
- juan.perez@atominovatec.com
- maria.garcia@atominovatec.com

### Desarrolladores (4 usuarios)
- carlos.rodriguez@atominovatec.com
- ana.martinez@atominovatec.com
- luis.fernandez@atominovatec.com
- laura.sanchez@atominovatec.com

**Nota:** Todos los usuarios tienen la contraseña `password`

## 🔐 Sistema de Permisos

El sistema utiliza **FilamentShield** con los siguientes roles:

### super_admin
- 50 permisos
- Control total del sistema
- Acceso a todas las funcionalidades

### lider_proyecto
- 30 permisos
- Control total de proyectos y tareas
- Gestión de equipos
- Visualización de usuarios
- Acceso a reportes y tableros

### desarrollador
- 7 permisos
- Visualización de proyectos
- Edición de tareas asignadas
- Acceso al dashboard y tablero Kanban

## 📊 Datos de Prueba

El seeder crea:
- **7 usuarios** con roles asignados
- **10 proyectos** de ejemplo
- **30 tareas** distribuidas en los proyectos

## ⚠️ Importante

### Flags Obligatorias para Laravel Cloud

SIEMPRE usa estas flags cuando ejecutes comandos en Laravel Cloud:

- `--no-interaction`: Evita prompts interactivos
- `--force`: Ejecuta migraciones en producción sin confirmación

### Actualización de Configuración

Si modificas archivos de configuración (como `config/filament-shield.php`), asegúrate de ejecutar:

```bash
php artisan config:clear --no-interaction
php artisan config:cache --no-interaction
```

## 🔧 Solución de Problemas

### Error 403 Forbidden

Si encuentras un error 403 después del login:

1. Verifica que el usuario tenga un rol asignado
2. Limpia el caché de permisos:
   ```bash
   php artisan permission:cache-reset --no-interaction
   ```
3. Limpia todos los cachés:
   ```bash
   php artisan optimize:clear --no-interaction
   ```

### Error: "Required" en comandos

Esto indica que el comando está intentando hacer prompts interactivos. Añade `--no-interaction` al comando.

### Permisos no funcionan

1. Verifica la configuración en `config/filament-shield.php`:
   - `separator` debe ser `'_'`
   - `case` debe ser `'snake'`

2. Recrea la base de datos:
   ```bash
   php artisan migrate:fresh --seed --force --no-interaction
   php artisan permission:cache-reset --no-interaction
   ```

## 📝 Formato de Permisos

El sistema usa el formato de permisos de FilamentShield:

```
{action}_{model}
```

Ejemplos:
- `view_any_proyecto`
- `create_proyecto`
- `update_proyecto`
- `delete_proyecto`
- `view_any_tarea`
- `update_tarea`

## 🔄 Actualización Después de Cambios

Después de hacer cambios en el código y subirlos a Laravel Cloud:

```bash
# Actualizar dependencias (si es necesario)
composer install --no-dev --optimize-autoloader --no-interaction

# Limpiar y reconstruir cachés
php artisan optimize:clear --no-interaction
php artisan optimize --no-interaction

# Si cambiaste migraciones o seeders
php artisan migrate --force --no-interaction

# Si cambiaste permisos
php artisan permission:cache-reset --no-interaction
```

## 🌐 Acceso a la Aplicación

Después del despliegue, accede a tu aplicación en:

```
https://tu-proyecto.laravel.cloud/admin
```

Inicia sesión con las credenciales del super admin para empezar a usar el sistema.
