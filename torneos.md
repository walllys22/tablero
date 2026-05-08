# Proyecto Torneos - Memoria de Trabajo

## Contexto

Proyecto Laravel 10 ubicado en:

`E:\Desarrollo\torneos`

Stack usado por decision del usuario:

- Laravel 10
- PHP
- Bootstrap
- CSS
- JavaScript

Se evitaron Tailwind y Alpine para la interfaz principal.

## Credenciales de administrador

Usuario administrador creado para ingresar al sistema:

- Email: `admin@torneos.com`
- Password: `password123`

Tambien existe usuario de prueba:

- Email: `test@torneos.com`
- Password: `password123`

Seeders relevantes:

- `database/seeders/RoleSeeder.php`
- `database/seeders/TestUserSeeder.php`
- `database/seeders/DatabaseSeeder.php`

Para recrear usuarios y roles:

```bash
php artisan db:seed --class=DatabaseSeeder
```

## Base de datos y pruebas

La base de datos real del entorno local es MySQL:

- DB: `torneo`
- Usuario: `root`
- Password: vacio

Se corrigio `phpunit.xml` para que las pruebas usen SQLite en memoria:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

Esto evita que `RefreshDatabase` borre datos reales de MySQL al correr tests.

## Login

Archivo principal:

`resources/views/auth/login.blade.php`

Cambios realizados:

- Se agrego boton con icono de ojo para mostrar/ocultar contraseña.
- Se agrego soporte de scripts con `@stack('scripts')` en `resources/views/layouts/guest.blade.php`.
- Se limpio el comportamiento para que no borre los campos mientras el usuario escribe.
- El formulario tiene autocomplete reducido para evitar datos guardados del navegador.

## Logout

Archivo:

`app/Http/Controllers/Auth/AuthenticatedSessionController.php`

Al cerrar sesion ahora redirige al login:

```php
return redirect()->route('login');
```

Test actualizado:

`tests/Feature/Auth/AuthenticationTest.php`

## Layout interno

Archivo real usado por `<x-app-layout>`:

`resources/views/layouts/app.blade.php`

Importante: el componente PHP `app/View/Components/AppLayout.php` renderiza `layouts.app`, no `components.app-layout`.

Se agrego estructura interna:

- Navbar superior
- Sidebar lateral
- Bottom bar

Partials:

- `resources/views/layouts/navigation.blade.php`
- `resources/views/layouts/sidebar.blade.php`
- `resources/views/layouts/bottom-navigation.blade.php`

## Sidebar

Archivo:

`resources/views/layouts/sidebar.blade.php`

Opciones actuales:

- Dashboard
- Personas
- Tablero Kumite
- Tablero Kata
- Perfil

Tambien hay menu offcanvas para pantallas moviles.

## Bottom bar

Archivo:

`resources/views/layouts/bottom-navigation.blade.php`

Se dejo visible con:

```html
<nav class="bottom-bar" aria-label="Navegacion inferior">
</nav>
```

El CSS esta en:

`public/css/app.css`

Nota: revisar que el contenido del bottom bar no quede vacio si se edita este archivo. Debe contener links a dashboard, personas, kumite y kata.

## Dashboard

Archivo:

`resources/views/dashboard.blade.php`

Se agregaron tarjetas con imagenes reales del proyecto:

- `public/images/campeonato.png`
- `public/images/kumite.png`
- `public/images/kata.png`

## Imagenes

Ruta fisica:

`E:\Desarrollo\torneos\public\images`

Archivos existentes:

- `campeonato.png`
- `icono.png`
- `kata.png`
- `kumite.png`
- `tablero.png`

En Laravel se acceden sin `public`:

```blade
asset('images/icono.png')
asset('images/kumite.png')
asset('images/kata.png')
```

Se reemplazo el logo SVG de Laravel por `icono.png` en:

`resources/views/components/application-logo.blade.php`

Ahora `<x-application-logo />` usa:

```blade
<img src="{{ asset('images/icono.png') }}" ...>
```

## Personas

Archivos principales:

- `resources/views/people/browse.blade.php`
- `resources/views/people/list.blade.php`
- `app/Http/Controllers/PersonaController.php`

Se agrego ruta AJAX:

```php
Route::get('/people/ajax/list', [PersonaController::class, 'ajaxList'])->name('people.ajax.list');
```

Se corrigieron referencias a imagenes inexistentes:

- `images/default.jpg` -> `images/icono.png`
- `images/empty.png` -> `images/campeonato.png`

## Modelos y requests

Modelos ajustados:

- `app/Models/persona.php`
- `app/Models/Torneo.php`

Se agrego:

- `SoftDeletes`
- `$fillable`
- `$casts`

Requests ajustados:

- `StorepersonaRequest`
- `UpdatepersonaRequest`
- `StoreTorneoRequest`
- `UpdateTorneoRequest`

Se cambio `authorize()` a `true` y se agregaron reglas basicas.

## Tableros

Rutas:

- `tablero.kumite` -> `/kumite/tablero`
- `tablero.kata` -> `/kata/tablero`

Controlador:

`app/Http/Controllers/TableroController.php`

Vistas:

- `resources/views/kumite/tablero.blade.php`
- `resources/views/kata/tablero.blade.php`

Kata fue limpiado para usar Bootstrap/CSS propio en lugar de Tailwind CDN.

Kumite mantiene su logica grande, pero se corrigio HTML invalido en botones Hantei y se agregaron estilos equivalentes para no depender de Tailwind.

## Comandos utiles

Limpiar cache:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

Validar vistas:

```bash
php artisan view:cache
php artisan view:clear
```

Tests de autenticacion:

```bash
php artisan test --filter=AuthenticationTest
```

Levantar servidor local:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

URL local:

`http://127.0.0.1:8000`

## Pendientes sugeridos

- Revisar que `bottom-navigation.blade.php` conserve los links internos.
- Revisar visualmente dashboard en desktop y movil.
- Agregar CRUD real para personas si se requiere crear/editar/eliminar desde la interfaz.
- Separar JavaScript grande de Kumite en archivo propio si se va a seguir desarrollando.
