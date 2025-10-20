# Diagramas ER y UML - Sistema AtomInovatec

## 📊 Diagrama Entidad-Relación Completo

```mermaid
erDiagram
    USERS ||--o{ PROYECTOS : "crea"
    USERS ||--o{ TAREAS : "asignado_a"
    PROYECTOS ||--o{ TAREAS : "contiene"
    USERS ||--o{ SESSIONS : "tiene"
    USERS ||--o{ PASSWORD_RESET_TOKENS : "solicita"

    USERS {
        bigint id PK
        varchar name
        varchar email UK
        timestamp email_verified_at
        varchar password
        varchar remember_token
        text two_factor_secret
        text two_factor_recovery_codes
        timestamp two_factor_confirmed_at
        timestamp created_at
        timestamp updated_at
    }

    PROYECTOS {
        bigint id PK
        varchar nombre
        text descripcion
        bigint user_id FK
        date fecha_inicio
        date fecha_fin
        enum estado
        decimal presupuesto
        integer prioridad
        timestamp created_at
        timestamp updated_at
    }

    TAREAS {
        bigint id PK
        bigint proyecto_id FK
        varchar nombre
        text descripcion
        bigint user_id FK
        enum estado
        date fecha_inicio
        date fecha_fin
        integer prioridad
        integer progreso
        timestamp created_at
        timestamp updated_at
    }

    SESSIONS {
        varchar id PK
        bigint user_id FK
        varchar ip_address
        text user_agent
        longtext payload
        integer last_activity
    }

    PASSWORD_RESET_TOKENS {
        varchar email PK
        varchar token
        timestamp created_at
    }
```

---

## 🏗️ Diagrama UML de Clases (Modelo de Dominio)

```mermaid
classDiagram
    class User {
        -Long id
        -String name
        -String email
        -DateTime emailVerifiedAt
        -String password
        -String rememberToken
        -Text twoFactorSecret
        -Text twoFactorRecoveryCodes
        -DateTime twoFactorConfirmedAt
        -DateTime createdAt
        -DateTime updatedAt
        +proyectos() HasMany~Proyecto~
        +tareasAsignadas() HasMany~Tarea~
        +sessions() HasMany~Session~
        +verificarEmail() void
        +habilitarTwoFactor() void
        +deshabilitarTwoFactor() void
    }

    class Proyecto {
        -Long id
        -String nombre
        -Text descripcion
        -Long userId
        -Date fechaInicio
        -Date fechaFin
        -Estado estado
        -Decimal presupuesto
        -Integer prioridad
        -DateTime createdAt
        -DateTime updatedAt
        +creador() BelongsTo~User~
        +tareas() HasMany~Tarea~
        +calcularProgreso() Float
        +estaRetrasado() Boolean
        +completar() void
        +cancelar() void
    }

    class Tarea {
        -Long id
        -Long proyectoId
        -String nombre
        -Text descripcion
        -Long userId
        -EstadoTarea estado
        -Date fechaInicio
        -Date fechaFin
        -Integer prioridad
        -Integer progreso
        -DateTime createdAt
        -DateTime updatedAt
        +proyecto() BelongsTo~Proyecto~
        +asignado() BelongsTo~User~
        +actualizarProgreso(Integer) void
        +completar() void
        +estaRetrasada() Boolean
        +diasRestantes() Integer
    }

    class Session {
        -String id
        -Long userId
        -String ipAddress
        -Text userAgent
        -LongText payload
        -Integer lastActivity
        +user() BelongsTo~User~
        +esActiva() Boolean
    }

    class PasswordResetToken {
        -String email
        -String token
        -DateTime createdAt
        +esValido() Boolean
    }

    class Estado {
        <<enumeration>>
        PENDIENTE
        EN_PROGRESO
        COMPLETADO
        CANCELADO
    }

    class EstadoTarea {
        <<enumeration>>
        PENDIENTE
        EN_PROGRESO
        COMPLETADA
        CANCELADA
    }

    User "1" --> "0..*" Proyecto : crea
    User "1" --> "0..*" Tarea : asignado a
    Proyecto "1" --> "0..*" Tarea : contiene
    User "1" --> "0..*" Session : tiene

    Proyecto ..> Estado : usa
    Tarea ..> EstadoTarea : usa
```

---

## 🔄 Diagrama de Secuencia: Crear Proyecto

```mermaid
sequenceDiagram
    actor Usuario
    participant UI as Interfaz
    participant Controller as ProyectoController
    participant Model as Proyecto
    participant DB as Base de Datos
    participant Event as EventDispatcher

    Usuario->>UI: Crear Nuevo Proyecto
    UI->>Controller: POST /proyectos
    Controller->>Controller: Validar datos

    alt Datos válidos
        Controller->>Model: Proyecto::create(data)
        Model->>DB: INSERT INTO proyectos
        DB-->>Model: Proyecto creado
        Model-->>Controller: return Proyecto
        Controller->>Event: dispatch(ProyectoCreado)
        Controller-->>UI: 201 Created
        UI-->>Usuario: Proyecto creado exitosamente
    else Datos inválidos
        Controller-->>UI: 422 Validation Error
        UI-->>Usuario: Mostrar errores
    end
```

