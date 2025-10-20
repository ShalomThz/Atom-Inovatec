# Documentación de Base de Datos - AtomInovatec
## Sistema de Gestión de Proyectos

---

## 1. Diagrama Entidad-Relación (ER)

```
┌─────────────────────────────────────────────┐
│                   USERS                     │
├─────────────────────────────────────────────┤
│ PK  id: BIGINT                              │
│     name: VARCHAR(255)                      │
│ UQ  email: VARCHAR(255)                     │
│     email_verified_at: TIMESTAMP            │
│     password: VARCHAR(255)                  │
│     remember_token: VARCHAR(100)            │
│     two_factor_secret: TEXT                 │
│     two_factor_recovery_codes: TEXT         │
│     two_factor_confirmed_at: TIMESTAMP      │
│     created_at: TIMESTAMP                   │
│     updated_at: TIMESTAMP                   │
└─────────────────────────────────────────────┘
              │                    │
              │ 1                  │ 1
              │                    │
              │ crea               │ es asignado a
              │                    │
              │ N                  │ N
              ▼                    ▼
┌─────────────────────────────────────────────┐
│                 PROYECTOS                   │
├─────────────────────────────────────────────┤
│ PK  id: BIGINT                              │
│     nombre: VARCHAR(255)                    │
│     descripcion: TEXT                       │
│ FK  user_id: BIGINT                         │
│     fecha_inicio: DATE                      │
│     fecha_fin: DATE                         │
│     estado: ENUM                            │
│     presupuesto: DECIMAL(10,2)              │
│     prioridad: INTEGER                      │
│     created_at: TIMESTAMP                   │
│     updated_at: TIMESTAMP                   │
└─────────────────────────────────────────────┘
              │
              │ 1
              │
              │ contiene
              │
              │ N
              ▼
┌─────────────────────────────────────────────┐
│                  TAREAS                     │
├─────────────────────────────────────────────┤
│ PK  id: BIGINT                              │
│ FK  proyecto_id: BIGINT                     │
│     nombre: VARCHAR(255)                    │
│     descripcion: TEXT                       │
│ FK  user_id: BIGINT                         │
│     estado: ENUM                            │
│     fecha_inicio: DATE                      │
│     fecha_fin: DATE                         │
│     prioridad: INTEGER                      │
│     progreso: INTEGER                       │
│     created_at: TIMESTAMP                   │
│     updated_at: TIMESTAMP                   │
└─────────────────────────────────────────────┘


┌─────────────────────────────────────────────┐
│         PASSWORD_RESET_TOKENS               │
├─────────────────────────────────────────────┤
│ PK  email: VARCHAR(255)                     │
│     token: VARCHAR(255)                     │
│     created_at: TIMESTAMP                   │
└─────────────────────────────────────────────┘


┌─────────────────────────────────────────────┐
│                 SESSIONS                    │
├─────────────────────────────────────────────┤
│ PK  id: VARCHAR(255)                        │
│ FK  user_id: BIGINT                         │
│     ip_address: VARCHAR(45)                 │
│     user_agent: TEXT                        │
│     payload: LONGTEXT                       │
│ IDX last_activity: INTEGER                  │
└─────────────────────────────────────────────┘
```

---

## 2. Diagrama UML Relacional Normalizado

