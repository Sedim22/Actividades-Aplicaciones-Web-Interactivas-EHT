<?php

namespace Tests\Feature;

use App\Models\User;
use DOMDocument;
use DOMElement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class MensajesFormularioTest extends TestCase
{
    use RefreshDatabase;

    private function documento(TestResponse $respuesta): DOMDocument
    {
        $documento = new DOMDocument;
        $anterior = libxml_use_internal_errors(true);
        $documento->loadHTML('<?xml encoding="UTF-8">'.$respuesta->getContent());
        libxml_clear_errors();
        libxml_use_internal_errors($anterior);

        return $documento;
    }

    private function assertErrorCampo(DOMDocument $documento, string $campo, string $mensaje): void
    {
        $control = $documento->getElementById($campo);
        $error = $documento->getElementById($campo.'-error');
        $this->assertInstanceOf(DOMElement::class, $control);
        $this->assertInstanceOf(DOMElement::class, $error);
        $this->assertStringContainsString('is-invalid', $control->getAttribute('class'));
        $this->assertSame('true', $control->getAttribute('aria-invalid'));
        $this->assertStringContainsString($campo.'-error', $control->getAttribute('aria-describedby'));
        $this->assertSame($mensaje, trim($error->textContent));
        $this->assertSame($control->parentNode, $error->parentNode);
    }

    public function test_el_registro_vacio_muestra_un_error_junto_a_cada_campo(): void
    {
        $this->from('/registro')->post('/registro', [])->assertRedirect('/registro');
        $respuesta = $this->get('/registro')->assertOk()->assertSee('novalidate', false);
        $documento = $this->documento($respuesta);

        foreach ([
            'name' => 'El nombre es obligatorio.',
            'email' => 'El correo electrónico es obligatorio.',
            'password' => 'La contraseña es obligatoria.',
            'password_confirmation' => 'Confirma tu contraseña.',
        ] as $campo => $mensaje) {
            $this->assertErrorCampo($documento, $campo, $mensaje);
        }

        $this->assertDatabaseCount('users', 0);
    }

    public function test_la_confirmacion_tiene_su_error_y_no_se_rellenan_las_contrasenas(): void
    {
        $this->from('/registro')->post('/registro', [
            'name' => 'Ana Pérez',
            'email' => 'ana@example.com',
            'password' => 'ClaveSegura123!',
            'password_confirmation' => 'OtraClave123!',
        ])->assertSessionHasErrors('password_confirmation')->assertSessionDoesntHaveErrors('password');

        $documento = $this->documento($this->get('/registro')->assertOk());
        $this->assertErrorCampo($documento, 'password_confirmation', 'Las contraseñas no coinciden.');
        $this->assertSame('', $documento->getElementById('password')->getAttribute('value'));
        $this->assertSame('', $documento->getElementById('password_confirmation')->getAttribute('value'));
        $this->assertSame('Ana Pérez', $documento->getElementById('name')->getAttribute('value'));
        $this->assertStringContainsString('is-valid', $documento->getElementById('name')->getAttribute('class'));
        $this->assertSame('El formato de este campo es válido.', trim($documento->getElementById('name-correcto')->textContent));
        $this->assertDatabaseCount('users', 0);
    }

    public function test_el_login_incorrecto_marca_los_dos_campos_sin_exponer_la_clave(): void
    {
        $usuario = User::factory()->create();
        $this->from('/login')->post('/login', ['email' => $usuario->email, 'password' => 'ClaveErronea123!'])
            ->assertSessionHasErrors(['email', 'password']);

        $documento = $this->documento($this->get('/login')->assertOk()->assertDontSee('ClaveErronea123!'));
        $this->assertErrorCampo($documento, 'email', 'El correo electrónico o la contraseña son incorrectos.');
        $this->assertErrorCampo($documento, 'password', 'El correo electrónico o la contraseña son incorrectos.');
        $this->assertGuest();
    }

    public function test_los_datos_malformados_muestran_el_error_sin_romper_el_formulario(): void
    {
        $this->from('/registro')->post('/registro', ['name' => ['valor inválido'], 'email' => ['valor inválido']])
            ->assertSessionHasErrors(['name', 'email']);

        $documento = $this->documento($this->get('/registro')->assertOk());
        $this->assertErrorCampo($documento, 'name', 'El nombre debe ser texto.');
        $this->assertErrorCampo($documento, 'email', 'El correo electrónico debe ser texto.');
        $this->assertSame('', $documento->getElementById('name')->getAttribute('value'));
    }

    public function test_los_torneos_muestran_errores_en_inputs_selects_y_descripcion(): void
    {
        $this->actingAs(User::factory()->create(['rol' => 'admin']));
        $this->from('/admin/torneos/crear')->post('/admin/torneos', [
            'nombre_torneo' => '', 'tipo' => '', 'fecha' => '', 'cupo' => '',
            'estado' => '', 'descripcion' => ['valor inválido'],
        ])->assertSessionHasErrors(['nombre_torneo', 'tipo', 'fecha', 'cupo', 'estado', 'descripcion']);

        $documento = $this->documento($this->get('/admin/torneos/crear')->assertOk()->assertSee('novalidate', false));
        foreach ([
            'nombre_torneo' => 'El nombre del torneo es obligatorio.',
            'tipo' => 'Selecciona un juego o deporte.',
            'fecha' => 'La fecha del torneo es obligatoria.',
            'cupo' => 'El cupo es obligatorio.',
            'estado' => 'Selecciona el estado del torneo.',
            'descripcion' => 'La descripción debe ser texto.',
        ] as $campo => $mensaje) {
            $this->assertErrorCampo($documento, $campo, $mensaje);
        }

        $this->assertDatabaseCount('torneos', 0);
    }

    public function test_el_formulario_inicial_no_muestra_exito_antes_de_validar(): void
    {
        $this->get('/registro')->assertOk()->assertDontSee('is-valid')->assertDontSee('is-invalid');
        $this->get('/login')->assertOk()->assertDontSee('is-valid')->assertDontSee('is-invalid');
    }

    public function test_el_exito_del_registro_se_muestra_en_la_pagina_de_destino(): void
    {
        $this->followingRedirects()->post('/registro', [
            'name' => 'Ana', 'email' => 'ana@example.com',
            'password' => 'ClaveSegura123!', 'password_confirmation' => 'ClaveSegura123!',
        ])->assertOk()->assertSee('Tu cuenta de jugador se creó correctamente.')
            ->assertSee('alert-success')->assertSee('Cerrar mensaje de éxito');
        $this->assertAuthenticated();
    }
}
