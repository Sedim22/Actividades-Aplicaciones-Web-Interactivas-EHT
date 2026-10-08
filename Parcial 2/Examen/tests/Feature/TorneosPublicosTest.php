<?php

namespace Tests\Feature;

use App\Models\Torneos;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TorneosPublicosTest extends TestCase
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
            'nombre_torneo' => 'Copa pública',
            'tipo' => 'futbol',
            'fecha' => '2026-11-15',
            'cupo' => 16,
            'descripcion' => 'Encuentro en la cancha universitaria.',
            'estado' => 'abierto',
        ], $cambios));
    }

    private function inscribir(Torneos $torneo, int $cantidad): void
    {
        foreach (User::factory()->count($cantidad)->create(['rol' => 'jugador']) as $jugador) {
            $torneo->inscripciones()->create(['user_id' => $jugador->id]);
        }
    }

    #[DataProvider('rolesPublicos')]
    public function test_invitados_jugadores_y_admins_pueden_consultar_listado_y_detalle(?string $rol): void
    {
        $torneo = $this->torneo();

        if ($rol !== null) {
            $this->actingAs(User::factory()->create(['rol' => $rol]));
        }

        $this->get('/torneos')->assertOk()->assertSee($torneo->nombre_torneo)
            ->assertSee(route('torneos.show', $torneo));

        $detalle = $this->get("/torneos/{$torneo->id}")->assertOk()
            ->assertSee($torneo->nombre_torneo)->assertSee('Fútbol')
            ->assertSee('15/11/2026')->assertSee('16 jugadores')
            ->assertSee($torneo->descripcion)->assertSee('Aún no hay participantes inscritos.');

        if ($rol === 'admin') {
            $detalle->assertSee('Editar torneo');
        } else {
            $detalle->assertDontSee('Editar torneo')->assertDontSee('Gestionar torneos');
        }
    }

    public static function rolesPublicos(): array
    {
        return ['invitado' => [null], 'jugador' => ['jugador'], 'administrador' => ['admin']];
    }

    public function test_el_listado_solo_incluye_abiertos_futuros_y_con_plazas(): void
    {
        $this->torneo(['nombre_torneo' => 'Disponible lejano', 'fecha' => '2026-12-01']);
        $this->torneo(['nombre_torneo' => 'Disponible cercano', 'fecha' => '2026-10-08']);
        $this->torneo(['nombre_torneo' => 'Cerrado manualmente', 'estado' => 'cerrado']);
        $this->torneo(['nombre_torneo' => 'Torneo pasado', 'fecha' => '2026-10-06']);
        $this->torneo(['nombre_torneo' => 'Torneo de hoy', 'fecha' => '2026-10-07']);
        $lleno = $this->torneo(['nombre_torneo' => 'Sin plazas', 'cupo' => 2]);
        $this->inscribir($lleno, 2);
        $parcial = $this->torneo(['nombre_torneo' => 'Una plaza libre', 'fecha' => '2026-11-01', 'cupo' => 2]);
        $this->inscribir($parcial, 1);

        $this->get('/torneos')->assertOk()
            ->assertSeeInOrder(['Disponible cercano', 'Una plaza libre', 'Disponible lejano'])
            ->assertSee('3 torneos disponibles')
            ->assertDontSee('Cerrado manualmente')->assertDontSee('Torneo pasado')
            ->assertDontSee('Torneo de hoy')->assertDontSee('Sin plazas');
    }

    public function test_muestra_un_mensaje_cuando_no_hay_torneos_disponibles(): void
    {
        $this->get('/torneos')->assertOk()->assertSee('No hay torneos disponibles');

        $this->torneo(['estado' => 'cerrado']);
        $this->torneo(['fecha' => '2026-10-07']);
        $lleno = $this->torneo(['cupo' => 2]);
        $this->inscribir($lleno, 2);

        $this->get('/torneos')->assertOk()->assertSee('No hay torneos disponibles')->assertDontSee('Ver detalles');
    }

    #[DataProvider('torneosNoDisponibles')]
    public function test_los_torneos_no_disponibles_siguen_accesibles_por_enlace_directo(array $cambios, int $inscritos, string $estado): void
    {
        $torneo = $this->torneo($cambios);
        $this->inscribir($torneo, $inscritos);

        $this->get('/torneos')->assertOk()->assertDontSee($torneo->nombre_torneo);
        $this->get("/torneos/{$torneo->id}")->assertOk()
            ->assertSee($torneo->nombre_torneo)->assertSee($estado)->assertSee('Participantes');
    }

    public static function torneosNoDisponibles(): array
    {
        return [
            'cerrado' => [['estado' => 'cerrado'], 1, 'El administrador ha cerrado este torneo.'],
            'lleno' => [['cupo' => 2], 2, 'Este torneo está lleno.'],
            'pasado' => [['fecha' => '2026-10-06'], 1, 'Cerrado por fecha'],
            'hoy' => [['fecha' => '2026-10-07'], 1, 'Cerrado por fecha'],
        ];
    }

    public function test_el_detalle_muestra_solo_los_nombres_de_sus_participantes(): void
    {
        $torneo = $this->torneo(['cupo' => 4]);
        $otro = $this->torneo(['nombre_torneo' => 'Otra copa']);
        $ana = User::factory()->create(['name' => 'Ana Participante', 'email' => 'correo-privado@example.com']);
        $luis = User::factory()->create(['name' => 'Luis Participante']);
        $externo = User::factory()->create(['name' => 'Participante de otro torneo']);
        $torneo->inscripciones()->create(['user_id' => $ana->id]);
        $torneo->inscripciones()->create(['user_id' => $luis->id]);
        $otro->inscripciones()->create(['user_id' => $externo->id]);

        $this->get("/torneos/{$torneo->id}")->assertOk()
            ->assertSeeInOrder(['Ana Participante', 'Luis Participante'])
            ->assertDontSee($externo->name)->assertDontSee($ana->email)->assertDontSee($luis->email)
            ->assertDontSee($ana->password)->assertDontSee('Aún no hay participantes inscritos.');

        $this->assertSame(2, $torneo->plazasDisponibles());
    }

    public function test_el_listado_refleja_las_plazas_actuales(): void
    {
        $torneo = $this->torneo(['cupo' => 2]);
        $this->inscribir($torneo, 1);
        $this->get('/torneos')->assertOk()->assertSee($torneo->nombre_torneo);

        $this->inscribir($torneo, 1);
        $this->get('/torneos')->assertOk()->assertDontSee($torneo->nombre_torneo);

        $torneo->inscripciones()->first()->delete();
        $this->get('/torneos')->assertOk()->assertSee($torneo->nombre_torneo);
    }

    public function test_el_listado_filtra_antes_de_paginar_y_conserva_el_orden(): void
    {
        for ($numero = 14; $numero >= 1; $numero--) {
            $this->torneo([
                'nombre_torneo' => sprintf('Competencia %02d', $numero),
                'fecha' => today()->addDays($numero)->format('Y-m-d'),
            ]);
        }

        $this->torneo(['nombre_torneo' => 'Oculto por cierre', 'estado' => 'cerrado', 'fecha' => '2026-10-08']);

        $this->get('/torneos')->assertOk()
            ->assertSeeInOrder(['Competencia 01', 'Competencia 02', 'Competencia 12'])
            ->assertSee('Mostrando 1 a 12 de 14 torneos')
            ->assertDontSee('Competencia 13')->assertDontSee('Oculto por cierre');

        $this->get('/torneos?page=2')->assertOk()
            ->assertSeeInOrder(['Competencia 13', 'Competencia 14'])
            ->assertDontSee('Competencia 01')->assertSee('Mostrando 13 a 14 de 14 torneos');
    }

    public function test_la_descripcion_es_opcional_y_un_torneo_inexistente_devuelve_404(): void
    {
        $torneo = $this->torneo(['descripcion' => null]);

        $this->get("/torneos/{$torneo->id}")->assertOk()->assertSee('Este torneo no tiene descripción.');
        $this->get('/torneos/999')->assertNotFound();
    }

    public function test_los_datos_del_torneo_y_los_nombres_se_escapan_como_texto(): void
    {
        $nombre = '</title><script>alert("nombre")</script>';
        $descripcion = '<script>alert("descripcion")</script>';
        $participante = '<img src=x onerror=alert("participante")>';
        $torneo = $this->torneo(['nombre_torneo' => $nombre, 'descripcion' => $descripcion]);
        $usuario = User::factory()->create(['name' => $participante]);
        $torneo->inscripciones()->create(['user_id' => $usuario->id]);

        $this->get('/torneos')->assertOk()->assertSee($nombre)->assertDontSee($nombre, false);
        $this->get("/torneos/{$torneo->id}")->assertOk()
            ->assertSee($nombre)->assertSee($descripcion)->assertSee($participante)
            ->assertDontSee($nombre, false)->assertDontSee($descripcion, false)->assertDontSee($participante, false);
    }

    public function test_un_rol_desconocido_no_puede_acceder_a_las_rutas_publicas(): void
    {
        $torneo = $this->torneo();
        $this->actingAs(User::factory()->create(['rol' => 'otro']));

        $this->get('/torneos')->assertForbidden();
        $this->get("/torneos/{$torneo->id}")->assertForbidden();
    }
}
