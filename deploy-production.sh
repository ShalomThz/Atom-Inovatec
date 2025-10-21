#!/bin/bash

# Script de despliegue para Laravel Cloud
# Este script configura la base de datos y permisos después del despliegue

echo "🚀 Iniciando despliegue en producción..."
echo ""

# 1. Limpiar cachés
echo "📦 Limpiando cachés..."
php artisan optimize:clear --no-interaction
echo "✅ Cachés limpiados"
echo ""

# 2. Ejecutar migraciones y seeders
echo "🗄️  Ejecutando migraciones y seeders..."
php artisan migrate:fresh --seed --force --no-interaction
echo "✅ Base de datos configurada"
echo ""

# 3. Limpiar caché de permisos
echo "🔐 Limpiando caché de permisos..."
php artisan permission:cache-reset --no-interaction
echo "✅ Caché de permisos limpiado"
echo ""

# 4. Optimizar para producción
echo "⚡ Optimizando para producción..."
php artisan optimize --no-interaction
echo "✅ Optimización completada"
echo ""

echo "✅ Despliegue completado exitosamente!"
echo ""
echo "📧 Credenciales de acceso:"
echo "   Email: admin@atominovatec.com"
echo "   Password: password"
echo ""
