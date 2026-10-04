<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('login y registro son públicos y no solicitan correo', function () {
    foreach (['/login', '/registro'] as $ruta) {
        $this->get($ruta)->assertOk()->assertSee('Nombre de usuario')->assertDontSee('type="email"', false);
    }
});

test('el registro crea una cuenta sin correo y solicita iniciar sesión', function () {
    $this->post('/registro', [
        'name' => 'cocinera',
        'password' => 'clave-segura',
        'password_confirmation' => 'clave-segura',
    ])->assertRedirect(route('login'))->assertSessionHas('success');

    $usuario = User::sole();
    expect($usuario->name)->toBe('cocinera');
    expect(Hash::check('clave-segura', $usuario->password))->toBeTrue();
    expect($usuario->recetas()->count())->toBe(0);
    $this->assertGuest();

    $this->post('/login', ['name' => 'cocinera', 'password' => 'clave-segura'])
        ->assertRedirect(route('recetas.index'));
    $this->get('/recetas')->assertOk()->assertSee('Aún no tienes recetas.');
});

test('el registro valida nombres únicos y confirmación de contraseña', function () {
    User::factory()->create(['name' => 'cocinero']);

    $this->from('/registro')->post('/registro', [
        'name' => 'cocinero',
        'password' => 'clave-segura',
        'password_confirmation' => 'otra-clave',
    ])->assertRedirect('/registro')->assertSessionHasErrors(['name', 'password']);

    $this->get('/registro')->assertSee('ya está registrado')->assertSee('no coincide');
    $this->assertDatabaseCount('users', 1);
    $this->assertGuest();
});

test('el registro rechaza nombres inválidos y contraseñas cortas', function () {
    $this->post('/registro', [
        'name' => 'con espacios', 'password' => '123', 'password_confirmation' => '123',
    ])->assertSessionHasErrors(['name', 'password']);
    $this->assertDatabaseCount('users', 0);
});

test('el login autentica por nombre y contraseña y renueva la sesión', function () {
    $usuario = User::factory()->create();
    $this->withSession(['dato' => 'conservado']);
    $sesionAnterior = session()->getId();

    $this->post('/login', ['name' => $usuario->name, 'password' => 'password'])
        ->assertRedirect(route('recetas.index'));

    $this->assertAuthenticatedAs($usuario);
    expect(session()->getId())->not->toBe($sesionAnterior);
    $this->get('/')->assertRedirect(route('recetas.index'));
});

test('credenciales incorrectas no abren una sesión', function () {
    $usuario = User::factory()->create();

    $this->from('/login')->post('/login', ['name' => $usuario->name, 'password' => 'incorrecta'])
        ->assertRedirect('/login')->assertSessionHasErrors('name');

    $this->assertGuest();
    $this->get('/login')->assertSee('El nombre de usuario o la contraseña son incorrectos.');
    expect(session()->getOldInput('password'))->toBeNull();
});

test('el login limita intentos repetidos fallidos', function () {
    $usuario = User::factory()->create();
    for ($intento = 0; $intento < 5; $intento++) {
        $this->post('/login', ['name' => $usuario->name, 'password' => 'incorrecta']);
    }

    $this->post('/login', ['name' => $usuario->name, 'password' => 'password'])
        ->assertSessionHasErrors('name');
    expect(session('errors')->first('name'))->toContain('Demasiados intentos');
    $this->assertGuest();
});

test('las rutas privadas muestran la vista 401 sin sesión', function (string $metodo, string $ruta) {
    $this->{$metodo}($ruta)->assertStatus(401)->assertViewIs('errors.401')
        ->assertSee('Acceso no autorizado')->assertSee('Iniciar sesión');
})->with([
    ['get', '/recetas'],
    ['get', '/recetas/create'],
    ['get', '/recetas/999'],
    ['get', '/recetas/999/edit'],
    ['post', '/recetas'],
    ['put', '/recetas/999'],
    ['patch', '/recetas/999'],
    ['delete', '/recetas/999'],
    ['post', '/logout'],
]);

test('las peticiones JSON sin sesión también reciben 401', function () {
    $this->getJson('/recetas')->assertUnauthorized()
        ->assertJson(['message' => 'Acceso no autorizado. Debes iniciar sesión.']);
});

test('la sesión se comprueba antes del token CSRF en rutas privadas', function () {
    // Activar la comprobación CSRF que Laravel omite normalmente durante las pruebas.
    $this->app['env'] = 'local';
    $this->post('/recetas')->assertUnauthorized()->assertViewIs('errors.401');
    $this->delete('/recetas/999')->assertUnauthorized();

    $this->actingAs(User::factory()->create())->post('/recetas')
        ->assertStatus(419)->assertSee('El formulario ha caducado');
});

test('usuarios autenticados no pueden volver al login o al registro', function () {
    $this->actingAs(User::factory()->create());
    $this->get('/login')->assertRedirect(route('recetas.index'));
    $this->get('/registro')->assertRedirect(route('recetas.index'));
});

test('cerrar sesión impide volver a acceder a las recetas', function () {
    $this->actingAs(User::factory()->create())->withSession(['dato_privado' => 'dato']);
    $this->post('/logout')->assertRedirect(route('login'))->assertSessionMissing('dato_privado');
    $this->assertGuest();
    $this->get('/recetas')->assertUnauthorized();
});
