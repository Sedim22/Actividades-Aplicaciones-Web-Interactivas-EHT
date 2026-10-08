<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UsuariosDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AutenticacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_invitado_puede_ver_el_inicio_y_los_formularios(): void
    {
        $this->get('/')->assertOk()->assertSee('Crear mi cuenta');
        $this->get('/registro')->assertOk()->assertSee('Crea tu cuenta');
        $this->get('/login')->assertOk()->assertSee('Inicia sesión');
    }

    public function test_el_registro_asigna_jugador_aunque_se_envie_admin(): void
    {
        $this->post('/registro', [
            'name' => 'Ana Pérez',
            'email' => 'ANA@example.com',
            'password' => 'Secreto123!',
            'password_confirmation' => 'Secreto123!',
            'rol' => 'admin',
        ])->assertRedirect(route('jugador.inicio'))->assertSessionHas('success');

        $usuario = User::where('email', 'ana@example.com')->firstOrFail();

        $this->assertSame('jugador', $usuario->rol);
        $this->assertTrue(Hash::check('Secreto123!', $usuario->password));
        $this->assertAuthenticatedAs($usuario);
        $this->get('/jugador')->assertOk()->assertSee('Ana Pérez');
        $this->get('/admin')->assertRedirect(route('inicio'))->assertSessionHas('error');
    }

    #[DataProvider('registrosInvalidos')]
    public function test_el_registro_valida_los_campos_en_espanol(array $cambios, string $campo, string $mensaje): void
    {
        $datos = array_replace([
            'name' => 'Ana Pérez',
            'email' => 'ana@example.com',
            'password' => 'Secreto123!',
            'password_confirmation' => 'Secreto123!',
        ], $cambios);

        $this->from('/registro')->post('/registro', $datos)
            ->assertRedirect('/registro')
            ->assertSessionHasErrors([$campo => $mensaje])
            ->assertSessionMissing('_old_input.password')
            ->assertSessionMissing('_old_input.password_confirmation');

        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
        $this->get('/registro')->assertOk()->assertSee($mensaje)->assertSee('is-invalid');
    }

    public static function registrosInvalidos(): array
    {
        return [
            'nombre vacío' => [['name' => ''], 'name', 'El nombre es obligatorio.'],
            'correo vacío' => [['email' => ''], 'email', 'El correo electrónico es obligatorio.'],
            'correo inválido' => [['email' => 'sin-correo'], 'email', 'Ingresa un correo electrónico válido.'],
            'contraseña vacía' => [['password' => ''], 'password', 'La contraseña es obligatoria.'],
            'contraseña corta' => [['password' => 'abc', 'password_confirmation' => 'abc'], 'password', 'La contraseña debe tener al menos 8 caracteres.'],
            'confirmación diferente' => [['password_confirmation' => 'OtroSecreto'], 'password_confirmation', 'Las contraseñas no coinciden.'],
            'confirmación vacía' => [['password_confirmation' => ''], 'password_confirmation', 'Confirma tu contraseña.'],
        ];
    }

    public function test_no_se_permite_registrar_un_correo_duplicado(): void
    {
        User::factory()->create(['email' => 'ana@example.com']);

        $this->post('/registro', [
            'name' => 'Otra Ana',
            'email' => 'ANA@example.com',
            'password' => 'Secreto123!',
            'password_confirmation' => 'Secreto123!',
        ])->assertSessionHasErrors(['email' => 'Este correo electrónico ya está registrado.']);

        $this->assertDatabaseCount('users', 1);
        $this->assertGuest();
    }

    #[DataProvider('roles')]
    public function test_el_login_dirige_a_cada_rol_a_su_panel(string $rol, string $panel): void
    {
        $usuario = User::factory()->create(['email' => 'usuario@example.com', 'rol' => $rol]);

        $this->get('/login');
        $sesionAnterior = session()->getId();

        $this->post('/login', ['email' => 'USUARIO@example.com', 'password' => 'password'])
            ->assertRedirect($panel)->assertSessionHas('success');

        $this->assertAuthenticatedAs($usuario);
        $this->assertNotSame($sesionAnterior, session()->getId());
        $this->get($panel)->assertOk()->assertSee($usuario->name);
        $this->get('/panel')->assertRedirect($panel);
        $this->get('/')->assertOk()->assertSee('Cerrar sesión')->assertDontSee('Crear mi cuenta');
    }

    public static function roles(): array
    {
        return [
            'administrador' => ['admin', '/admin'],
            'jugador' => ['jugador', '/jugador'],
        ];
    }

    public function test_credenciales_incorrectas_no_inician_sesion(): void
    {
        $usuario = User::factory()->create();

        $this->from('/login')->post('/login', ['email' => $usuario->email, 'password' => 'incorrecta'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email' => 'El correo electrónico o la contraseña son incorrectos.'])
            ->assertSessionHasInput('email', $usuario->email)
            ->assertSessionMissing('_old_input.password');

        $this->assertGuest();
    }

    public function test_el_login_requiere_correo_y_contrasena(): void
    {
        $this->post('/login', [])->assertSessionHasErrors([
            'email' => 'El correo electrónico es obligatorio.',
            'password' => 'La contraseña es obligatoria.',
        ]);

        $this->assertGuest();
    }

    public function test_el_login_bloquea_intentos_repetidos_y_permite_reintentar_despues(): void
    {
        $usuario = User::factory()->create();

        for ($intento = 0; $intento < 5; $intento++) {
            $this->post('/login', ['email' => $usuario->email, 'password' => 'incorrecta'])
                ->assertSessionHasErrors('email');
        }

        $this->post('/login', ['email' => $usuario->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertStringContainsString('Demasiados intentos.', session('errors')->first('email'));
        $this->assertGuest();

        $this->travel(61)->seconds();

        $this->post('/login', ['email' => $usuario->email, 'password' => 'password'])
            ->assertRedirect('/jugador');

        $this->assertAuthenticatedAs($usuario);
    }

    #[DataProvider('roles')]
    public function test_el_logout_invalida_la_sesion_y_cierra_el_acceso(string $rol, string $panel): void
    {
        $usuario = User::factory()->create(['rol' => $rol]);
        $this->actingAs($usuario)->withSession(['dato_privado' => 'secreto'])->get($panel)->assertOk();
        $sesionAnterior = session()->getId();
        $tokenAnterior = session()->token();

        $this->post('/logout')->assertRedirect(route('login'))
            ->assertSessionHas('success')->assertSessionMissing('dato_privado');

        $this->assertGuest();
        $this->assertNotSame($sesionAnterior, session()->getId());
        $this->assertNotSame($tokenAnterior, session()->token());
        $this->get($panel)->assertRedirect(route('login'))->assertSessionHas('error');
    }

    public function test_no_se_puede_cerrar_sesion_mediante_get(): void
    {
        $this->actingAs(User::factory()->create())->get('/logout')->assertStatus(405);
        $this->assertAuthenticated();
    }

    public function test_los_invitados_no_pueden_acceder_a_los_paneles(): void
    {
        foreach (['/admin', '/jugador', '/panel'] as $ruta) {
            $this->get($ruta)->assertRedirect(route('login'))
                ->assertSessionHas('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        $this->post('/logout')->assertRedirect(route('login'));
    }

    #[DataProvider('roles')]
    public function test_los_roles_no_pueden_acceder_al_panel_del_otro_rol(string $rol, string $panel): void
    {
        $otroPanel = $panel === '/admin' ? '/jugador' : '/admin';

        $this->actingAs(User::factory()->create(['rol' => $rol]))
            ->get($otroPanel)->assertRedirect(route('inicio'))
            ->assertSessionHas('error', 'No tienes permiso para acceder a esta página.');
    }

    #[DataProvider('roles')]
    public function test_usuarios_autenticados_no_pueden_registrarse_ni_iniciar_otra_sesion(string $rol, string $panel): void
    {
        $usuario = User::factory()->create(['rol' => $rol]);
        $this->actingAs($usuario);

        foreach (['/registro', '/login'] as $ruta) {
            $this->get($ruta)->assertRedirect(route('panel'));
            $this->post($ruta, [])->assertRedirect(route('panel'));
        }

        $this->get('/panel')->assertRedirect($panel);
        $this->assertDatabaseCount('users', 1);
        $this->assertAuthenticatedAs($usuario);
    }

    public function test_un_rol_desconocido_no_puede_iniciar_sesion_ni_acceder_a_los_paneles(): void
    {
        $usuario = User::factory()->create(['rol' => 'otro']);

        $this->post('/login', ['email' => $usuario->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->actingAs($usuario);
        $this->get('/admin')->assertForbidden();
        $this->get('/jugador')->assertForbidden();
        $this->get('/panel')->assertForbidden();
        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_el_seeder_crea_las_cuentas_demo_sin_sobrescribirlas(): void
    {
        $this->seed(UsuariosDemoSeeder::class);

        $admin = User::where('email', 'admin@torneos.test')->firstOrFail();
        $jugador = User::where('email', 'jugador@torneos.test')->firstOrFail();

        $this->assertSame('admin', $admin->rol);
        $this->assertSame('jugador', $jugador->rol);
        $this->assertTrue(Hash::check('Admin12345!', $admin->password));
        $this->assertTrue(Hash::check('Jugador12345!', $jugador->password));

        $admin->update(['password' => 'NuevaClave123!']);
        $this->seed(UsuariosDemoSeeder::class);

        $this->assertDatabaseCount('users', 2);
        $this->assertTrue(Hash::check('NuevaClave123!', $admin->fresh()->password));
    }
}
