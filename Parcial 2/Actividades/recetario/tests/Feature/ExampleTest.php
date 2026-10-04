<?php

test('la página inicial lleva a los visitantes al login', function () {
    $this->get('/')->assertRedirect(route('login'));
});