---

## 🔄 Diagrama de Secuencia: Actualizar Estado de Tarea (Kanban)

```mermaid
sequenceDiagram
    actor Usuario
    participant Kanban as KanbanBoard (React)
    participant API as Backend API
    participant Tarea as TareaModel
    participant DB as Base de Datos
    participant Trigger as DB Triggers

    Usuario->>Kanban: Arrastrar tarea a nueva columna
    Kanban->>Kanban: Actualizar UI localmente
    Kanban->>API: POST /kanban/update-tarea

    API->>API: Validar estado
    API->>Tarea: findOrFail(tareaId)
    Tarea->>DB: SELECT * FROM tareas WHERE id = ?
    DB-->>Tarea: return tarea

    Tarea->>Tarea: tarea.estado = nuevoEstado

    alt Estado = 'completada'
        Tarea->>Tarea: tarea.progreso = 100
    else Estado = 'en_progreso' AND progreso = 0
        Tarea->>Tarea: tarea.progreso = 10
    end

    Tarea->>DB: UPDATE tareas SET estado = ?
    DB->>Trigger: Ejecutar trigger
    Trigger->>DB: Validar y ajustar progreso
    DB-->>Tarea: Tarea actualizada
    Tarea-->>API: return success
    API-->>Kanban: 200 OK {success: true}
    Kanban-->>Usuario: Mostrar confirmación
```

---

## 📦 Diagrama de Componentes

```mermaid
graph TB
    subgraph "Frontend Layer"
        React[React Components]
        Kanban[Kanban Board]
        Forms[Filament Forms]
        Charts[Charts & Widgets]
    end

    subgraph "Application Layer"
        Routes[Laravel Routes]
        Controllers[Controllers]
        Resources[Filament Resources]
        Pages[Filament Pages]
    end

    subgraph "Business Logic Layer"
        Models[Eloquent Models]
        Services[Business Services]
        Events[Event System]
        Jobs[Queue Jobs]
    end

    subgraph "Data Layer"
        Eloquent[Eloquent ORM]
        Migrations[Migrations]
        Seeders[Seeders]
        DB[(Database)]
    end

    React --> Routes
    Kanban --> Routes
    Forms --> Resources
    Charts --> Pages

    Routes --> Controllers
    Resources --> Models
    Pages --> Controllers
    Controllers --> Models
    Controllers --> Services

    Models --> Eloquent
    Services --> Events
    Services --> Jobs

    Eloquent --> DB
    Migrations --> DB
    Seeders --> DB

    style DB fill:#4CAF50
    style React fill:#61DAFB
    style Models fill:#FF6B6B
```

---

## 🔐 Diagrama de Casos de Uso

```mermaid
graph LR
    subgraph Sistema
        UC1[Gestionar Proyectos]
        UC2[Gestionar Tareas]
        UC3[Asignar Tareas]
        UC4[Ver Tablero Kanban]
        UC5[Generar Reportes]
        UC6[Autenticación 2FA]
        UC7[Administrar Usuarios]
    end

    Admin((Administrador))
    User((Usuario))
    Guest((Invitado))

    Admin --> UC1
    Admin --> UC2
    Admin --> UC3
    Admin --> UC4
    Admin --> UC5
    Admin --> UC6
    Admin --> UC7

    User --> UC2
    User --> UC4
    User --> UC5
    User --> UC6

    Guest --> Login[Iniciar Sesión]
    Login --> UC6
```

---

## 🗄️ Diagrama de Despliegue

```mermaid
graph TB
    subgraph "Cliente"
        Browser[Navegador Web]
    end

    subgraph "Servidor Web"
        Nginx[Nginx/Apache]
        PHP[PHP 8.3]
        Laravel[Laravel 12]
        Vite[Vite Dev Server]
    end

    subgraph "Servicios"
        Queue[Laravel Queue]
        Cache[Redis Cache]
        Mail[Mail Service]
    end

    subgraph "Base de Datos"
        MySQL[(MySQL 8.0)]
        SQLite[(SQLite)]
    end

    Browser <--> Nginx
    Nginx <--> PHP
    PHP <--> Laravel
    Laravel <--> MySQL
    Laravel <--> SQLite
    Laravel <--> Queue
    Laravel <--> Cache
    Laravel <--> Mail
    Browser <--> Vite

    style Browser fill:#61DAFB
    style Laravel fill:#FF2D20
    style MySQL fill:#4479A1
    style SQLite fill:#003B57
```

---

## 📊 Diagrama de Estados: Proyecto

```mermaid
stateDiagram-v2
    [*] --> Pendiente
    Pendiente --> EnProgreso : Iniciar proyecto
    EnProgreso --> Completado : Todas las tareas completadas
    EnProgreso --> Cancelado : Cancelar proyecto
    Pendiente --> Cancelado : Cancelar antes de iniciar
    Completado --> [*]
    Cancelado --> [*]

    note right of Pendiente
        - Proyecto creado
        - Esperando inicio
    end note

    note right of EnProgreso
        - Al menos una tarea iniciada
        - Progreso > 0%
    end note

    note right of Completado
        - Todas las tareas completadas
        - Progreso = 100%
    end note
```

