# 📝 Guía: Ver y Registrar Auditorías en Tareas

## 🎯 Dónde Ver las Auditorías

Las auditorías ahora están **integradas directamente en el modal de edición de tareas**, no en un recurso separado.

### Pasos para ver/registrar auditoría:

1. **Ir a Tareas** → Panel de admin → Sección de Tareas
2. **Editar una tarea** → Hacer clic en el botón de edición
3. **Ver pestaña "Auditoría"** → En el formulario modal

---

## 📊 Vista de la Pestaña Auditoría

```
┌─────────────────────────────────────────────────────┐
│  Información General │ Asignación │ Fechas │ Auditoría│
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ Registrar Observación                               │
├─────────────────────────────────────────────────────┤
│ [Describe la auditoría o cambios detectados       ] │
│ [                                                   ] │
│ [                                                   ] │
└─────────────────────────────────────────────────────┘

┌──────────────────────┬──────────────────────────────┐
│ Estado               │ Notas de Implementación      │
├──────────────────────┼──────────────────────────────┤
│ [✅ Aprobado       ] │ [Cómo se implementó...      ] │
│ [⏳ Observado      ] │ [                            ] │
│ [❌ Rechazado      ] │ [                            ] │
└──────────────────────┴──────────────────────────────┘

 💾 GUARDAR
```

---

## 📋 Cómo Usar

### Crear una auditoría:

1. Abre el modal de edición de una tarea
2. Ve a la pestaña **"Auditoría"**
3. Llena los campos:
   - **Registrar Observación**: Describe qué se auditó (ej: "Se verificó la asignación correcta de la tarea")
   - **Estado**: Selecciona uno de:
     - ✅ **Aprobado**: Sin problemas encontrados
     - ⏳ **Observado**: Requiere acción (por defecto)
     - ❌ **Rechazado**: No procede
   - **Notas de Implementación**: Solo se muestra si el estado NO es "Observado". Aquí documentas cómo se resolvió.

4. Haz clic en **"Guardar"** (o **"Actualizar"**)

---

## 🔄 Flujo de Auditoría en Tareas

```
CREAR AUDITORÍA
    ↓
┌─────────────────────────┐
│ Estado: OBSERVADO ⏳     │  ← Requiere acción
│ Fecha: Hoy 20:15       │
│ Auditor: Tu nombre     │
│ Observaciones: ...     │
└─────────────────────────┘
    ↓
[Alguien corrige/implementa]
    ↓
ACTUALIZAR AUDITORÍA
    ↓
┌─────────────────────────┐
│ Estado: APROBADO ✅     │  ← Problema resuelto
│ Fecha: Hoy 20:15       │
│ Implementado: Hoy 21:00│
│ Notas: "Se hizo X..."  │
└─────────────────────────┘
```

---

## 📌 Historial de Auditorías

Debajo del formulario, verás un **historial de todas las auditorías registradas** para esa tarea:

```
📋 Auditorías Registradas

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
"Se verificó asignación correcta"    ✅ Aprobado
Auditor: admin@example.com
Fecha: 10/12/2025 20:03
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
"Requiere mejorar descripción"       ⏳ Observado
Auditor: usuario@example.com
Fecha: 09/12/2025 15:30
Implementado: Pendiente
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

---

## 🛠️ Ejemplos Prácticos

### Ejemplo 1: Auditoría de Verificación Simple

**Observación:** "Se verificó que la tarea está correctamente asignada y tiene fecha de inicio válida"
**Estado:** ✅ Aprobado
**Notas:** (no es necesario llenar)

**Resultado:** Auditoría registrada como aprobada inmediatamente.

---

### Ejemplo 2: Auditoría con Problema Detectado

**Observación:** "La descripción de la tarea es muy vaga. Se requiere más detalle sobre los requisitos"
**Estado:** ⏳ Observado
**Notas:** (vacío - pendiente de implementación)

**Resultado:** Auditoría queda en estado "Observado" esperando que alguien corrija la descripción.

---

### Ejemplo 3: Auditoría Resuelta

**Observación:** "La descripción de la tarea es muy vaga. Se requiere más detalle sobre los requisitos"
**Estado:** ✅ Aprobado
**Notas:** "Se actualizó la descripción con los requisitos específicos: debe incluir validación de emails, logs de errores y manejo de excepciones"

**Resultado:** Auditoría registrada como aprobada con fecha de implementación.

---

## 💡 Casos de Uso en el Proyecto

### Para Líder de Proyecto:
- Auditar asignación de tareas
- Verificar fechas y prioridades
- Revisar descripción y alcance

### Para Desarrollador:
- Registrar cambios en descripción
- Marcar problemas encontrados
- Documentar soluciones implementadas

### Para Admin:
- Auditoría general del sistema
- Validar permisos
- Revisar calidad de datos

---

## 🔗 Integración con la Base de Datos

Cuando registras una auditoría en el modal, se crea un registro en la tabla `audit_observaciones`:

```
Table: audit_observaciones
┌─────────────────────────────────────────┐
│ tipo: 'tarea'                           │
│ entidad_id: [ID de la tarea]            │
│ user_id: [Tu ID]                        │
│ observaciones: [Lo que escribiste]      │
│ estado: 'aprobado'/'observado'/...      │
│ fecha_auditoria: [Fecha/Hora ahora]     │
│ fecha_implementacion: [Cuando se aprobó]│
│ notas_implementacion: [Cómo se resolvió]│
└─────────────────────────────────────────┘
```

---

## ⚙️ API de Auditoría (Para Desarrolladores)

Si quieres programar auditorías desde código:

```php
use App\Models\AuditObservacion;

// Crear auditoría
AuditObservacion::create([
    'tipo' => 'tarea',
    'entidad_id' => $tarea->id,
    'user_id' => auth()->id(),
    'observaciones' => 'Verificación de calidad completada',
    'estado' => 'aprobado',
    'fecha_auditoria' => now(),
    'fecha_implementacion' => now(),
    'notas_implementacion' => 'Todos los requisitos cumplidos',
]);

// Ver auditorías de una tarea
$tarea->auditorias()->get();

// Ver auditorías pendientes
AuditObservacion::pendientesImplementacion()->get();
```

---

## ✅ Checklist de Implementación

- [x] Pestaña "Auditoría" agregada al modal de tareas
- [x] Relación entre Tarea y AuditObservacion creada
- [x] Guardado automático de auditorías en DB
- [x] Historial de auditorías visible en el modal
- [x] Estados (aprobado, observado, rechazado) funcionando
- [x] Fechas de implementación automáticas

---

**Versión:** 2.0  
**Fecha:** 2025-12-10  
**Estado:** ✅ Implementado en modal de tareas
