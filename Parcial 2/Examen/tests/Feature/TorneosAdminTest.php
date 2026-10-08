<?php

namespace Tests\Feature;

use App\Models\Torneos;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TorneosAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::parse('2026-10-07 12:00:00', config('app.timezone')));
    }

    private function datos(array $cambios = []): array
    {
        return array_replace([
            'nombre_torneo' => 'Copa universitaria',
            'tipo' => 'futbol',
            'fecha' => '2026-11-15',
            'cupo' => 16,
            'descripcion' => 'Partidos en la cancha de la universidad.',
            'estado' => 'abierto',
        ], $cambios);
    }

    private function comoAdmin(): User
    {
        $admin = User::factory()->create(['rol' => 'admin']);
        $this->actingAs($admin);

        return $admin;
    }

    private function inscribir(Torneos $torneo, int $cantidad): void
    {
        foreach (User::factory()->count($cantidad)->create(['rol' => 'jugador']) as $jugador) {
            $torneo->inscripciones()->create(['user_id' => $jugador->id]);
        }
    }

    public function test_el_admin_puede_ver_el_listado_vacio_y_el_formulario(): void
    {
        $this->comoAdmin();

        $this->get('/admin')->assertOk()->assertSee('Gestionar torneos');
        $this->get('/admin/torneos')->assertOk()->assertSee('No hay torneos para mostrar');
        $this->get('/admin/torneos/crear')->assertOk()
            ->assertSee('Crear torneo')
            ->assertSee('value="16"', false)
            ->assertSee('min="2026-10-08"', false);
    }

    public function test_el_admin_crea_un_torneo_con_todos_sus_campos(): void
    {
        $this->comoAdmin();

        $this->post('/admin/torneos', $this->datos())
            ->assertRedirect(route('admin.torneos.index'))
            ->assertSessionHas('success', 'El torneo se creó correctamente.');

        $this->assertDatabaseHas('torneos', Arr::except($this->datos(), 'fecha'));
        $this->assertSame('2026-11-15', Torneos::firstOrFail()->fecha->toDateString());
        $this->assertDatabaseCount('torneos', 1);
    }

    public function test_el_cupo_omitido_es_16_y_la_descripcion_es_opcional(): void
    {
        $this->comoAdmin();
        $datos = $this->datos();
        unset($datos['cupo'], $datos['descripcion']);

        $this->post('/admin/torneos', $datos)->assertSessionHasNoErrors()->assertRedirect(route('admin.torneos.index'));

        $this->assertDatabaseHas('torneos', ['nombre_torneo' => $datos['nombre_torneo'], 'cupo' => 16, 'descripcion' => null]);
    }

    #[DataProvider('cuposYEstadosValidos')]
    public function test_admite_los_limites_del_cupo_y_ambos_estados(int $cupo, string $estado): void
    {
        $this->comoAdmin();

        $this->post('/admin/torneos', $this->datos(['cupo' => $cupo, 'estado' => $estado]))
            ->assertSessionHasNoErrors()->assertRedirect(route('admin.torneos.index'));

        $this->assertDatabaseHas('torneos', ['cupo' => $cupo, 'estado' => $estado]);
    }

    public static function cuposYEstadosValidos(): array
    {
        return [[2, 'abierto'], [100, 'cerrado']];
    }

    #[DataProvider('datosInvalidos')]
    public function test_las_validaciones_impiden_crear_y_muestran_errores_en_espanol(array $cambios, string $campo, string $mensaje): void
    {
        $this->comoAdmin();

        $this->from('/admin/torneos/crear')->post('/admin/torneos', $this->datos($cambios))
            ->assertRedirect('/admin/torneos/crear')
            ->assertSessionHasErrors([$campo => $mensaje]);

        $this->assertDatabaseCount('torneos', 0);
        $this->get('/admin/torneos/crear')->assertOk()->assertSee($mensaje)->assertSee('is-invalid');
    }

    public static function datosInvalidos(): array
    {
        return [
            'nombre vacío' => [['nombre_torneo' => ' '], 'nombre_torneo', 'El nombre del torneo es obligatorio.'],
            'nombre largo' => [['nombre_torneo' => str_repeat('a', 256)], 'nombre_torneo', 'El nombre no debe superar los 255 caracteres.'],
            'tipo vacío' => [['tipo' => ''], 'tipo', 'Selecciona un juego o deporte.'],
            'tipo desconocido' => [['tipo' => 'otro'], 'tipo', 'Selecciona uno de los juegos o deportes disponibles.'],
            'fecha vacía' => [['fecha' => ''], 'fecha', 'La fecha del torneo es obligatoria.'],
            'fecha imposible' => [['fecha' => '2026-11-31'], 'fecha', 'Ingresa una fecha válida con el formato año-mes-día.'],
            'fecha pasada' => [['fecha' => '2026-10-06'], 'fecha', 'La fecha del torneo debe ser posterior a hoy.'],
            'fecha actual' => [['fecha' => '2026-10-07'], 'fecha', 'La fecha del torneo debe ser posterior a hoy.'],
            'cupo vacío' => [['cupo' => ''], 'cupo', 'El cupo es obligatorio.'],
            'cupo menor' => [['cupo' => 1], 'cupo', 'El cupo debe ser de al menos 2 jugadores.'],
            'cupo mayor' => [['cupo' => 101], 'cupo', 'El cupo no puede superar los 100 jugadores.'],
            'cupo decimal' => [['cupo' => 2.5], 'cupo', 'El cupo debe ser un número entero.'],
            'estado vacío' => [['estado' => ''], 'estado', 'Selecciona el estado del torneo.'],
            'estado desconocido' => [['estado' => 'lleno'], 'estado', 'El estado debe ser abierto o cerrado.'],
            'descripción larga' => [['descripcion' => str_repeat('a', 10001)], 'descripcion', 'La descripción no debe superar los 10,000 caracteres.'],
        ];
    }

    public function test_el_listado_admin_incluye_cerrados_pasados_y_llenos_con_sus_cupos(): void
    {
        $this->comoAdmin();
        Torneos::create($this->datos(['nombre_torneo' => 'Torneo pasado', 'fecha' => '2026-10-01']));
        Torneos::create($this->datos(['nombre_torneo' => 'Torneo cerrado', 'estado' => 'cerrado']));
        $lleno = Torneos::create($this->datos(['nombre_torneo' => 'Torneo lleno', 'cupo' => 2]));
        $this->inscribir($lleno, 2);

        $this->get('/admin/torneos')->assertOk()
            ->assertSee('Torneo pasado')->assertSee('Torneo cerrado')->assertSee('Torneo lleno')
            ->assertSee('2 / 2')->assertSee('Lleno')->assertSee('Cerrado por fecha')
            ->assertSee('Sí, eliminar torneo');
    }

    public function test_el_listado_se_ordena_por_fecha_y_se_pagina(): void
    {
        $this->comoAdmin();

        for ($numero = 12; $numero >= 1; $numero--) {
            Torneos::create($this->datos([
                'nombre_torneo' => sprintf('Torneo %02d', $numero),
                'fecha' => today()->addDays($numero)->format('Y-m-d'),
            ]));
        }

        $this->get('/admin/torneos')->assertOk()
            ->assertSeeInOrder(['Torneo 01', 'Torneo 02', 'Torneo 10'])
            ->assertDontSee('Torneo 11')->assertSee('Mostrando 1 a 10 de 12 torneos')->assertSee('Siguiente');

        $this->get('/admin/torneos?page=2')->assertOk()
            ->assertSee('Torneo 11')->assertSee('Torneo 12')->assertDontSee('Torneo 01');
    }

    public function test_el_admin_puede_editar_todos_los_datos(): void
    {
        $this->comoAdmin();
        $torneo = Torneos::create($this->datos());
        $this->inscribir($torneo, 1);

        $this->get("/admin/torneos/{$torneo->id}/editar")->assertOk()
            ->assertSee($torneo->nombre_torneo)->assertSee($torneo->descripcion);

        $nuevos = $this->datos([
            'nombre_torneo' => 'Copa de videojuegos',
            'tipo' => 'videojuegos',
            'fecha' => '2026-12-20',
            'cupo' => 20,
            'descripcion' => 'Nueva descripción.',
            'estado' => 'cerrado',
        ]);

        $this->put("/admin/torneos/{$torneo->id}", $nuevos)
            ->assertRedirect(route('admin.torneos.index'))->assertSessionHas('success');

        $this->assertDatabaseHas('torneos', ['id' => $torneo->id, ...Arr::except($nuevos, 'fecha')]);
        $this->assertSame('2026-12-20', $torneo->fresh()->fecha->toDateString());
        $this->assertSame(1, $torneo->inscripciones()->count());
    }

    public function test_la_edicion_tambien_exige_fecha_futura_y_cupo_valido(): void
    {
        $this->comoAdmin();
        $torneo = Torneos::create($this->datos());

        $this->put("/admin/torneos/{$torneo->id}", $this->datos(['fecha' => '2026-10-07', 'cupo' => 101]))
            ->assertSessionHasErrors(['fecha', 'cupo']);

        $this->assertDatabaseHas('torneos', ['id' => $torneo->id, ...Arr::except($this->datos(), 'fecha')]);
        $this->assertSame('2026-11-15', $torneo->fresh()->fecha->toDateString());
    }

    public function test_no_se_puede_reducir_el_cupo_por_debajo_de_los_inscritos(): void
    {
        $this->comoAdmin();
        $torneo = Torneos::create($this->datos());
        $this->inscribir($torneo, 3);

        $this->from("/admin/torneos/{$torneo->id}/editar")
            ->put("/admin/torneos/{$torneo->id}", $this->datos(['cupo' => 2, 'nombre_torneo' => 'No debe guardarse']))
            ->assertRedirect("/admin/torneos/{$torneo->id}/editar")
            ->assertSessionHasErrors(['cupo' => 'No puedes reducir el cupo a menos de 3: ya hay 3 jugadores inscritos.']);

        $this->assertDatabaseHas('torneos', ['id' => $torneo->id, 'cupo' => 16, 'nombre_torneo' => 'Copa universitaria']);
        $this->assertSame(3, $torneo->inscripciones()->count());
        $this->get("/admin/torneos/{$torneo->id}/editar")->assertOk()
            ->assertSee('No puedes reducir el cupo a menos de 3')->assertSee('No debe guardarse');
    }

    public function test_el_cupo_puede_ser_igual_al_numero_de_inscritos(): void
    {
        $this->comoAdmin();
        $torneo = Torneos::create($this->datos());
        $this->inscribir($torneo, 3);

        $this->put("/admin/torneos/{$torneo->id}", $this->datos(['cupo' => 3, 'descripcion' => '']))
            ->assertSessionHasNoErrors()->assertRedirect(route('admin.torneos.index'));

        $this->assertDatabaseHas('torneos', ['id' => $torneo->id, 'cupo' => 3, 'descripcion' => null]);
        $this->assertSame(3, $torneo->inscripciones()->count());
    }

    public function test_eliminar_borra_solo_el_torneo_y_sus_inscripciones(): void
    {
        $this->comoAdmin();
        $torneo = Torneos::create($this->datos());
        $otro = Torneos::create($this->datos(['nombre_torneo' => 'Otro torneo']));
        $this->inscribir($torneo, 2);
        $jugador = $torneo->inscripciones()->first()->user;
        $otraInscripcion = $otro->inscripciones()->create(['user_id' => $jugador->id]);

        $this->delete("/admin/torneos/{$torneo->id}")
            ->assertRedirect(route('admin.torneos.index'))
            ->assertSessionHas('success', 'El torneo y sus inscripciones se eliminaron correctamente.');

        $this->assertDatabaseMissing('torneos', ['id' => $torneo->id]);
        $this->assertDatabaseMissing('inscripciones', ['torneo_id' => $torneo->id]);
        $this->assertDatabaseHas('torneos', ['id' => $otro->id]);
        $this->assertDatabaseHas('inscripciones', ['id' => $otraInscripcion->id]);
        $this->assertDatabaseHas('users', ['id' => $jugador->id]);
    }

    #[DataProvider('rolesSinPermiso')]
    public function test_todas_las_rutas_del_crud_rechazan_invitados_y_jugadores(?string $rol, string $destino): void
    {
        $torneo = Torneos::create($this->datos());

        if ($rol !== null) {
            $this->actingAs(User::factory()->create(['rol' => $rol]));
        }

        foreach (['/admin/torneos', '/admin/torneos/crear', "/admin/torneos/{$torneo->id}/editar"] as $ruta) {
            $this->get($ruta)->assertRedirect($destino)->assertSessionHas('error');
        }

        $this->post('/admin/torneos', $this->datos())->assertRedirect($destino)->assertSessionHas('error');
        $this->put("/admin/torneos/{$torneo->id}", $this->datos(['cupo' => 50]))->assertRedirect($destino)->assertSessionHas('error');
        $this->delete("/admin/torneos/{$torneo->id}")->assertRedirect($destino)->assertSessionHas('error');

        $this->assertDatabaseCount('torneos', 1);
        $this->assertDatabaseHas('torneos', ['id' => $torneo->id, 'cupo' => 16]);
        $this->get('/')->assertOk()->assertDontSee('Gestionar torneos');
    }

    public static function rolesSinPermiso(): array
    {
        return ['invitado' => [null, '/login'], 'jugador' => ['jugador', '/']];
    }

    public function test_las_operaciones_sobre_un_torneo_inexistente_devuelven_404(): void
    {
        $this->comoAdmin();

        $this->get('/admin/torneos/999/editar')->assertNotFound();
        $this->put('/admin/torneos/999', $this->datos())->assertNotFound();
        $this->delete('/admin/torneos/999')->assertNotFound();
    }

    public function test_los_formularios_del_crud_exigen_token_csrf(): void
    {
        $this->comoAdmin();
        $torneo = Torneos::create($this->datos());
        // Laravel omite CSRF en testing: se activa aquí sin cambiar la BD en memoria.
        $this->app->instance('env', 'local');

        $this->post('/admin/torneos', $this->datos())->assertStatus(419);
        $this->put("/admin/torneos/{$torneo->id}", $this->datos(['cupo' => 50]))->assertStatus(419);
        $this->delete("/admin/torneos/{$torneo->id}")->assertStatus(419);

        $this->assertDatabaseCount('torneos', 1);
        $this->assertDatabaseHas('torneos', ['id' => $torneo->id, 'cupo' => 16]);
    }
}
