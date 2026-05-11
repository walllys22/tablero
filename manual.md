# Manual del Usuario - Sistema de Gestión de Torneos

Este manual describe las funcionalidades y reglas del sistema de gestión de torneos deportivos.

## 1. Gestión de Eventos (Torneos)

El módulo de torneos permite la administración centralizada de los eventos.

### 1.1. Creación y Edición
- **Creación:** Se realiza desde la vista principal mediante un botón que despliega un modal con datos de fecha, lugar, costos y sistema de competencia.
- **Edición:** Se utiliza el botón celeste con el icono de lápiz (`bi-pencil-square`).
- **Logos:** Es posible cargar una imagen representativa para cada torneo. Al posicionar el mouse sobre el logo en los listados, se activará un efecto de zoom para visualizarlo mejor.

### 1.2. Estados del Campeonato
El sistema maneja dos estados principales:
- **Activo (Verde):** El torneo está vigente y visible para inscripciones o gestión.
- **Inactivo (Rojo):** El torneo está suspendido o finalizado.

**Nota de seguridad:** Al intentar desactivar un campeonato activo, el sistema mostrará un mensaje de confirmación en un modal rojo: *"¿Está seguro de desactivar el campeonato?"*.

---

## 2. Modalidades y Categorías

Este es el núcleo técnico del sistema, diseñado para simplificar la creación de llaves de competencia.

### 2.1. Modalidades
Las modalidades representan el tipo de competencia (ej: *Kumite Individual*, *Kata Equipos*). Se administran por nombre y están vinculadas a un torneo específico.

### 2.2. Categorías (Generación Automática)
El sistema genera los nombres de las categorías automáticamente basándose en los parámetros seleccionados, garantizando consistencia.

- **Reglas de Nombre:** El nombre se construye uniendo: `[Prefijo Opcional] + [Rango de Edad] + [Género] + [Condición de Peso]`.
- **Prefijo:** Campo de texto libre para añadir niveles o tipos específicos (ej: "Open", "Principiante", "Grado B").
- **Edad:** Puede definirse como un rango ("10 a 12 años"), un mínimo ("desde 18 años") o un máximo ("hasta 17 años").
- **Género:** Se selecciona entre Masculino, Femenino o Mixto.
- **Peso (Solo Kumite):** 
    - Para modalidades de **Kumite**, se puede indicar un peso y seleccionar si es "menor o igual" o "mayor o igual".
    - Para modalidades de **Kata**, el campo de peso se ignora automáticamente y no se solicita en el formulario.
- **Validación de Duplicados:** El sistema impide crear dos categorías con el mismo nombre generado dentro de la misma modalidad.
- **Visualización de Datos:** En los listados (General y Detalle), se muestra de forma prominente el nombre amigable (formateado) y, debajo o al lado en texto tenue, el nombre técnico guardado en la base de datos para asegurar la integridad y facilitar la auditoría.

---

## 3. Inscripciones y Participantes

### 3.1. Organizaciones (Dojos/Clubes)
- Antes de inscribir competidores, se debe registrar la participación de la organización en el torneo.
- Se asigna un costo de inscripción de organización según las bases del torneo.

### 3.2. Competidores
- Se vinculan personas registradas en el sistema a una organización inscrita.
- Se asignan las modalidades y categorías de participación. El sistema valida que el competidor no esté duplicado en el mismo torneo.

---

## 4. Personal y Árbitros

- **Personas:** Directorio central de todos los involucrados. Incluye datos críticos como CI, fecha de nacimiento (para cálculo automático de categorías), género y tipo de sangre.
- **Árbitros:** Gestión de jueces para el evento. Permite registrar cargos, rangos y tipos de licencia (vía catálogo de licencias).

---

## 5. Reportes y Salida de Datos

### 5.1. Impresión de Modalidades
- Genera un documento PDF/Impreso optimizado con el logo del torneo y el listado de categorías agrupadas.
- Incluye metadatos de control: fecha de impresión, página y usuario responsable.

---

## 6. Navegación y Dashboard

- **Dashboard:** Al ingresar, se presenta una pantalla de bienvenida con la imagen oficial del campeonato.
- **Menú Lateral:** 
    - **Torneos:** Acceso a la lista de campeonatos.
    - **Modalidades:** Permite gestionar las modalidades de un torneo específico mediante un buscador/selector inicial.
- **Navegación Contextual:** Al crear modalidades o categorías, el sistema lo mantendrá en la misma vista actual (Lista o Detalle) para facilitar la carga masiva de datos sin interrupciones.
- **Botón Cerrar/Volver:** El sistema recuerda el origen del usuario. Si accede a modalidades desde un torneo, el botón "Cerrar" lo devolverá a la lista de torneos. Si accede desde el menú directo, lo devolverá al Dashboard.

---
## 7. Roles de Sistema

El acceso está restringido según el perfil del usuario:
1. **Admin:** Control total del sistema y configuración.
2. **Organizer:** Gestión de torneos, modalidades y categorías asignadas.
3. **User:** Usuario regular con permisos de lectura o participación según se configure.

---
## 8. Soporte y Pie de Página

En la parte inferior de la aplicación (footer), encontrará los derechos reservados y un acceso directo vía icono de WhatsApp para contactar con el soporte técnico (Walter Landivar Limpias).

---
*Última actualización: Mayo 2026*