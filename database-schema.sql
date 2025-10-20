-- ============================================================================
-- SCRIPT DE BASE DE DATOS - AtomInovatec
-- Sistema de Gestión de Proyectos
-- Motor: SQLite / MySQL / PostgreSQL Compatible
-- Autor: Sistema AtomInovatec
-- Fecha: 2025-10-19
-- ============================================================================

-- Configuración inicial
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- ============================================================================
-- ELIMINACIÓN DE TABLAS EXISTENTES (si existen)
-- ============================================================================

DROP TABLE IF EXISTS `tareas`;
DROP TABLE IF EXISTS `proyectos`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `cache_locks`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `job_batches`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `users`;

-- ============================================================================
-- TABLA: users
-- Descripción: Almacena la información de los usuarios del sistema
-- ============================================================================

CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL COMMENT 'Nombre completo del usuario',
    `email` VARCHAR(255) NOT NULL COMMENT 'Correo electrónico único',
    `email_verified_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Fecha de verificación del email',
    `password` VARCHAR(255) NOT NULL COMMENT 'Contraseña hasheada',
    `remember_token` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Token para sesión persistente',
    `two_factor_secret` TEXT NULL DEFAULT NULL COMMENT 'Secreto para autenticación de dos factores',
    `two_factor_recovery_codes` TEXT NULL DEFAULT NULL COMMENT 'Códigos de recuperación 2FA',
    `two_factor_confirmed_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Fecha de confirmación 2FA',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación del registro',
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de última actualización',

    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de usuarios del sistema';

-- ============================================================================
-- TABLA: password_reset_tokens
-- Descripción: Tokens para recuperación de contraseñas
-- ============================================================================

