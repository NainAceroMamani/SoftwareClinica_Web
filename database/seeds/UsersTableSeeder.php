<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Nain Acero Mamani',
            'email' => 'nain.acero24@gmail.com',
            'password' => bcrypt('secret'),
            'dni' => '74575544',
            'address' => '',
            'phone' => '',
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'Paciente',
            'email' => 'paciente@gmail.com',
            'password' => bcrypt('secret'),
            'dni' => '74575544',
            'address' => '',
            'phone' => '',
            'role' => 'patient'
        ]);

        User::create([
            'name' => 'doctor',
            'email' => 'doctor@gmail.com',
            'password' => bcrypt('secret'),
            'dni' => '74575544',
            'address' => '',
            'phone' => '',
            'role' => 'doctor'
        ]);

        factory(User::class, 50)->states('patient')->create();
    }
}
