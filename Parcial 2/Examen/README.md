# Sistema de torneos

Aplicación de Laravel 12 con autenticación y roles, gestión administrativa y consulta pública de torneos, inscripciones de jugadores, cancelaciones y bajas de participantes. Las vistas utilizan Bootstrap 5.3.8 por CDN, por lo que el navegador necesita internet para cargar sus estilos y componentes. Estos módulos no requieren ejecutar Vite ni instalar dependencias de npm.

## Instalación local

Requisitos: PHP 8.2 o superior, Composer y MySQL o SQLite con su extensión de PHP habilitada.

1. Ejecutar `composer install`.
2. Si todavía no existe `.env`, copiar `.env.example` como `.env` y ejecutar `php artisan key:generate`.
3. Configurar `APP_URL` y la conexión de base de datos en `.env`. Para MySQL, crear la base de datos y configurar `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`. Para SQLite, usar `DB_CONNECTION=sqlite` y crear el archivo `database/database.sqlite` si no existe.
4. Ejecutar `php artisan migrate`.
5. Crear las cuentas demo con `php artisan db:seed --class=UsuariosDemoSeeder` (también se incluye en `php artisan db:seed`).
6. Ejecutar `php artisan serve` y abrir `http://127.0.0.1:8000`.

Las fechas se validan con la zona horaria `America/Mexico_City`. Se puede cambiar mediante `APP_TIMEZONE` en `.env`.

## Cuentas de prueba

| Rol | Correo | Contraseña |
| --- | --- | --- |
| Administrador | admin@torneos.test | Admin12345! |
| Jugador | jugador@torneos.test | Jugador12345! |

El administrador se crea mediante `database/seeders/UsuariosDemoSeeder.php`. Al repetir el seeder se conservan las cuentas que ya existen, incluidas sus contraseñas y roles. Estas credenciales son para pruebas locales.

El registro público crea únicamente jugadores: no admite elegir el rol y el servidor ignora cualquier rol enviado manualmente. Las contraseñas se guardan con hash mediante el cast del modelo `User`.

## Rutas y permisos

Todas las rutas de la aplicación están en `routes/web.php`. Laravel les aplica el grupo `web`, que incluye sesiones y protección CSRF. El alias `rol` del middleware `VerificarRol` está registrado en `bootstrap/app.php`. Se deshabilitaron las rutas automáticas de archivos privados y la ruta de salud del proyecto inicial para mantener las rutas HTTP centralizadas en `web.php`.

| Método | Ruta | Middleware de acceso | Uso |
| --- | --- | --- | --- |
| GET | `/` | `rol:guest,jugador,admin` | Inicio público |
| GET | `/torneos` | `rol:guest,jugador,admin` | Torneos disponibles por fecha próxima |
| GET | `/torneos/{torneo}` | `rol:guest,jugador,admin` | Datos y participantes de un torneo |
| GET / POST | `/registro` | `guest` | Formulario y registro de jugador |
| GET / POST | `/login` | `guest` | Formulario e inicio de sesión |
| POST | `/logout` | `auth` | Cierre de sesión |
| GET | `/panel` | `auth`, `rol:jugador,admin` | Redirección al panel correspondiente |
| GET | `/admin` | `auth`, `rol:admin` | Panel de administrador |
| GET | `/jugador` | `auth`, `rol:jugador` | Panel de jugador |
| GET | `/mis-torneos` | `auth`, `rol:jugador` | Inscripciones del jugador autenticado |
| POST | `/torneos/{torneo}/inscripciones` | `auth`, `rol:jugador` | Inscribirse en un torneo disponible |
| DELETE | `/torneos/{torneo}/inscripciones` | `auth`, `rol:jugador` | Cancelar la propia inscripción antes del evento |
| GET | `/admin/torneos` | `auth`, `rol:admin` | Listado administrativo de todos los torneos |
| GET | `/admin/torneos/crear` | `auth`, `rol:admin` | Formulario de creación |
| POST | `/admin/torneos` | `auth`, `rol:admin` | Guardar un torneo |
| GET | `/admin/torneos/{torneo}/editar` | `auth`, `rol:admin` | Formulario de edición |
| PUT | `/admin/torneos/{torneo}` | `auth`, `rol:admin` | Actualizar un torneo |
| DELETE | `/admin/torneos/{torneo}` | `auth`, `rol:admin` | Eliminar el torneo y sus inscripciones |
| GET | `/admin/torneos/{torneo}/inscripciones` | `auth`, `rol:admin` | Consultar inscritos del torneo |
| DELETE | `/admin/torneos/{torneo}/inscripciones/{inscripcion}` | `auth`, `rol:admin` | Dar de baja una inscripción del torneo |

Un invitado que intenta abrir un panel se redirige al login con un aviso en español. Un usuario que intenta acceder al panel de otro rol vuelve al inicio con un aviso. Si ya hay una sesión iniciada, los formularios de registro y login redirigen al panel.