```
╔═══════════════════════════════════════════════╗
║                    User                       ║
╠═══════════════════════════════════════════════╣
║ - id: Long <<PK>>                             ║
║ - name: String                                ║
║ - email: String <<UQ>>                        ║
║ - email_verified_at: DateTime                 ║
║ - password: String                            ║
║ - remember_token: String                      ║
║ - two_factor_secret: Text                     ║
║ - two_factor_recovery_codes: Text             ║
║ - two_factor_confirmed_at: DateTime           ║
║ - created_at: DateTime                        ║
║ - updated_at: DateTime                        ║
╠═══════════════════════════════════════════════╣
║ + proyectos(): HasMany<Proyecto>              ║
║ + tareasAsignadas(): HasMany<Tarea>           ║
║ + sessions(): HasMany<Session>                ║
╚═══════════════════════════════════════════════╝
                    △ 1
                    │
                    │ creates
                    │
                    │ N
╔═══════════════════════════════════════════════╗
║                  Proyecto                     ║
╠═══════════════════════════════════════════════╣
║ - id: Long <<PK>>                             ║
║ - nombre: String                              ║
║ - descripcion: Text                           ║
║ - user_id: Long <<FK>>                        ║
║ - fecha_inicio: Date                          ║
║ - fecha_fin: Date                             ║
║ - estado: Enum                                ║
║   ['pendiente','en_progreso',                 ║
║    'completado','cancelado']                  ║
║ - presupuesto: Decimal(10,2)                  ║
║ - prioridad: Integer                          ║
║   [1=baja, 2=media, 3=alta]                   ║
║ - created_at: DateTime                        ║
║ - updated_at: DateTime                        ║
╠═══════════════════════════════════════════════╣
║ + creador(): BelongsTo<User>                  ║
║ + tareas(): HasMany<Tarea>                    ║
╚═══════════════════════════════════════════════╝
                    △ 1
                    │
                    │ contains
                    │
                    │ N
╔═══════════════════════════════════════════════╗
║                    Tarea                      ║
╠═══════════════════════════════════════════════╣
║ - id: Long <<PK>>                             ║
║ - proyecto_id: Long <<FK>>                    ║
║ - nombre: String                              ║
║ - descripcion: Text                           ║
║ - user_id: Long <<FK>>                        ║
║ - estado: Enum                                ║
║   ['pendiente','en_progreso',                 ║
║    'completada','cancelada']                  ║
║ - fecha_inicio: Date                          ║
║ - fecha_fin: Date                             ║
║ - prioridad: Integer                          ║
║   [1=baja, 2=media, 3=alta, 4=urgente]        ║
║ - progreso: Integer [0-100]                   ║
║ - created_at: DateTime                        ║
║ - updated_at: DateTime                        ║
╠═══════════════════════════════════════════════╣
║ + proyecto(): BelongsTo<Proyecto>             ║
║ + asignado(): BelongsTo<User>                 ║
╚═══════════════════════════════════════════════╝
```

---

## 3. Relaciones del Sistema

### Cardinalidad:

1. **User → Proyecto** (1:N)
   - Un usuario puede crear múltiples proyectos
   - Un proyecto pertenece a un único creador

2. **User → Tarea** (1:N)
   - Un usuario puede tener asignadas múltiples tareas
   - Una tarea puede ser asignada a un único usuario

3. **Proyecto → Tarea** (1:N)
   - Un proyecto puede contener múltiples tareas
   - Una tarea pertenece a un único proyecto

---

## 4. Niveles de Normalización

### ✅ Primera Forma Normal (1NF)
- Todas las columnas contienen valores atómicos
- No hay grupos repetitivos
- Cada tabla tiene una clave primaria única

### ✅ Segunda Forma Normal (2NF)
- Cumple 1NF
- Todos los atributos no clave dependen completamente de la clave primaria
- No hay dependencias parciales

### ✅ Tercera Forma Normal (3NF)
- Cumple 2NF
- No hay dependencias transitivas
- Todos los atributos no clave dependen únicamente de la clave primaria

### Análisis de Dependencias:

**Tabla USERS:**
- Clave primaria: `id`
- Dependencias funcionales: `id → {name, email, password, ...}`
- ✅ Normalizada en 3NF

**Tabla PROYECTOS:**
- Clave primaria: `id`
- Clave foránea: `user_id`
- Dependencias funcionales: `id → {nombre, descripcion, fecha_inicio, ...}`
- ✅ Normalizada en 3NF

**Tabla TAREAS:**
- Clave primaria: `id`
- Claves foráneas: `proyecto_id`, `user_id`
- Dependencias funcionales: `id → {nombre, descripcion, estado, ...}`
- ✅ Normalizada en 3NF

---

## 5. Restricciones de Integridad

### Restricciones de Clave Primaria:
- `users.id` BIGINT AUTO_INCREMENT
- `proyectos.id` BIGINT AUTO_INCREMENT
- `tareas.id` BIGINT AUTO_INCREMENT
- `password_reset_tokens.email` VARCHAR(255)
- `sessions.id` VARCHAR(255)

### Restricciones de Clave Foránea:
- `proyectos.user_id` → `users.id` (ON DELETE: CASCADE)
- `tareas.proyecto_id` → `proyectos.id` (ON DELETE: CASCADE)
- `tareas.user_id` → `users.id` (ON DELETE: SET NULL)
- `sessions.user_id` → `users.id` (ON DELETE: CASCADE)

