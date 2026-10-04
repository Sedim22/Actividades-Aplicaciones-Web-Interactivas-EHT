# Recetario casero

Aplicación en Laravel 12 para guardar recetas personales. La interfaz, las validaciones y los mensajes están en español. Cada usuario se registra con **nombre de usuario y contraseña, sin correo electrónico**, e inicia sesión para acceder a su propio recetario.

## Requisitos

- PHP 8.2 o superior compatible con Laravel 12, con las extensiones habituales de Laravel y `pdo_mysql` para MySQL (o `pdo_sqlite` para SQLite y las pruebas).
- Composer 2.
- Node.js 20.19+ o 22.12+ compatible con Vite 7 y npm.
- MySQL/MariaDB, por ejemplo mediante XAMPP; también se admite SQLite.

## Instalación

Desde la carpeta del proyecto:

```powershell
composer install
npm ci
```

En una instalación nueva, copia la configuración de ejemplo y genera la clave. Si ya existe un `.env` configurado, consérvalo y no vuelvas a generar una clave en una aplicación en uso.

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

### Base de datos MySQL / MariaDB

Inicia MySQL desde XAMPP y crea la base de datos mediante phpMyAdmin o un cliente SQL:

```sql
CREATE DATABASE recetario CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Configura en `.env` tus credenciales locales:

```dotenv
APP_NAME="Recetario casero"
APP_URL=http://127.0.0.1:8000
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=recetario
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
SESSION_DRIVER=database
```

Para usar SQLite, conserva `DB_CONNECTION=sqlite`, elimina o comenta las otras variables `DB_*` del ejemplo de MySQL y crea `database/database.sqlite` si todavía no existe:

```powershell
if (!(Test-Path database/database.sqlite)) { New-Item database/database.sqlite -ItemType File }
```

Aplica las migraciones y compila la interfaz:

```powershell
php artisan config:clear
php artisan migrate
npm run build
php artisan serve
```

Abre **http://127.0.0.1:8000**. Crea una cuenta desde «Crear cuenta» e inicia sesión. No se incluyen cuentas ni recetas de ejemplo.

Si ya habías aplicado las migraciones iniciales, `php artisan migrate` solo ejecutará las pendientes. La migración complementaria `2026_10_04_180000_add_unique_name_to_users_table` añade la restricción de nombre de usuario único. No es necesario reiniciar las tablas. Si una instalación previa contiene nombres duplicados, hay que resolverlos antes de aplicar esa restricción.

### Estilos durante el desarrollo

Después de cambiar vistas, estilos o JavaScript, ejecuta `npm run build` para actualizar `public/build`. Si no se compilan, el navegador puede mostrar una versión antigua o incompleta de los estilos.

También puedes dejar `npm run dev` abierto en otra terminal mientras trabajas. Para utilizar los archivos compilados, detén ese proceso. Si quedó un archivo `public/hot` de un proceso interrumpido, elimínalo únicamente después de confirmar que Vite está detenido y ejecuta `npm run build`. Recarga el navegador con **Ctrl + F5**.

## Acceso y rutas

Las rutas están definidas en `routes/web.php`:

| Método | Ruta | Acceso |
| --- | --- | --- |
| GET | `/` | Envía al login o al recetario según la sesión |
| GET / POST | `/login` | Visitantes: formulario e inicio de sesión |
| GET / POST | `/registro` | Visitantes: formulario y creación de cuenta |
| GET | `/recetas` | Usuario autenticado: listado, búsqueda y filtro |
| GET | `/recetas/create` | Usuario autenticado: formulario de receta |
| POST | `/recetas` | Usuario autenticado: guardar receta |
| GET | `/recetas/{receta}` | Propietario: detalle |
| GET | `/recetas/{receta}/edit` | Propietario: edición |
| PUT / PATCH | `/recetas/{receta}` | Propietario: actualizar |
| DELETE | `/recetas/{receta}` | Propietario: eliminar |
| POST | `/logout` | Usuario autenticado: cerrar sesión |

Todas las rutas privadas están dentro de `Route::middleware('auth')->group(...)`. El middleware comprueba la sesión; `bootstrap/app.php` transforma la excepción de autenticación en la vista `resources/views/errors/401.blade.php`, con código HTTP **401**. El navegador muestra «Acceso no autorizado» y un enlace al login. Las peticiones JSON reciben el mismo código y un mensaje en español.

Las consultas de recetas parten de `$request->user()->recetas()`. Intentar consultar, editar o eliminar una receta ajena devuelve **404** sin revelar su contenido. El propietario se toma de la sesión, nunca de un `user_id` enviado en el formulario.

El login renueva el identificador de sesión y limita los intentos fallidos. Cerrar sesión la invalida. Los formularios que modifican datos incluyen protección CSRF. La sesión se verifica antes del token en las rutas privadas: un visitante recibe 401, mientras que un usuario conectado que envía un formulario sin token válido recibe 419.

## Datos y validación

- **Título:** obligatorio, máximo 255 caracteres.
- **Categoría:** desayuno, almuerzo, cena, postre o bebida.
- **Tiempo:** entero positivo en minutos, dentro de la capacidad de la columna de la base de datos.
- **Dificultad:** fácil, media o difícil; se guardan los valores `facil`, `media` y `dificil`.
- **Ingredientes:** texto obligatorio, un ingrediente por línea, máximo 10 000 caracteres.
- **Pasos:** texto obligatorio, un paso por línea, máximo 15 000 caracteres.
- **Nota personal:** opcional, máximo 5 000 caracteres; corresponde a la columna `nota` de la migración.

Ingredientes y pasos se guardan en columnas de texto de `recetas`, sin tablas adicionales. En el detalle se muestran como listas, omitiendo las líneas vacías. Crear y editar comparten las mismas reglas. Los errores aparecen junto a sus campos y se conservan los datos introducidos para corregirlos.

El nombre de usuario admite de 3 a 50 caracteres: letras ASCII, números, guiones y guiones bajos. Debe ser único. La contraseña requiere al menos 8 caracteres y confirmación al registrarse. El registro lleva al login; después de iniciar sesión, la nueva cuenta encuentra su recetario vacío.

## Comprobaciones por fase

| Fase | Prueba | Resultado esperado |
| --- | --- | --- |
| 0 | Registrar una cuenta e iniciar sesión | Accede a un recetario propio vacío |
| 0 | Abrir `/recetas` sin sesión | Vista «Acceso no autorizado», HTTP 401 |
| 1 | Crear una receta válida | Aparece en el listado y se muestra confirmación de éxito |
| 1 | Enviar campos vacíos, tiempo cero o valores no permitidos | No guarda; muestra los errores correspondientes |
| 1 | Abrir el detalle con varios ingredientes y pasos | Ingredientes en lista y pasos numerados |
| 2 | Editar y guardar | Formulario precargado; cambios visibles y mensaje de éxito |
| 2 | Eliminar y cancelar la confirmación | La receta permanece |
| 2 | Eliminar y aceptar la confirmación | Desaparece y se muestra el mensaje de éxito |
| 3 | Buscar por título y filtrar por categoría | Solo aparecen las coincidencias de ambos criterios |
| 3 | Buscar un título inexistente | Mensaje sin resultados y opción de limpiar filtros |
| Aislamiento | Entrar con otra cuenta y probar la URL de la receta anterior | No aparece en su listado; el acceso directo devuelve 404 |
| Sesión | Cerrar sesión y volver a una URL privada | HTTP 401 con enlace al login |

La confirmación de eliminación utiliza un diálogo del navegador y requiere JavaScript. Para verificar errores del servidor que el navegador impide enviar, usa las pruebas automatizadas o desactiva temporalmente la validación HTML desde las herramientas del navegador.

### Pruebas automatizadas

```powershell
php artisan test
```

La configuración de `phpunit.xml` usa SQLite **en memoria** y no utiliza la base MySQL de trabajo. Las pruebas cubren registro, login, renovación y cierre de sesión, control de acceso, CSRF, validación, creación, consulta, edición, eliminación, búsqueda, filtros, paginación y aislamiento entre cuentas. La aceptación o cancelación del diálogo de eliminación se comprueba manualmente en el navegador.

Para preparar la entrega, comprueba también que compilen los recursos:

```powershell
npm run build
```

## Referencias

- [Autenticación de Laravel 12](https://laravel.com/docs/12.x/authentication)
- [Manejo de excepciones de Laravel 12](https://laravel.com/docs/12.x/errors)

Entrega esta carpeta o el enlace al repositorio junto con este README. Conserva `.env` fuera del repositorio; usa `.env.example` para documentar la configuración.