El cierre de sesión utiliza POST con CSRF, invalida la sesión y regenera el token. El inicio y el registro regeneran el identificador de sesión. Después de cinco intentos fallidos de login para un correo y una IP, se bloquean nuevos intentos hasta que termine la ventana de un minuto, con aviso en español.

## Cómo probar el primer punto

1. Como invitado, visitar `/`, `/registro` y `/login`.
2. Registrar una cuenta: debe ingresar al panel de jugador y mostrar el rol Jugador.
3. Probar correo duplicado, campos vacíos, contraseña de menos de ocho caracteres y confirmación diferente. Deben aparecer errores en español junto a cada campo. Para probar las validaciones del servidor con campos vacíos, desactivar la validación del navegador o usar las pruebas automatizadas.
4. Cerrar sesión y comprobar que `/jugador` y `/admin` redirigen al login con un aviso.
5. Entrar con la cuenta demo del administrador: debe abrir `/admin`. Intentar `/jugador` y comprobar que se deniega el acceso.
6. Entrar con la cuenta demo del jugador: debe abrir `/jugador`. Intentar `/admin` y comprobar que se deniega el acceso.
7. Con sesión iniciada, visitar `/login` y `/registro`: deben redirigir al panel.
8. Probar una contraseña incorrecta: debe conservar el correo y mostrar el error sin iniciar sesión. Repetir cinco veces y comprobar el bloqueo temporal en el siguiente intento.
9. Cerrar sesión usando el botón de la barra de navegación. No debe ser posible volver a cargar el panel sin autenticarse.

## Cómo probar el segundo punto

1. Ejecutar `php artisan migrate` para crear la tabla `inscripciones` si todavía está pendiente.
2. Iniciar sesión como administrador y abrir **Gestionar torneos** desde el panel o la barra de navegación.
3. Crear un torneo con nombre, juego o deporte, fecha posterior a hoy, cupo entre 2 y 100 y estado abierto o cerrado. El formulario propone 16 plazas. La descripción es opcional y admite hasta 10,000 caracteres.
4. Probar nombre y deporte vacíos, fecha de hoy o pasada, cupos 1, 101 o decimales y estados no admitidos. Deben aparecer errores en español junto al campo correspondiente y conservarse los datos introducidos.
5. Editar un torneo y comprobar que se guardan nombre, tipo, fecha, cupo, descripción y estado. También se exige fecha futura al editar.
6. En el listado administrativo se muestran todos los torneos, incluidos los pasados, cerrados y llenos, ordenados por fecha y paginados de 10 en 10. Cada fila muestra inscritos, cupo, plazas libres y estado.
7. Pulsar **Eliminar** y comprobar la confirmación Bootstrap. **Cancelar** conserva el torneo; **Sí, eliminar torneo** borra el torneo y sus inscripciones, sin borrar jugadores ni afectar otros torneos.
8. Como jugador o invitado, intentar abrir las rutas administrativas: deben redirigir con un aviso. Tampoco pueden crear, actualizar ni eliminar mediante peticiones directas.

La tabla `inscripciones` incluye `torneo_id`, `user_id`, marcas de tiempo, una restricción única por torneo y jugador, y eliminación en cascada. Los modelos `Torneos`, `User` e `Inscripcion` tienen sus relaciones. Esta estructura permite comprobar el cupo mínimo, evitar duplicados y eliminar en cascada las inscripciones de un torneo.

La actualización cuenta las inscripciones dentro de una transacción que bloquea el torneo. No se permite reducir el cupo por debajo de ese número; sí se permite igualarlo, respetando siempre el mínimo de 2. Las pruebas automatizadas crean inscripciones temporales y verifican ambas situaciones, además de la eliminación en cascada.

## Cómo probar el tercer punto

1. Abrir **Torneos** desde la barra de navegación o visitar `/torneos`, incluso sin iniciar sesión.
2. Crear como administrador varios torneos abiertos con fechas futuras. El listado público debe mostrarlos del más próximo al más lejano, con su juego o deporte, fecha, inscritos y plazas libres. Se muestran 12 por página.
3. Cerrar uno de esos torneos desde su edición. Debe desaparecer del listado público, pero seguir accesible por `/torneos/{id}`. El botón **Ver** del listado administrativo permite abrir su detalle.
4. Abrir el detalle de un torneo. Debe mostrar nombre, juego o deporte, fecha, cupo, inscritos, plazas libres, descripción y nombres de sus participantes. No se publican correos ni contraseñas.
5. Si el torneo no tiene descripción o participantes, se muestra el mensaje correspondiente. Si no existe ningún torneo disponible, el listado muestra **No hay torneos disponibles**.
6. Los torneos llenos, cerrados manualmente, con fecha de hoy o pasada no aparecen en el listado público. Su detalle continúa accesible y explica su estado. Las pruebas automatizadas preparan estos casos y verifican el filtrado y el acceso directo.
7. Repetir la consulta como jugador y como administrador. Ambos pueden consultar; solo el administrador ve el enlace **Editar torneo**.

