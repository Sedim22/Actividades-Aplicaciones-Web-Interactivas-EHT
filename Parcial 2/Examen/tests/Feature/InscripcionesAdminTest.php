<?php

namespace Tests\Feature;

use App\Models\Torneos;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class InscripcionesAdminTest extends TestCase
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
            'nombre_torneo' => 'Copa de bajas',
            'tipo' => 'futbol',
            'fecha' => '2026-11-15',
            'cupo' => 2,
            'estado' => 'abierto',
        ], $cambios));
    }

    private function comoAdmin(): void
    {
        $this->actingAs(User::factory()->create(['rol' => 'admin']));
    }

    public function test_el_admin_ve_inscritos_y_sus_datos_solo_en_la_gestion(): void
    {
        $torneo = $this->torneo();
        $otro = $this->torneo(['nombre_torneo' => 'Otro torneo']);
        $jugador = User::factory()->create(['name' => 'Ana Inscrita', 'email' => 'ana-privado@example.com']);
        $ajeno = User::factory()->create(['name' => 'Participante ajeno']);
        $torneo->inscripciones()->create(['user_id' => $jugador->id]);
        $otro->inscripciones()->create(['user_id' => $ajeno->id]);
        $this->comoAdmin();

        $this->get("/admin/torneos/{$torneo->id}/inscripciones")->assertOk()
            ->assertSee($jugador->name)->assertSee($jugador->email)->assertSee('Sí, dar de baja')
            ->assertDontSee($ajeno->name)->assertDontSee($jugador->password);
        $this->get("/torneos/{$torneo->id}")->assertOk()->assertSee('Gestionar inscritos')->assertDontSee($jugador->email);
    }

    public function test_la_gestion_muestra_un_mensaje_si_no_hay_inscritos(): void
    {
        $torneo = $this->torneo();
        $this->comoAdmin();

        $this->get("/admin/torneos/{$torneo->id}/inscripciones")->assertOk()->assertSee('Este torneo todavía no tiene inscripciones.');
    }

    #[DataProvider('estadosYFechas')]
    public function test_el_admin_puede_dar_de_baja_en_cualquier_estado_y_fecha(array $cambios): void
    {
        $torneo = $this->torneo($cambios);
        $jugador = User::factory()->create(['rol' => 'jugador']);
        $inscripcion = $torneo->inscripciones()->create(['user_id' => $jugador->id]);
        $otra = $torneo->inscripciones()->create(['user_id' => User::factory()->create()->id]);
        $this->comoAdmin();

        $this->delete("/admin/torneos/{$torneo->id}/inscripciones/{$inscripcion->id}")
            ->assertRedirect(route('admin.torneos.inscripciones.index', $torneo))
            ->assertSessionHas('success', 'La inscripción se dio de baja correctamente. La plaza quedó libre.');

        $this->assertDatabaseMissing('inscripciones', ['id' => $inscripcion->id]);
        $this->assertDatabaseHas('inscripciones', ['id' => $otra->id]);
        $this->assertDatabaseHas('users', ['id' => $jugador->id]);
        $this->assertDatabaseHas('torneos', ['id' => $torneo->id]);
        $this->assertSame(1, $torneo->plazasDisponibles());
        $this->actingAs($jugador)->get('/mis-torneos')->assertOk()->assertDontSee($torneo->nombre_torneo);
    }

    public static function estadosYFechas(): array
    {
        return [
            'lleno' => [[]],
            'cerrado' => [['estado' => 'cerrado']],
            'hoy' => [['fecha' => '2026-10-07']],
            'pasado' => [['fecha' => '2026-10-06']],
        ];
    }

    public function test_la_baja_libera_una_plaza_de_un_torneo_lleno(): void
    {
        $torneo = $this->torneo();
        $inscripcion = $torneo->inscripciones()->create(['user_id' => User::factory()->create()->id]);
        $torneo->inscripciones()->create(['user_id' => User::factory()->create()->id]);
        $this->comoAdmin();

        $this->get('/torneos')->assertOk()->assertDontSee($torneo->nombre_torneo);
        $this->delete("/admin/torneos/{$torneo->id}/inscripciones/{$inscripcion->id}")->assertSessionHas('success');
        $this->get('/torneos')->assertOk()->assertSee($torneo->nombre_torneo);

        $this->actingAs(User::factory()->create(['rol' => 'jugador']))
            ->post("/torneos/{$torneo->id}/inscripciones")->assertSessionHas('success');
        $this->assertSame(2, $torneo->inscripciones()->count());
    }

    public function test_no_se_puede_mezclar_la_inscripcion_con_otro_torneo_en_la_url(): void
    {
        $torneo = $this->torneo();
        $otro = $this->torneo(['nombre_torneo' => 'Otro torneo']);
        $inscripcion = $otro->inscripciones()->create(['user_id' => User::factory()->create()->id]);
        $this->comoAdmin();

        $this->delete("/admin/torneos/{$torneo->id}/inscripciones/{$inscripcion->id}")->assertNotFound();
        $this->assertDatabaseHas('inscripciones', ['id' => $inscripcion->id]);
    }

    #[DataProvider('rolesSinPermiso')]
    public function test_jugadores_e_invitados_no_pueden_ver_ni_eliminar_inscripciones_admin(?string $rol, string $destino): void
    {
        $torneo = $this->torneo();
        $inscripcion = $torneo->inscripciones()->create(['user_id' => User::factory()->create()->id]);

        if ($rol !== null) {
            $this->actingAs(User::factory()->create(['rol' => $rol]));
        }

        $this->get("/admin/torneos/{$torneo->id}/inscripciones")->assertRedirect($destino)->assertSessionHas('error');
        $this->delete("/admin/torneos/{$torneo->id}/inscripciones/{$inscripcion->id}")->assertRedirect($destino)->assertSessionHas('error');
        $this->get('/admin/torneos/999/inscripciones')->assertRedirect($destino)->assertSessionHas('error');
        $this->delete('/admin/torneos/999/inscripciones/999')->assertRedirect($destino)->assertSessionHas('error');
        $this->assertDatabaseHas('inscripciones', ['id' => $inscripcion->id]);
    }

    public static function rolesSinPermiso(): array
    {
        return ['invitado' => [null, '/login'], 'jugador' => ['jugador', '/']];
    }

    public function test_bajas_inexistentes_y_peticiones_sin_csrf_no_eliminan_registros(): void
    {
        $torneo = $this->torneo();
        $inscripcion = $torneo->inscripciones()->create(['user_id' => User::factory()->create()->id]);
        $this->comoAdmin();

        $this->get('/admin/torneos/999/inscripciones')->assertNotFound();
        $this->delete("/admin/torneos/{$torneo->id}/inscripciones/999")->assertNotFound();
        $this->get("/admin/torneos/{$torneo->id}/inscripciones/{$inscripcion->id}")->assertStatus(405);

        $this->app->instance('env', 'local');
        $this->delete("/admin/torneos/{$torneo->id}/inscripciones/{$inscripcion->id}")->assertStatus(419);
        $this->assertDatabaseHas('inscripciones', ['id' => $inscripcion->id]);
    }
}
