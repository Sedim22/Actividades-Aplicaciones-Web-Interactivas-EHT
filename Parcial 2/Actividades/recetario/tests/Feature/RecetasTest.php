<?php

use App\Models\Receta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function datosDeReceta(array $cambios = []): array
{
    return array_replace([
        'titulo' => 'Enchiladas verdes',
        'categoria' => 'almuerzo',
        'tiempo' => 30,
        'dificultad' => 'facil',
        'ingredientes' => "4 tortillas\n1 taza de salsa verde",
        'pasos' => "Calentar la salsa.\nRellenar las tortillas.",
        'nota' => 'Servir con queso.',
    ], $cambios);
}

test('un usuario nuevo ve su recetario vacío y el formulario de creación', function () {
    $this->actingAs(User::factory()->create());
    $this->get('/recetas')->assertOk()->assertSee('Aún no tienes recetas.');
    $this->get('/recetas/create')->assertOk()->assertSee('Nueva receta')->assertSee('Nota personal');
});

test('crear una receta la asigna al usuario conectado aunque se envíe otro propietario', function () {
    $usuario = User::factory()->create();
    $otro = User::factory()->create();

    $this->actingAs($usuario)->post('/recetas', datosDeReceta(['user_id' => $otro->id]))
        ->assertSessionHasNoErrors()->assertRedirect(route('recetas.show', Receta::sole()))
        ->assertSessionHas('success', 'Receta creada correctamente.');

    $receta = Receta::sole();
    expect($receta->user_id)->toBe($usuario->id);
    expect($receta->nota)->toBe('Servir con queso.');
    expect($receta->ingredientes)->toBe("4 tortillas\n1 taza de salsa verde");
    $this->get('/recetas')->assertSee('Enchiladas verdes')->assertSee('30 min')->assertSee('Fácil');
});

test('la creación valida los campos y conserva los datos para corregirlos', function (array $cambios, string $campo) {
    $this->actingAs(User::factory()->create())->from('/recetas/create')
        ->post('/recetas', datosDeReceta($cambios))
        ->assertRedirect('/recetas/create')->assertSessionHasErrors($campo);

    $this->assertDatabaseCount('recetas', 0);
    $this->get('/recetas/create')->assertOk()->assertSee('id="'.$campo.'-error"', false);
})->with([
    [['titulo' => ''], 'titulo'],
    [['titulo' => str_repeat('a', 256)], 'titulo'],
    [['categoria' => ''], 'categoria'],
    [['categoria' => 'otra'], 'categoria'],
    [['tiempo' => 0], 'tiempo'],
    [['tiempo' => -2], 'tiempo'],
    [['tiempo' => 1.5], 'tiempo'],
    [['dificultad' => 'extrema'], 'dificultad'],
    [['ingredientes' => ''], 'ingredientes'],
    [['pasos' => ''], 'pasos'],
]);

test('la nota personal es opcional', function () {
    $datos = datosDeReceta();
    unset($datos['nota']);
    $this->actingAs(User::factory()->create())->post('/recetas', $datos)->assertSessionHasNoErrors();
    expect(Receta::sole()->nota)->toBeNull();
});

test('el detalle presenta listas y escapa el contenido introducido', function () {
    $usuario = User::factory()->create();
    $receta = $usuario->recetas()->create(datosDeReceta([
        'titulo' => '</title><script>alert("titulo")</script>',
        'ingredientes' => "  Sal\r\n\r\nPimienta\rAceite",
        'pasos' => "Mezclar.\n\nServir.",
        'nota' => '<script>alert("xss")</script>',
    ]));

    expect($receta->ingredientesComoLista())->toBe(['Sal', 'Pimienta', 'Aceite']);
    expect($receta->pasosComoLista())->toBe(['Mezclar.', 'Servir.']);
    $this->actingAs($usuario)->get(route('recetas.show', $receta))->assertOk()
        ->assertSee('<ul', false)->assertSee('<ol', false)
        ->assertSee('Pimienta')->assertSee('Servir.')
        ->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert', false)
        ->assertDontSee('</title><script>', false);
});

test('la edición precarga los datos y guarda cambios sin cambiar de propietario', function () {
    $usuario = User::factory()->create();
    $otro = User::factory()->create();
    $receta = $usuario->recetas()->create(datosDeReceta());
    $this->actingAs($usuario)->get(route('recetas.edit', $receta))
        ->assertOk()->assertSee('Enchiladas verdes')->assertSee('Servir con queso.');

    $this->put(route('recetas.update', $receta), datosDeReceta([
        'titulo' => 'Enchiladas de la casa', 'nota' => null, 'user_id' => $otro->id,
    ]))->assertRedirect(route('recetas.show', $receta))->assertSessionHas('success');

    expect($receta->fresh()->titulo)->toBe('Enchiladas de la casa');
    expect($receta->fresh()->user_id)->toBe($usuario->id);
    expect($receta->fresh()->nota)->toBeNull();
});