---

## 📊 Diagrama de Estados: Tarea

```mermaid
stateDiagram-v2
    [*] --> Pendiente
    Pendiente --> EnProgreso : Iniciar tarea
    EnProgreso --> Completada : Finalizar tarea
    EnProgreso --> Pendiente : Pausar tarea
    Pendiente --> Cancelada : Cancelar tarea
    EnProgreso --> Cancelada : Cancelar en progreso
    Completada --> [*]
    Cancelada --> [*]

    note right of Pendiente
        - Progreso = 0%
        - Esperando inicio
    end note

    note right of EnProgreso
        - Progreso: 1-99%
        - Tarea activa
    end note

    note right of Completada
        - Progreso = 100%
        - Tarea finalizada
    end note
```

---

## 🔗 Diagrama de Dependencias (Relaciones entre Modelos)

```
                    ┌─────────────┐
                    │    User     │
                    └──────┬──────┘
                           │
              ┌────────────┼────────────┐
              │                         │
              ▼                         ▼
     ┌────────────────┐        ┌──────────────┐
     │   Proyecto     │        │    Tarea     │
     │                │◄───────┤              │
     │ - user_id (FK) │  1:N   │ - user_id (FK)│
     └────────────────┘        │ - proyecto_id │
                               └──────────────┘

    Relaciones:
    ═══════════
    1. User → Proyecto (1:N)
       - Un usuario crea muchos proyectos
       - Cascade on delete

    2. User → Tarea (1:N)
       - Un usuario es asignado a muchas tareas
       - Cascade on delete

    3. Proyecto → Tarea (1:N)
       - Un proyecto contiene muchas tareas
       - Cascade on delete
```

---

## 📈 Normalización de la Base de Datos

### Análisis de Formas Normales

#### ✅ Primera Forma Normal (1NF)
```
Criterios:
├── ✓ Todos los atributos son atómicos
├── ✓ No hay grupos repetitivos
├── ✓ Cada tabla tiene clave primaria
└── ✓ Orden de filas no importa

Ejemplo de 1NF en tabla TAREAS:
┌────┬─────────────┬────────────┬───────┬─────────┐
│ ID │   Nombre    │ Proyecto   │Estado │Progreso │
├────┼─────────────┼────────────┼───────┼─────────┤
│ 1  │ Diseño UI   │ 1          │ comp. │  100    │
│ 2  │ Backend API │ 1          │ prog. │   65    │
└────┴─────────────┴────────────┴───────┴─────────┘
```

#### ✅ Segunda Forma Normal (2NF)
```
Criterios:
├── ✓ Cumple 1NF
├── ✓ Todos los atributos no-clave dependen
│      completamente de la clave primaria
└── ✓ No hay dependencias parciales

Dependencias Funcionales:
TAREAS:
  id → {nombre, descripcion, proyecto_id, user_id, estado, ...}
  NO existe: {id, proyecto_id} → atributo
```

#### ✅ Tercera Forma Normal (3NF)
```
Criterios:
├── ✓ Cumple 2NF
├── ✓ No hay dependencias transitivas
└── ✓ Atributos no-clave solo dependen de PK

Ejemplo de eliminación de dependencia transitiva:
❌ ANTES (No normalizado):
TAREAS: id, nombre, proyecto_id, proyecto_nombre

✅ DESPUÉS (Normalizado):
TAREAS: id, nombre, proyecto_id
PROYECTOS: id, nombre
```

---

## 🎯 Resumen de Diseño

### Características Principales:

1. **Integridad Referencial**
   - Todas las claves foráneas definidas
   - Cascade deletes configurados
   - Constraints CHECK implementados

2. **Normalización**
   - Base de datos en 3NF
   - Sin redundancia de datos
   - Optimizada para consultas

3. **Escalabilidad**
   - Índices en columnas frecuentes
   - Vistas para consultas complejas
   - Triggers para automatización

4. **Seguridad**
   - Contraseñas hasheadas
   - Autenticación de dos factores
   - Tokens de sesión seguros

---

## 📝 Notas Técnicas

### Convenciones de Nomenclatura:
- **Tablas**: Plural, snake_case (`proyectos`, `tareas`)
- **Columnas**: snake_case (`user_id`, `fecha_inicio`)
- **Claves Primarias**: `id` (BIGINT UNSIGNED AUTO_INCREMENT)
- **Claves Foráneas**: `{tabla}_id` (`proyecto_id`, `user_id`)
- **Timestamps**: `created_at`, `updated_at`
- **Soft Deletes**: `deleted_at` (no implementado actualmente)

### Tecnologías Utilizadas:
- **ORM**: Laravel Eloquent
- **Migraciones**: Laravel Migrations
- **Motor BD**: MySQL 8.0 / SQLite 3
- **Charset**: utf8mb4_unicode_ci
- **Motor de almacenamiento**: InnoDB
