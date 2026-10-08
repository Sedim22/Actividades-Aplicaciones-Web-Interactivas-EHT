<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsuariosDemoSeeder extends Seeder
{
    public function run(): void
    {
        // firstOrCreate conserva los datos de las cuentas si se ejecuta otra vez.
        User::firstOrCreate(['email' => 'admin@torneos.test'], [
            'name' => 'Administrador Demo',
            'password' => 'Admin12345!',
            'rol' => User::ROL_ADMIN,
        ]);

        User::firstOrCreate(['email' => 'jugador@torneos.test'], [
            'name' => 'Jugador Demo',
            'password' => 'Jugador12345!',
            'rol' => User::ROL_JUGADOR,
        ]);
    }
}
