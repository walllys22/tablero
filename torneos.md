# Torneos

Resumen de cambios realizados en el proyecto de torneos.

## Torneos

- Se agrego creacion de torneos desde modal en `resources/views/eventos/browse.blade.php`.
- Se agrego edicion de torneos desde modal en `resources/views/eventos/list.blade.php`.
- Se agrego boton celeste de editar con icono `bi-pencil-square`.
- Se agrego boton naranja para cambiar el estado del campeonato.
- Antes de desactivar un campeonato activo, se muestra un modal rojo con el mensaje:
  "Esta seguro de desactivar el campeonato".
- El estado `Activo` se muestra con fondo verde.
- El estado `Inactivo` se muestra con fondo rojo.
- Se corrigio la visualizacion del logo para mostrar imagen solo cuando el torneo tiene logo cargado.
- Se habilito el efecto de ampliar imagen al posicionar el mouse sobre el logo del campeonato.
- Se ejecuto `php artisan storage:link` para publicar los logos guardados en `storage/app/public`.

## Backend De Torneos

- Se agrego ruta `PATCH torneos/{torneo}` con nombre `torneos.update`.
- Se agrego ruta `PATCH torneos/{torneo}/estado` con nombre `torneos.toggle-status`.
- Se completo el metodo `update` en `app/Http/Controllers/TorneoController.php`.
- Se agrego el metodo `toggleStatus` para activar/desactivar solamente el estado.
- Se ajusto `app/Http/Requests/UpdateTorneoRequest.php` para validar nombre requerido y logo como imagen.

## Modalidades

- Se agrego boton de modalidades en acciones de cada torneo.
- Se creo modelo `app/Models/Modalidad.php`.
- Se agrego relacion `modalidades()` en `app/Models/Torneo.php`.
- Se creo controlador `app/Http/Controllers/ModalidadController.php`.
- Se creo migracion `database/migrations/2026_05_08_000001_create_modalidades_table.php`.
- Se creo seeder `database/seeders/ModalidadSeeder.php`.
- Se crearon vistas:
  - `resources/views/modalidades/browse.blade.php`
  - `resources/views/modalidades/list.blade.php`
- Se agregaron rutas:
  - `modalidades.index`
  - `modalidades.ajax.list`
- Se ingresaron datos de ejemplo:
  - Kumite Individual
  - Kumite Equipos
- El cierre de modalidades depende del origen:
  - Si se abre desde Torneos, vuelve a Torneos.
  - Si se abre desde el menu Modalidades, vuelve al dashboard.
- Se ajusto la relacion para que una modalidad pueda tener varias categorias.
- La modalidad se administra solo por nombre; el genero queda en cada categoria.
- El boton verde de categoria en cada modalidad abre el modal con la modalidad seleccionada.
- Se ajusto el seeder para crear una sola modalidad por nombre y asociarle sus categorias.
- Se elimino el campo `genero` del modelo y tabla `modalidades`.
- En el modal de crear categoria, el nombre de la modalidad elegida se muestra solo como referencia.
- Se simplifico `categorias` para usar solamente `id`, `torneo_id`, `modalidad_id`, `nombre`, `genero`, `edad_desde`, `edad_hasta` y `peso_hasta`.
- Se eliminaron `peso_desde`, `grado`, `orden`, `created_at` y `updated_at` de `categorias`.
- En categorias de modalidades con la palabra `Kata` no se solicita peso; el backend tambien ignora `peso_hasta` para esas modalidades.
- Para Kumite, `peso_hasta` queda como valor de referencia y la condicion de peso maximo/minimo se escribe en el nombre de la categoria.
- En el modal de categoria se agrego un selector visual para indicar si el peso es `menor o igual` o `mayor o igual`; no se guarda como columna, solo ayuda a formar el nombre.
- El genero seleccionado se agrega al nombre de la categoria para todas las modalidades.
- Se agrego migracion para normalizar categorias existentes y sumar el genero al nombre sin duplicarlo.
- El nombre de categoria ahora se genera con las opciones seleccionadas: texto base, edad, genero y peso cuando aplica.
- Se agrego migracion para regenerar nombres existentes desde `edad_desde`, `edad_hasta`, `genero` y `peso_hasta`, conservando `mayor o igual` cuando ya estaba escrito.
- El campo visible `Categoria` quedo en solo lectura; el sistema genera y guarda el nombre desde `nombre_base` y las opciones elegidas.
- Se elimino el campo `Nombre base` del modal de categoria.
- El nombre de categoria ahora se genera solo con edad, genero y peso cuando aplica.
- Se agrego validacion e indice unico para impedir categorias duplicadas dentro de la misma modalidad.
- Se consolidaron categorias duplicadas existentes, manteniendo una sola por `modalidad_id` y `nombre`.

## Dashboard Y Navegacion

- El dashboard quedo solo con la imagen `public/images/campeonato.png`.
- Se agrego `Modalidades` debajo de `Torneos` en el menu lateral, dentro de `Eventos`.
- Al hacer clic en `Modalidades`, se abre un modal con un select para elegir campeonato.
- Se quito el bloque de usuario del sidebar.
- El boton `Cerrar` de Torneos vuelve al dashboard.
- Se cambio el favicon del navegador para usar `public/images/icono.png`.

## Footer

- Se actualizo `resources/views/layouts/bottom-navigation.blade.php`.
- Se agrego texto de derechos reservados con Walter LandivarLimpias, anio actual e icono de WhatsApp.
- Se ajusto el alto visual del footer con texto pequeno y padding reducido.
- Se corrigio el footer para que en escritorio comience despues del sidebar y no se sobreponga.

## Estilos

- Se agrego `.label-danger` en `public/css/app.css`.
- Se agrego efecto hover/zoom a `.image-expandable`.
- Se ajusto `.bottom-bar` para respetar el ancho del sidebar en desktop.

## Comandos Ejecutados

- `php artisan migrate`
- `php artisan db:seed --class=ModalidadSeeder`
- `php artisan storage:link`
- `php artisan cache:clear`
- `php artisan config:clear`
- `php artisan route:clear`
- `php artisan view:clear`
- `php artisan event:clear`
- `php artisan test`

## Verificacion

- Las rutas de torneos y modalidades fueron verificadas con `php artisan route:list`.
- Las pruebas del proyecto pasaron correctamente con `php artisan test`.
