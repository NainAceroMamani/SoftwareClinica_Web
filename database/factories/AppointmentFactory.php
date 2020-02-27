<?php

use Faker\Generator as Faker;
use App\Appointment;
use App\User;

$factory->define(Appointment::class, function (Faker $faker) {
    $doctorIds = User::doctors()->pluck('id'); // pluck => te devuelve un array
    $patientIds = User::patients()->pluck('id');

    // dateTimeBetween te devuelve la fecha de hace un año a la actualidad
    $date = $faker->dateTimeBetween('-1 years', 'now');
    $scheduled_date = $date->format('Y-m-d');
    $scheduled_time = $date->format('H:i:s');

    $types = ['Consulta', 'Examen', 'Operación'];
    $status = ['Atendida', 'Cancelada', 'Reservada', 'Confirmada'];
    
    return [
        'description'   => $faker->sentence(5), // sentence -> te devuelve palabras 5
        'specialty_id'  => $faker->numberBetween(1,3),
        'doctor_id'     => $faker->randomElement($doctorIds),
        'patient_id'    => $faker->randomElement($patientIds),
        'scheduled_date'=> $scheduled_date,
        'scheduled_time'=> $scheduled_time,
        'type'          => $faker->randomElement($types),
        'status'        => $faker->randomElement($status),
    ];
});
