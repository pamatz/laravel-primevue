# Vue Testing Decision Record

## Contexto
Este proyecto utiliza Laravel + Inertia.js + Vue 3 + PrimeVue.
El backend ya cuenta con Feature Tests en Laravel que protegen:
- Autenticación y autorización
- Contratos Inertia (component + props)
- Flujos CRUD y validaciones

Se evaluó la conveniencia de agregar tests de Vue con Vitest y @vue/test-utils.
El objetivo **no es aumentar coverage**, sino proteger contratos frontend que el backend no puede garantizar.

---

## Decisión

Se **adoptan tests de Vue de forma selectiva y mínima**.

Los tests de Vue se escribirán **solo** para componentes reutilizables que contengan:
- Lógica condicional
- Transformación de datos
- Manejo de errores frontend
- Emisión de eventos críticos

Las páginas Inertia, layouts y flujos dominados por backend **no se testean con Vue**.

---

## Componentes APROBADOS para tests de Vue

### 1. `components/FormFieldMessage.vue` (ALTA prioridad)

**Razón**:
- Contiene lógica de prioridad de errores
- Maneja múltiples formatos (`string | array | field.error.message`)
- Afecta directamente UX crítico
- No está protegido por backend

**Contratos protegidos**:
- Prioridad de `field.error.message`
- Soporte de `error` como array o string
- Visibilidad correcta del mensaje
- Integración contractual con PrimeVue `Message`

---

### 2. `components/AppConfig.vue` (OPCIONAL)

**Razón**:
- Contiene interacción directa con `useLayout()`
- Clicks deben disparar `updateColors()` correctamente

**Nota**:
- Solo se testea comportamiento
- No se testea CSS ni render visual

---

## Componentes RECHAZADOS para tests de Vue

### Layouts

- `layouts/AuthLayout.vue`
- `layouts/AppLayout.vue`

**Motivo**:
- Principalmente layout
- Lógica mínima o dependiente de UI
- Mejor cubiertos por browser tests o revisión visual

---

### Pages Inertia

- `pages/Dashboard.vue`
- `pages/admin/*`
- `pages/auth/*`

**Motivo**:
- Lógica dominada por backend
- Contratos ya protegidos por `assertInertia()`
- Evitar duplicación de tests

---

## Estrategia global de testing

- **Laravel Feature Tests** → contratos backend, permisos, flujos
- **Vue Tests (Vitest)** → suposiciones frontend críticas
- **Browser Tests (Pest v4)** → flujos end-to-end críticos (ej. login)

Cada tipo de test cubre un riesgo distinto.

---

## Principios rectores

- No escribir tests para subir coverage
- No duplicar garantías del backend
- Preferir pocos tests con alto valor
- Si un test no falla al romper la feature, no debe existir

---

## Consecuencia

El proyecto tendrá:
- Menos tests de Vue
- Mayor confianza real
- Menor costo de mantenimiento
- Menos ruido en CI

Esta decisión es intencional y debe respetarse en futuros cambios.
