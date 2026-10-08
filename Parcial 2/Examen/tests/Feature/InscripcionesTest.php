<?php

namespace Tests\Feature;

use App\Models\Torneos;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class InscripcionesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::parse('2026-10-07 12:00:00', config('app.timezone')));
    }

    private function torneo(array $cambios = []): Torneos
    {
        return Torneos::create(array_replace([
            'nombre_torneo' => 'Copa de inscripciones',
            'tipo' => 'futbol',
            'fecha' => '2026-11-15',
            'cupo' => 2,
            'estado' => 'abierto',
        ], $cambios));
    }

    private function jugador(): User
    {
        return User::factory()->create(['rol' => 'jugador']);
    }

    public function test_el_jugador_se_inscribe_con_su_cuenta_e_ignora_identidades_del_formulario(): void
    {
        $torneo = $this->torneo();
        $otroTorneo = $this->torneo(['nombre_torneo' => 'Otro torneo']);
        $jugador = $this->jugador();
        $otro = $this->jugador();
        $this->actingAs($jugador);

        $this->get("/torneos/{$torneo->id}")->assertOk()->assertSee('Inscribirme');
        $this->post("/torneos/{$torneo->id}/inscripciones", ['user_id' => $otro->id, 'torneo_id' => $otroTorneo->id])
            ->assertRedirect(route('torneos.show', $torneo))->assertSessionHas('success');

        $this->assertDatabaseCount('inscripciones', 1);
        $this->assertDatabaseHas('inscripciones', ['torneo_id' => $torneo->id, 'user_id' => $jugador->id]);
        $this->get('/mis-torneos')->assertOk()->assertSee($torneo->nombre_torneo)->assertDontSee($otroTorneo->nombre_torneo);
        $this->get("/torneos/{$torneo->id}")->assertOk()
            ->assertSee('Ya estás inscrito en este torneo.')->assertSee('Cancelar mi inscripción')->assertDontSee('Inscribirme');
    }

    public function test_repetir_la_peticion_no_duplica_la_inscripcion(): void
    {
        $torneo = $this->torneo();
        $this->actingAs($this->jugador());

        $this->post("/torneos/{$torneo->id}/inscripciones")->assertSessionHas('success');
        $this->post("/torneos/{$torneo->id}/inscripciones")
            ->assertRedirect(route('torneos.show', $torneo))
            ->assertSessionHas('error', 'Ya estás inscrito en este torneo.');

        $this->assertDatabaseCount('inscripciones', 1);
    }

    #[DataProvider('noDisponibles')]
    public function test_no_se_puede_inscribir_mediante_peticion_directa_en_torneos_no_disponibles(array $cambios, string $aviso): void
    {
        $torneo = $this->torneo($cambios);
        $this->actingAs($this->jugador());

        $this->post("/torneos/{$torneo->id}/inscripciones")
            ->assertRedirect(route('torneos.show', $torneo))->assertSessionHas('error', $aviso);

        $this->assertDatabaseCount('inscripciones', 0);
        $this->get("/torneos/{$torneo->id}")->assertOk()->assertDontSee('Inscribirme');
    }

    public static function noDisponibles(): array
    {
        return [
            'cerrado' => [['estado' => 'cerrado'], 'No puedes inscribirte: el torneo está cerrado.'],
            'hoy' => [['fecha' => '2026-10-07'], 'No puedes inscribirte: la fecha del torneo ya llegó.'],
            'pasado' => [['fecha' => '2026-10-06'], 'No puedes inscribirte: la fecha del torneo ya llegó.'],
        ];
    }

    public function test_la_ultima_plaza_se_ocupa_y_el_siguiente_jugador_es_rechazado(): void
    {
        $torneo = $this->torneo();
        $torneo->inscripciones()->create(['user_id' => $this->jugador()->id]);
        $ultimo = $this->jugador();
        $siguiente = $this->jugador();

        $this->actingAs($ultimo)->post("/torneos/{$torneo->id}/inscripciones")->assertSessionHas('success');
        $this->actingAs($siguiente)->post("/torneos/{$torneo->id}/inscripciones")
            ->assertSessionHas('error', 'No puedes inscribirte: el torneo está lleno.');

        $this->assertSame(2, $torneo->inscripciones()->count());
        $this->assertDatabaseMissing('inscripciones', ['torneo_id' => $torneo->id, 'user_id' => $siguiente->id]);
        $this->get('/torneos')->assertOk()->assertDontSee($torneo->nombre_torneo);
        $this->get("/torneos/{$torneo->id}")->assertOk()->assertSee('Este torneo está lleno.')->assertDontSee('Inscribirme');

        $this->actingAs($ultimo)->post("/torneos/{$torneo->id}/inscripciones")
            ->assertSessionHas('error', 'Ya estás inscrito en este torneo.');
    }

    public function test_mis_torneos_solo_muestra_los_propios_incluidos_cerrados_y_pasados(): void
    {
        $jugador = $this->jugador();
        $futuro = $this->torneo(['nombre_torneo' => 'Mi futuro']);
        $cerrado = $this->torneo(['nombre_torneo' => 'Mi cerrado', 'estado' => 'cerrado']);
        $pasado = $this->torneo(['nombre_torneo' => 'Mi pasado', 'fecha' => '2026-10-01']);
        $ajeno = $this->torneo(['nombre_torneo' => 'Ajeno privado']);

        foreach ([$futuro, $cerrado, $pasado] as $torneo) {
            $torneo->inscripciones()->create(['user_id' => $jugador->id]);
        }
        $ajeno->inscripciones()->create(['user_id' => $this->jugador()->id]);

        $this->actingAs($jugador)->get('/mis-torneos')->assertOk()
            ->assertSee('Mi futuro')->assertSee('Mi cerrado')->assertSee('Mi pasado')
            ->assertSee('Plazo de cancelación terminado')->assertDontSee('Ajeno privado');
    }

    public function test_mis_torneos_tiene_estado_vacio_y_paginacion(): void
    {
        $jugador = $this->jugador();
        $this->actingAs($jugador)->get('/mis-torneos')->assertOk()->assertSee('Aún no estás inscrito en ningún torneo');

        for ($numero = 1; $numero <= 11; $numero++) {
            $torneo = $this->torneo(['nombre_torneo' => sprintf('Mi torneo %02d', $numero)]);
            $torneo->inscripciones()->create(['user_id' => $jugador->id]);
        }

        $this->get('/mis-torneos')->assertOk()->assertSee('Mi torneo 01')->assertSee('Mi torneo 10')
            ->assertDontSee('Mi torneo 11')->assertSee('Mostrando 1 a 10 de 11 torneos');
        $this->get('/mis-torneos?page=2')->assertOk()->assertSee('Mi torneo 11')->assertDontSee('Mi torneo 01');
    }

    public function test_cancelar_libera_plaza_y_permite_que_otro_jugador_se_inscriba(): void
    {
        $torneo = $this->torneo();
        $jugador = $this->jugador();
        $otro = $this->jugador();
        $torneo->inscripciones()->create(['user_id' => $jugador->id]);
        $inscripcionAjena = $torneo->inscripciones()->create(['user_id' => $otro->id]);
        $this->actingAs($jugador);

        $this->delete("/torneos/{$torneo->id}/inscripciones", ['user_id' => $otro->id, 'inscripcion_id' => $inscripcionAjena->id])
            ->assertRedirect(route('inscripciones.index'))->assertSessionHas('success');

        $this->assertDatabaseMissing('inscripciones', ['torneo_id' => $torneo->id, 'user_id' => $jugador->id]);
        $this->assertDatabaseHas('inscripciones', ['id' => $inscripcionAjena->id]);
        $this->get('/mis-torneos')->assertOk()->assertDontSee($torneo->nombre_torneo);
        $this->get('/torneos')->assertOk()->assertSee($torneo->nombre_torneo);
        $this->actingAs($this->jugador())->post("/torneos/{$torneo->id}/inscripciones")->assertSessionHas('success');
        $this->assertSame(2, $torneo->inscripciones()->count());
    }

    public function test_se_puede_cancelar_un_torneo_cerrado_con_fecha_futura(): void
    {
        $torneo = $this->torneo(['estado' => 'cerrado']);
        $jugador = $this->jugador();
        $torneo->inscripciones()->create(['user_id' => $jugador->id]);
        $this->actingAs($jugador);

        $this->get("/torneos/{$torneo->id}")->assertOk()->assertSee('Cancelar mi inscripción');
        $this->delete("/torneos/{$torneo->id}/inscripciones")->assertSessionHas('success');
        $this->assertDatabaseCount('inscripciones', 0);
        $this->get('/torneos')->assertOk()->assertDontSee($torneo->nombre_torneo);
    }

    #[DataProvider('fechasSinCancelacion')]
    public function test_no_se_puede_cancelar_desde_la_fecha_del_evento(string $fecha): void
    {
        $torneo = $this->torneo(['fecha' => $fecha]);
        $jugador = $this->jugador();
        $inscripcion = $torneo->inscripciones()->create(['user_id' => $jugador->id]);
        $this->actingAs($jugador);

        $this->delete("/torneos/{$torneo->id}/inscripciones")
            ->assertSessionHas('error', 'Solo puedes cancelar tu inscripción antes de la fecha del torneo.');

        $this->assertDatabaseHas('inscripciones', ['id' => $inscripcion->id]);
        $this->get('/mis-torneos')->assertOk()->assertDontSee('Sí, cancelar inscripción');
        $this->get("/torneos/{$torneo->id}")->assertOk()->assertDontSee('Cancelar mi inscripción');
    }

    public static function fechasSinCancelacion(): array
    {
        return [['2026-10-07'], ['2026-10-06']];
    }

    public function test_un_jugador_no_puede_cancelar_inscripciones_ajenas(): void
    {
        $torneo = $this->torneo();
        $otro = $this->jugador();
        $inscripcion = $torneo->inscripciones()->create(['user_id' => $otro->id]);

        $this->actingAs($this->jugador())->delete("/torneos/{$torneo->id}/inscripciones", ['user_id' => $otro->id])
            ->assertSessionHas('error', 'No tienes una inscripción en este torneo.');

        $this->assertDatabaseHas('inscripciones', ['id' => $inscripcion->id]);
    }

    public function test_cancelar_dos_veces_da_un_aviso_sin_afectar_a_otros(): void
    {
        $torneo = $this->torneo();
        $jugador = $this->jugador();
        $torneo->inscripciones()->create(['user_id' => $jugador->id]);
        $this->actingAs($jugador);

        $this->delete("/torneos/{$torneo->id}/inscripciones")->assertSessionHas('success');
        $this->delete("/torneos/{$torneo->id}/inscripciones")
            ->assertSessionHas('error', 'No tienes una inscripción en este torneo.');

        $this->assertDatabaseCount('inscripciones', 0);
    }

    #[DataProvider('rolesSinAccesoJugador')]
    public function test_solo_jugadores_pueden_usar_las_rutas_de_inscripcion(?string $rol, string $destino): void
    {
        $torneo = $this->torneo();
        if ($rol !== null) {
            $this->actingAs(User::factory()->create(['rol' => $rol]));
        }

        $this->get('/mis-torneos')->assertRedirect($destino)->assertSessionHas('error');
        $this->post("/torneos/{$torneo->id}/inscripciones")->assertRedirect($destino)->assertSessionHas('error');
        $this->delete("/torneos/{$torneo->id}/inscripciones")->assertRedirect($destino)->assertSessionHas('error');
        $this->post('/torneos/999/inscripciones')->assertRedirect($destino)->assertSessionHas('error');
        $this->get("/torneos/{$torneo->id}")->assertOk()->assertDontSee('Inscribirme');
        $this->assertDatabaseCount('inscripciones', 0);
    }

    public static function rolesSinAccesoJugador(): array
    {
        return ['invitado' => [null, '/login'], 'admin' => ['admin', '/']];
    }

    public function test_las_inscripciones_inexistentes_o_sin_token_no_se_modifican(): void
    {
        $torneo = $this->torneo();
        $this->actingAs($this->jugador());

        $this->post('/torneos/999/inscripciones')->assertNotFound();
        $this->delete('/torneos/999/inscripciones')->assertNotFound();
        $this->get("/torneos/{$torneo->id}/inscripciones")->assertStatus(405);

        $this->app->instance('env', 'local');
        $this->post("/torneos/{$torneo->id}/inscripciones")->assertStatus(419);
        $this->delete("/torneos/{$torneo->id}/inscripciones")->assertStatus(419);
        $this->assertDatabaseCount('inscripciones', 0);
    }
}
