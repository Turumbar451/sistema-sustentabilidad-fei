<?php

it('muestra el catálogo público', function () {
    $this->get(route('catalogo.index'))
        ->assertOk()
        ->assertSee('Explora las iniciativas')
        ->assertSee('Jornada de recuperación verde');
});

it('filtra las iniciativas por texto', function () {
    $this->get(route('catalogo.index', ['q' => 'ahorro']))
        ->assertOk()
        ->assertSee('Campaña de ahorro de agua')
        ->assertDontSee('Separación de residuos en el campus');
});

it('filtra las iniciativas por eje y modalidad', function () {
    $this->get(route('catalogo.index', [
        'ejes' => ['Agua'],
        'modalidad' => 'Híbrida',
    ]))
        ->assertOk()
        ->assertSee('Campaña de ahorro de agua')
        ->assertDontSee('Jornada de recuperación verde');
});

it('muestra el detalle temporal de una iniciativa', function () {
    $this->get(route('catalogo.show', ['accion' => 1]))
        ->assertOk()
        ->assertSee('Jornada de recuperación verde')
        ->assertSee('información demostrativa');
});

it('responde 404 cuando la iniciativa no existe', function () {
    $this->get(route('catalogo.show', ['accion' => 999]))
        ->assertNotFound();
});
