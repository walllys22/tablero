# Manual del Usuario - Sistema de Gestión de Torneos

Este manual describe las funcionalidades y reglas del sistema de gestión de torneos deportivos.

## 1. Gestión de Torneos

El módulo de torneos permite la administración centralizada de los eventos.

### 1.1. Creación y Edición
- **Creación:** Se realiza desde la vista principal de eventos mediante un botón que despliega un modal.
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

- **Reglas de Nombre:** El nombre se construye uniendo: `[Rango de Edad] + [Género] + [Condición de Peso]`.
- **Edad:** Puede definirse como un rango ("10 a 12 años"), un mínimo ("desde 18 años") o un máximo ("hasta 17 años").
- **Género:** Se selecciona entre Masculino, Femenino o Mixto.
- **Peso (Solo Kumite):** 
    - Para modalidades de **Kumite**, se puede indicar un peso y seleccionar si es "menor o igual" o "mayor o igual".
    - Para modalidades de **Kata**, el campo de peso se ignora automáticamente y no se solicita en el formulario.
- **Validación de Duplicados:** El sistema impide crear dos categorías con el mismo nombre generado dentro de la misma modalidad.

---

## 3. Navegación y Dashboard

- **Dashboard:** Al ingresar, se presenta una pantalla de bienvenida con la imagen oficial del campeonato.
- **Menú Lateral:** 
    - **Torneos:** Acceso a la lista de campeonatos.
    - **Modalidades:** Permite gestionar las modalidades de un torneo específico mediante un buscador/selector inicial.
- **Botón Cerrar/Volver:** El sistema está diseñado para recordar el origen del usuario. Si accede a modalidades desde un torneo, el botón "Cerrar" lo devolverá a la lista de torneos. Si accede desde el menú directo, lo devolverá al Dashboard.

---

## 4. Roles de Sistema

El acceso está restringido según el perfil del usuario:
1. **Admin:** Control total del sistema y configuración.
2. **Organizer:** Gestión de torneos, modalidades y categorías asignadas.
3. **User:** Usuario regular con permisos de lectura o participación según se configure.

---

## 5. Soporte y Pie de Página

En la parte inferior de la aplicación (footer), encontrará los derechos reservados y un acceso directo vía icono de WhatsApp para contactar con el soporte técnico (Walter Landivar Limpias).

---
*Última actualización: Mayo 2026*