### Restricciones de Unicidad:
- `users.email` UNIQUE

### Restricciones de Dominio:
- `proyectos.estado` ∈ {'pendiente', 'en_progreso', 'completado', 'cancelado'}
- `tareas.estado` ∈ {'pendiente', 'en_progreso', 'completada', 'cancelada'}
- `proyectos.prioridad` ∈ {1, 2, 3}
- `tareas.prioridad` ∈ {1, 2, 3, 4}
- `tareas.progreso` ∈ [0, 100]
- `proyectos.presupuesto` DECIMAL(10,2) >= 0

### Restricciones CHECK:
```sql
CHECK (tareas.progreso >= 0 AND tareas.progreso <= 100)
CHECK (proyectos.presupuesto >= 0)
CHECK (proyectos.fecha_fin IS NULL OR proyectos.fecha_fin >= proyectos.fecha_inicio)
CHECK (tareas.fecha_fin IS NULL OR tareas.fecha_fin >= tareas.fecha_inicio)
```

---

## 6. Índices del Sistema

```sql
-- Índices principales
PRIMARY KEY (id) ON users
PRIMARY KEY (id) ON proyectos
PRIMARY KEY (id) ON tareas
PRIMARY KEY (email) ON password_reset_tokens
PRIMARY KEY (id) ON sessions

-- Índices únicos
UNIQUE INDEX users_email_unique ON users(email)

-- Índices de claves foráneas
INDEX proyectos_user_id_foreign ON proyectos(user_id)
INDEX tareas_proyecto_id_foreign ON tareas(proyecto_id)
INDEX tareas_user_id_foreign ON tareas(user_id)
INDEX sessions_user_id_index ON sessions(user_id)

-- Índices de búsqueda
INDEX sessions_last_activity_index ON sessions(last_activity)
INDEX proyectos_estado_index ON proyectos(estado)
INDEX tareas_estado_index ON tareas(estado)
INDEX proyectos_fecha_inicio_index ON proyectos(fecha_inicio)
INDEX tareas_fecha_fin_index ON tareas(fecha_fin)
```

---

## 7. Diccionario de Datos

### Tabla: USERS

| Campo | Tipo | Null | Default | Descripción |
|-------|------|------|---------|-------------|
| id | BIGINT | NO | AUTO | Identificador único del usuario |
| name | VARCHAR(255) | NO | - | Nombre completo del usuario |
| email | VARCHAR(255) | NO | - | Correo electrónico (único) |
| email_verified_at | TIMESTAMP | YES | NULL | Fecha de verificación del email |
| password | VARCHAR(255) | NO | - | Contraseña hasheada |
| remember_token | VARCHAR(100) | YES | NULL | Token para "Recordarme" |
| two_factor_secret | TEXT | YES | NULL | Secreto 2FA |
| two_factor_recovery_codes | TEXT | YES | NULL | Códigos de recuperación 2FA |
| two_factor_confirmed_at | TIMESTAMP | YES | NULL | Confirmación 2FA |
| created_at | TIMESTAMP | YES | CURRENT | Fecha de creación |
| updated_at | TIMESTAMP | YES | CURRENT | Fecha de actualización |

### Tabla: PROYECTOS

| Campo | Tipo | Null | Default | Descripción |
|-------|------|------|---------|-------------|
| id | BIGINT | NO | AUTO | Identificador único del proyecto |
| nombre | VARCHAR(255) | NO | - | Nombre del proyecto |
| descripcion | TEXT | YES | NULL | Descripción detallada |
| user_id | BIGINT | NO | - | ID del creador (FK → users) |
| fecha_inicio | DATE | NO | - | Fecha de inicio del proyecto |
| fecha_fin | DATE | YES | NULL | Fecha de finalización |
| estado | ENUM | NO | 'pendiente' | Estado actual del proyecto |
| presupuesto | DECIMAL(10,2) | YES | NULL | Presupuesto asignado |
| prioridad | INTEGER | NO | 1 | Nivel de prioridad (1-3) |
| created_at | TIMESTAMP | YES | CURRENT | Fecha de creación |
| updated_at | TIMESTAMP | YES | CURRENT | Fecha de actualización |

### Tabla: TAREAS