test('editar aplica las mismas validaciones sin guardar datos inválidos', function () {
    $usuario = User::factory()->create();
    $receta = $usuario->recetas()->create(datosDeReceta());
    $this->actingAs($usuario)->put(route('recetas.update', $receta), datosDeReceta(['tiempo' => 0]))
        ->assertSessionHasErrors('tiempo');
    expect($receta->fresh()->tiempo)->toBe(30);
});

test('eliminar ofrece confirmación en la interfaz y muestra un mensaje de éxito', function () {
    $usuario = User::factory()->create();
    $receta = $usuario->recetas()->create(datosDeReceta());
    $this->actingAs($usuario)->get(route('recetas.show', $receta))->assertSee('data-confirm=', false);
    $this->get('/recetas')->assertSee('data-confirm=', false);
    $this->delete(route('recetas.destroy', $receta))->assertRedirect(route('recetas.index'))
        ->assertSessionHas('success', 'Receta eliminada correctamente.');
    $this->assertDatabaseMissing('recetas', ['id' => $receta->id]);
});

test('buscar por título y filtrar por categoría se pueden combinar', function () {
    $usuario = User::factory()->create();
    $usuario->recetas()->create(datosDeReceta(['titulo' => 'Pan de naranja', 'categoria' => 'postre']));
    $usuario->recetas()->create(datosDeReceta(['titulo' => 'Pan con huevo', 'categoria' => 'desayuno']));
    $usuario->recetas()->create(datosDeReceta(['titulo' => 'Flan casero', 'categoria' => 'postre']));
    User::factory()->create()->recetas()->create(datosDeReceta(['titulo' => 'Pan privado', 'categoria' => 'postre']));

    $this->actingAs($usuario)->get('/recetas?buscar=PAN&categoria=postre')
        ->assertOk()->assertSee('Pan de naranja')->assertDontSee('Pan con huevo')
        ->assertDontSee('Flan casero')->assertDontSee('Pan privado');
    $this->get('/recetas?buscar=Pan')->assertSee('Pan de naranja')->assertSee('Pan con huevo')->assertDontSee('Flan casero');
    $this->get('/recetas?categoria=postre')->assertSee('Pan de naranja')->assertSee('Flan casero')->assertDontSee('Pan con huevo');
    $this->get('/recetas?buscar=sopa&categoria=postre')->assertOk()->assertSee('No encontramos recetas');
});

test('la búsqueda trata los comodines como texto literal', function () {
    $usuario = User::factory()->create();
    $usuario->recetas()->create(datosDeReceta(['titulo' => 'Jugo 100% natural']));
    $usuario->recetas()->create(datosDeReceta());
    $this->actingAs($usuario)->get('/recetas?buscar=%25')->assertSee('Jugo 100% natural')->assertDontSee('Enchiladas verdes');
});

test('la paginación conserva los filtros', function () {
    $usuario = User::factory()->create();
    for ($numero = 1; $numero <= 11; $numero++) {
        $usuario->recetas()->create(datosDeReceta(['titulo' => 'Pan '.$numero, 'categoria' => 'postre']));
    }
    $this->actingAs($usuario)->get('/recetas?buscar=Pan&categoria=postre')
        ->assertOk()->assertSee('Página 1 de 2')->assertSee('buscar=Pan&amp;categoria=postre&amp;page=2', false);
});

test('otro usuario no puede ver editar actualizar ni eliminar recetas ajenas', function () {
    $propietario = User::factory()->create();
    $receta = $propietario->recetas()->create(datosDeReceta());
    $this->actingAs(User::factory()->create());

    $this->get('/recetas')->assertDontSee($receta->titulo)->assertSee('Aún no tienes recetas.');
    $this->get(route('recetas.show', $receta))->assertNotFound()->assertDontSee($receta->titulo);
    $this->get(route('recetas.edit', $receta))->assertNotFound();
    $this->put(route('recetas.update', $receta), datosDeReceta(['titulo' => 'Cambio ajeno']))->assertNotFound();
    $this->delete(route('recetas.destroy', $receta))->assertNotFound();
    expect($receta->fresh()->titulo)->toBe('Enchiladas verdes');
    expect($receta->fresh()->user_id)->toBe($propietario->id);
});