El modelo `Torneos` centraliza la consulta de disponibles y el cálculo de plazas y estado. El filtrado se realiza en la base de datos antes de paginar. Las fechas se consideran disponibles únicamente a partir de mañana, según `APP_TIMEZONE`.

## Cómo probar el cuarto punto

1. Iniciar sesión como jugador, abrir el detalle de un torneo disponible y pulsar **Inscribirme**. Debe mostrarse un mensaje de éxito, el estado **Inscrito** y el nombre del jugador en los participantes.
2. Abrir **Mis torneos** desde la navegación o el panel. Deben aparecer únicamente los torneos en los que está inscrita esa cuenta, incluidos los llenos, cerrados y pasados.
3. Repetir una petición de inscripción al mismo torneo: el servidor debe avisar **Ya estás inscrito en este torneo** y conservar una sola inscripción.
4. Crear un torneo de cupo 2 e inscribir dos jugadores distintos. Debe desaparecer del listado público. Una tercera cuenta que intente inscribirse mediante el enlace directo o una petición POST debe recibir el aviso de torneo lleno.
5. Intentar inscribirse en un torneo cerrado, con fecha de hoy o pasada: el servidor debe rechazarlo con su aviso correspondiente.
6. Desde el detalle o **Mis torneos**, pulsar **Cancelar inscripción** y confirmar. La inscripción debe desaparecer y liberar una plaza. Si el torneo tiene fecha futura y está abierto, vuelve al listado público al dejar de estar lleno.
7. La cancelación del jugador solo está permitida **antes de la fecha del evento**, según `APP_TIMEZONE`. Puede cancelar un torneo cerrado manualmente o lleno si aún tiene fecha futura. Desde el día del evento ya no puede cancelar, ni siquiera mediante una petición directa.
8. Cambiar de jugador y comprobar que no ve las inscripciones de otra cuenta en **Mis torneos**. En las operaciones se usa la identidad de la sesión; enviar un `user_id` o un identificador de inscripción ajeno no permite actuar como otra persona.
9. Los invitados y administradores no pueden usar las rutas de inscripción, cancelación ni **Mis torneos**. El administrador participa en este flujo mediante su gestión de inscritos.

## Cómo probar el quinto punto

1. Iniciar sesión como administrador. En **Gestionar torneos**, pulsar **Inscritos**. También se puede usar **Gestionar inscritos** desde el detalle público.
2. Comprobar el listado de nombres, correos y fechas de inscripción. Los correos solo se muestran en esta vista administrativa; el detalle público sigue mostrando únicamente nombres.
3. Pulsar **Dar de baja** y revisar la confirmación Bootstrap. **Cancelar** conserva la inscripción; **Sí, dar de baja** la elimina y libera la plaza, sin borrar al usuario ni al torneo.
4. El administrador puede dar de baja cualquier inscripción del torneo, incluso si el torneo está cerrado o su fecha ya llegó. Las otras inscripciones se conservan.
5. Como invitado o jugador, intentar abrir el listado administrativo o enviar una baja directamente: se debe redirigir con aviso y no modificar registros.
6. Intentar usar en la URL una inscripción que pertenece a otro torneo: debe responder 404 y conservar la inscripción.

Inscribirse, cancelar y dar de baja utilizan transacciones con bloqueo del torneo y reintentos ante conflictos de concurrencia. La disponibilidad se vuelve a comprobar dentro de la transacción y la base de datos conserva su restricción única de torneo y jugador. El mismo bloqueo se utiliza al editar el cupo. Todos los formularios que modifican datos incluyen CSRF; las bajas y cancelaciones usan DELETE mediante formularios POST, nunca enlaces GET. Los permisos por rol se verifican antes de resolver los identificadores de las rutas.

## Pruebas automatizadas

Ejecutar `php artisan test`. Las pruebas usan SQLite en memoria, sin modificar la base de datos de desarrollo; PHP necesita `pdo_sqlite` habilitado.

Para comprobar solo el segundo punto: `php artisan test --filter=TorneosAdminTest`.

Para comprobar solo el tercer punto: `php artisan test --filter=TorneosPublicosTest`.

Para comprobar los puntos 4 y 5: `php artisan test --filter=Inscripciones`.

## Alcance actual

Están implementados los puntos 1 a 5: autenticación, permisos por rol, CRUD administrativo, consulta pública de torneos, inscripciones del jugador, **Mis torneos**, cancelaciones y bajas por el administrador. Las vistas usan Bootstrap y los mensajes de estos flujos están en español.

Referencias: [autenticación de Laravel](https://laravel.com/docs/12.x/authentication), [middleware de Laravel](https://laravel.com/docs/12.x/middleware) y [Bootstrap](https://getbootstrap.com/docs/5.3/getting-started/introduction/).