| Campo | Tipo | Null | Default | Descripción |
|-------|------|------|---------|-------------|
| id | BIGINT | NO | AUTO | Identificador único de la tarea |
| proyecto_id | BIGINT | NO | - | ID del proyecto (FK → proyectos) |
| nombre | VARCHAR(255) | NO | - | Nombre de la tarea |
| descripcion | TEXT | YES | NULL | Descripción detallada |
| user_id | BIGINT | NO | - | ID del asignado (FK → users) |
| estado | ENUM | NO | 'pendiente' | Estado actual de la tarea |
| fecha_inicio | DATE | YES | NULL | Fecha de inicio de la tarea |
| fecha_fin | DATE | YES | NULL | Fecha límite de entrega |
| prioridad | INTEGER | NO | 1 | Nivel de prioridad (1-4) |
| progreso | INTEGER | NO | 0 | Porcentaje de avance (0-100) |
| created_at | TIMESTAMP | YES | CURRENT | Fecha de creación |
| updated_at | TIMESTAMP | YES | CURRENT | Fecha de actualización |

---

## 8. Reglas de Negocio

1. **Usuarios:**
   - Cada usuario debe tener un email único
   - La autenticación de dos factores es opcional
   - Los usuarios pueden crear múltiples proyectos

2. **Proyectos:**
   - Cada proyecto debe tener un creador (user_id)
   - La fecha de fin debe ser posterior a la fecha de inicio
   - El presupuesto debe ser mayor o igual a 0
   - Los estados válidos son: pendiente, en_progreso, completado, cancelado
   - La prioridad va de 1 (baja) a 3 (alta)

3. **Tareas:**
   - Cada tarea debe pertenecer a un proyecto
   - Cada tarea debe estar asignada a un usuario
   - El progreso debe estar entre 0 y 100
   - Si el estado es 'completada', el progreso debe ser 100
   - Si el estado es 'en_progreso' y el progreso es 0, se establece automáticamente en 10
   - Los estados válidos son: pendiente, en_progreso, completada, cancelada
   - La prioridad va de 1 (baja) a 4 (urgente)

4. **Integridad Referencial:**
   - Al eliminar un usuario, sus proyectos se eliminan (CASCADE)
   - Al eliminar un proyecto, sus tareas se eliminan (CASCADE)
   - Al eliminar un usuario asignado a tareas, el campo user_id se establece en NULL

---

## 9. Consultas SQL Comunes

### Obtener proyectos con sus tareas y usuarios asignados
```sql
SELECT
    p.id AS proyecto_id,
    p.nombre AS proyecto_nombre,
    p.estado AS proyecto_estado,
    u_creador.name AS creador,
    COUNT(t.id) AS total_tareas,
    AVG(t.progreso) AS progreso_promedio
FROM proyectos p
INNER JOIN users u_creador ON p.user_id = u_creador.id
LEFT JOIN tareas t ON p.id = t.proyecto_id
GROUP BY p.id, p.nombre, p.estado, u_creador.name
ORDER BY p.created_at DESC;
```

### Obtener tareas pendientes por usuario
```sql
SELECT
    u.name AS usuario,
    t.nombre AS tarea,
    p.nombre AS proyecto,
    t.estado,
    t.prioridad,
    t.fecha_fin
FROM tareas t
INNER JOIN users u ON t.user_id = u.id
INNER JOIN proyectos p ON t.proyecto_id = p.id
WHERE t.estado IN ('pendiente', 'en_progreso')
ORDER BY t.prioridad DESC, t.fecha_fin ASC;
```

### Estadísticas de proyectos por estado
```sql
SELECT
    estado,
    COUNT(*) AS total_proyectos,
    AVG(presupuesto) AS presupuesto_promedio
FROM proyectos
GROUP BY estado;
```

### Tareas con mayor retraso
```sql
SELECT
    t.nombre,
    p.nombre AS proyecto,
    u.name AS asignado,
    t.fecha_fin,
    DATEDIFF(CURRENT_DATE, t.fecha_fin) AS dias_retraso
FROM tareas t
INNER JOIN proyectos p ON t.proyecto_id = p.id
INNER JOIN users u ON t.user_id = u.id
WHERE t.estado != 'completada'
  AND t.fecha_fin < CURRENT_DATE
ORDER BY dias_retraso DESC;
```