CREATE TABLE `password_reset_tokens` (
    `email` VARCHAR(255) NOT NULL COMMENT 'Email del usuario',
    `token` VARCHAR(255) NOT NULL COMMENT 'Token de recuperación',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación del token',

    PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tokens de recuperación de contraseña';

-- ============================================================================
-- TABLA: sessions
-- Descripción: Sesiones activas de usuarios
-- ============================================================================

CREATE TABLE `sessions` (
    `id` VARCHAR(255) NOT NULL COMMENT 'ID único de la sesión',
    `user_id` BIGINT UNSIGNED NULL DEFAULT NULL COMMENT 'ID del usuario (FK)',
    `ip_address` VARCHAR(45) NULL DEFAULT NULL COMMENT 'Dirección IP del usuario',
    `user_agent` TEXT NULL DEFAULT NULL COMMENT 'Información del navegador',
    `payload` LONGTEXT NOT NULL COMMENT 'Datos de la sesión',
    `last_activity` INT NOT NULL COMMENT 'Timestamp de última actividad',

    PRIMARY KEY (`id`),
    KEY `sessions_user_id_index` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sesiones de usuario';

-- ============================================================================
-- TABLA: cache
-- Descripción: Sistema de caché de Laravel
-- ============================================================================

CREATE TABLE `cache` (
    `key` VARCHAR(255) NOT NULL,
    `value` MEDIUMTEXT NOT NULL,
    `expiration` INT NOT NULL,

    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
    `key` VARCHAR(255) NOT NULL,
    `owner` VARCHAR(255) NOT NULL,
    `expiration` INT NOT NULL,

    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: jobs
-- Descripción: Cola de trabajos asíncronos
-- ============================================================================

CREATE TABLE `jobs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `queue` VARCHAR(255) NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `attempts` TINYINT UNSIGNED NOT NULL,
    `reserved_at` INT UNSIGNED NULL DEFAULT NULL,
    `available_at` INT UNSIGNED NOT NULL,
    `created_at` INT UNSIGNED NOT NULL,

    PRIMARY KEY (`id`),
    KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
    `id` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `total_jobs` INT NOT NULL,
    `pending_jobs` INT NOT NULL,
    `failed_jobs` INT NOT NULL,
    `failed_job_ids` LONGTEXT NOT NULL,
    `options` MEDIUMTEXT NULL,
    `cancelled_at` INT NULL,
    `created_at` INT NOT NULL,
    `finished_at` INT NULL,

    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: failed_jobs
-- Descripción: Trabajos fallidos
-- ============================================================================

CREATE TABLE `failed_jobs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` VARCHAR(255) NOT NULL,
    `connection` TEXT NOT NULL,
    `queue` TEXT NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `exception` LONGTEXT NOT NULL,
    `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: proyectos
-- Descripción: Proyectos del sistema de gestión
-- ============================================================================

CREATE TABLE `proyectos` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(255) NOT NULL COMMENT 'Nombre del proyecto',
    `descripcion` TEXT NULL DEFAULT NULL COMMENT 'Descripción detallada del proyecto',
    `user_id` BIGINT UNSIGNED NOT NULL COMMENT 'ID del usuario creador (FK → users)',
    `fecha_inicio` DATE NOT NULL COMMENT 'Fecha de inicio del proyecto',
    `fecha_fin` DATE NULL DEFAULT NULL COMMENT 'Fecha estimada de finalización',
    `estado` ENUM('pendiente', 'en_progreso', 'completado', 'cancelado') NOT NULL DEFAULT 'pendiente' COMMENT 'Estado actual del proyecto',
    `presupuesto` DECIMAL(10, 2) NULL DEFAULT NULL COMMENT 'Presupuesto asignado al proyecto',
    `prioridad` INT NOT NULL DEFAULT 1 COMMENT 'Prioridad: 1=baja, 2=media, 3=alta',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de actualización',

    PRIMARY KEY (`id`),
    KEY `proyectos_user_id_foreign` (`user_id`),
    KEY `proyectos_estado_index` (`estado`),
    KEY `proyectos_fecha_inicio_index` (`fecha_inicio`),
    KEY `proyectos_prioridad_index` (`prioridad`),

    CONSTRAINT `proyectos_user_id_foreign` FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT `proyectos_presupuesto_check` CHECK (`presupuesto` >= 0),
    CONSTRAINT `proyectos_fechas_check` CHECK (`fecha_fin` IS NULL OR `fecha_fin` >= `fecha_inicio`),
    CONSTRAINT `proyectos_prioridad_check` CHECK (`prioridad` >= 1 AND `prioridad` <= 3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Proyectos del sistema';

-- ============================================================================
-- TABLA: tareas
-- Descripción: Tareas asociadas a proyectos
-- ============================================================================

CREATE TABLE `tareas` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `proyecto_id` BIGINT UNSIGNED NOT NULL COMMENT 'ID del proyecto (FK → proyectos)',
    `nombre` VARCHAR(255) NOT NULL COMMENT 'Nombre de la tarea',
    `descripcion` TEXT NULL DEFAULT NULL COMMENT 'Descripción detallada de la tarea',
    `user_id` BIGINT UNSIGNED NOT NULL COMMENT 'ID del usuario asignado (FK → users)',
    `estado` ENUM('pendiente', 'en_progreso', 'completada', 'cancelada') NOT NULL DEFAULT 'pendiente' COMMENT 'Estado actual de la tarea',
    `fecha_inicio` DATE NULL DEFAULT NULL COMMENT 'Fecha de inicio de la tarea',
    `fecha_fin` DATE NULL DEFAULT NULL COMMENT 'Fecha límite de entrega',
    `prioridad` INT NOT NULL DEFAULT 1 COMMENT 'Prioridad: 1=baja, 2=media, 3=alta, 4=urgente',
    `progreso` INT NOT NULL DEFAULT 0 COMMENT 'Porcentaje de progreso (0-100)',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de actualización',

    PRIMARY KEY (`id`),
    KEY `tareas_proyecto_id_foreign` (`proyecto_id`),
    KEY `tareas_user_id_foreign` (`user_id`),
    KEY `tareas_estado_index` (`estado`),
    KEY `tareas_fecha_fin_index` (`fecha_fin`),
    KEY `tareas_prioridad_index` (`prioridad`),

    CONSTRAINT `tareas_proyecto_id_foreign` FOREIGN KEY (`proyecto_id`)
        REFERENCES `proyectos` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT `tareas_user_id_foreign` FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT `tareas_progreso_check` CHECK (`progreso` >= 0 AND `progreso` <= 100),
    CONSTRAINT `tareas_fechas_check` CHECK (`fecha_fin` IS NULL OR `fecha_inicio` IS NULL OR `fecha_fin` >= `fecha_inicio`),
    CONSTRAINT `tareas_prioridad_check` CHECK (`prioridad` >= 1 AND `prioridad` <= 4)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tareas de los proyectos';

-- ============================================================================
-- INSERCIÓN DE DATOS DE PRUEBA
-- ============================================================================

-- Insertar usuario administrador de prueba
INSERT INTO `users` (`name`, `email`, `password`, `email_verified_at`) VALUES
('Administrador', 'admin@atominovatec.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5NANh1VqF.wTe', NOW()),
('Juan Pérez', 'juan.perez@atominovatec.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5NANh1VqF.wTe', NOW()),
('María García', 'maria.garcia@atominovatec.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5NANh1VqF.wTe', NOW());

-- Insertar proyectos de prueba
INSERT INTO `proyectos` (`nombre`, `descripcion`, `user_id`, `fecha_inicio`, `fecha_fin`, `estado`, `presupuesto`, `prioridad`) VALUES
('Desarrollo Web Corporativo', 'Desarrollo del sitio web institucional de la empresa', 1, '2025-01-15', '2025-06-30', 'en_progreso', 50000.00, 3),
('Sistema de Gestión Interna', 'Implementación de sistema ERP para gestión de recursos', 1, '2025-02-01', '2025-12-31', 'pendiente', 120000.00, 3),
('Aplicación Móvil de Ventas', 'Desarrollo de app móvil para equipo de ventas', 2, '2025-03-01', '2025-09-30', 'en_progreso', 75000.00, 2);

-- Insertar tareas de prueba
INSERT INTO `tareas` (`proyecto_id`, `nombre`, `descripcion`, `user_id`, `estado`, `fecha_inicio`, `fecha_fin`, `prioridad`, `progreso`) VALUES
(1, 'Diseño de interfaz de usuario', 'Crear mockups y prototipos del sitio web', 2, 'completada', '2025-01-15', '2025-02-15', 3, 100),
(1, 'Desarrollo del backend', 'Implementar API REST y base de datos', 2, 'en_progreso', '2025-02-16', '2025-04-30', 3, 65),
(1, 'Integración de pasarela de pago', 'Implementar Stripe para procesamiento de pagos', 3, 'pendiente', '2025-04-01', '2025-05-15', 2, 0),
(2, 'Análisis de requerimientos', 'Reuniones con stakeholders y documentación', 3, 'completada', '2025-02-01', '2025-02-28', 3, 100),
(2, 'Selección de tecnología', 'Evaluar y seleccionar stack tecnológico', 2, 'en_progreso', '2025-03-01', '2025-03-31', 2, 40),
(3, 'Configuración del proyecto', 'Setup inicial de React Native', 2, 'completada', '2025-03-01', '2025-03-07', 1, 100),
(3, 'Módulo de autenticación', 'Implementar login y registro de usuarios', 3, 'en_progreso', '2025-03-08', '2025-04-15', 3, 75),
(3, 'Catálogo de productos', 'Pantallas de listado y detalle de productos', 3, 'pendiente', '2025-04-16', '2025-06-30', 2, 0);

-- ============================================================================
-- VISTAS ÚTILES
-- ============================================================================

-- Vista: Resumen de proyectos con estadísticas
CREATE OR REPLACE VIEW `v_proyectos_resumen` AS
SELECT
    p.id,
    p.nombre,
    p.estado,
    u.name AS creador,
    p.fecha_inicio,
    p.fecha_fin,
    p.presupuesto,
    p.prioridad,
    COUNT(t.id) AS total_tareas,
    SUM(CASE WHEN t.estado = 'completada' THEN 1 ELSE 0 END) AS tareas_completadas,
    SUM(CASE WHEN t.estado = 'en_progreso' THEN 1 ELSE 0 END) AS tareas_en_progreso,
    SUM(CASE WHEN t.estado = 'pendiente' THEN 1 ELSE 0 END) AS tareas_pendientes,
    ROUND(AVG(t.progreso), 2) AS progreso_promedio
FROM proyectos p
INNER JOIN users u ON p.user_id = u.id
LEFT JOIN tareas t ON p.id = t.proyecto_id
GROUP BY p.id, p.nombre, p.estado, u.name, p.fecha_inicio, p.fecha_fin, p.presupuesto, p.prioridad;

-- Vista: Tareas con información completa
CREATE OR REPLACE VIEW `v_tareas_completas` AS
SELECT
    t.id,
    t.nombre AS tarea,
    t.descripcion,
    p.nombre AS proyecto,
    u.name AS asignado,
    t.estado,
    t.prioridad,
    t.progreso,
    t.fecha_inicio,
    t.fecha_fin,
    CASE
        WHEN t.fecha_fin < CURDATE() AND t.estado != 'completada' THEN 'Retrasada'
        WHEN t.fecha_fin = CURDATE() AND t.estado != 'completada' THEN 'Vence hoy'
        ELSE 'A tiempo'
    END AS situacion,
    DATEDIFF(t.fecha_fin, CURDATE()) AS dias_restantes
FROM tareas t
INNER JOIN proyectos p ON t.proyecto_id = p.id
INNER JOIN users u ON t.user_id = u.id;

-- ============================================================================
-- PROCEDIMIENTOS ALMACENADOS
-- ============================================================================

DELIMITER $$

-- Procedimiento: Actualizar progreso de proyecto basado en tareas
CREATE PROCEDURE `sp_actualizar_progreso_proyecto`(IN p_proyecto_id BIGINT)
BEGIN
    DECLARE v_progreso_promedio INT;

    SELECT ROUND(AVG(progreso), 0)
    INTO v_progreso_promedio
    FROM tareas
    WHERE proyecto_id = p_proyecto_id;

    -- Actualizar estado del proyecto basado en progreso
    IF v_progreso_promedio = 100 THEN
        UPDATE proyectos
        SET estado = 'completado'
        WHERE id = p_proyecto_id;
    ELSEIF v_progreso_promedio > 0 THEN
        UPDATE proyectos
        SET estado = 'en_progreso'
        WHERE id = p_proyecto_id;
    END IF;
END$$

-- Procedimiento: Obtener estadísticas de usuario
CREATE PROCEDURE `sp_estadisticas_usuario`(IN p_user_id BIGINT)
BEGIN
    SELECT
        (SELECT COUNT(*) FROM proyectos WHERE user_id = p_user_id) AS proyectos_creados,
        (SELECT COUNT(*) FROM tareas WHERE user_id = p_user_id) AS tareas_asignadas,
        (SELECT COUNT(*) FROM tareas WHERE user_id = p_user_id AND estado = 'completada') AS tareas_completadas,
        (SELECT COUNT(*) FROM tareas WHERE user_id = p_user_id AND estado = 'en_progreso') AS tareas_en_progreso,
        (SELECT COUNT(*) FROM tareas WHERE user_id = p_user_id AND estado = 'pendiente') AS tareas_pendientes,
        (SELECT ROUND(AVG(progreso), 2) FROM tareas WHERE user_id = p_user_id) AS progreso_promedio;
END$$

DELIMITER ;

-- ============================================================================
-- TRIGGERS
-- ============================================================================

DELIMITER $$

-- Trigger: Actualizar progreso automáticamente al cambiar estado
CREATE TRIGGER `tr_tareas_estado_progreso_insert`
AFTER INSERT ON `tareas`
FOR EACH ROW
BEGIN
    IF NEW.estado = 'completada' THEN
        UPDATE tareas SET progreso = 100 WHERE id = NEW.id;
    ELSEIF NEW.estado = 'en_progreso' AND NEW.progreso = 0 THEN
        UPDATE tareas SET progreso = 10 WHERE id = NEW.id;
    END IF;
END$$

CREATE TRIGGER `tr_tareas_estado_progreso_update`
AFTER UPDATE ON `tareas`
FOR EACH ROW
BEGIN
    IF NEW.estado = 'completada' AND NEW.progreso != 100 THEN
        UPDATE tareas SET progreso = 100 WHERE id = NEW.id;
    ELSEIF NEW.estado = 'en_progreso' AND OLD.progreso = 0 AND NEW.progreso = 0 THEN
        UPDATE tareas SET progreso = 10 WHERE id = NEW.id;
    END IF;
END$$

DELIMITER ;

-- ============================================================================
-- CONFIGURACIÓN FINAL
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 1;

-- Mensaje de confirmación
SELECT 'Base de datos creada exitosamente. Sistema AtomInovatec listo para usar.' AS mensaje;
