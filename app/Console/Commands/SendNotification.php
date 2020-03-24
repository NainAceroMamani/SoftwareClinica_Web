<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Appointment;

class SendNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fcm:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar mensajes vía FCM';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now = Carbon::now();
        $this->info('Buscando citas médicas: '. $now);
        // un dia despues de la fecha actual y la hora que sea igual al dia anterior de la fecha de la cita
        // toDateString() -> para obtener el dia addDay() -> sumarle un dia 
        // hActual - 3m <= shceduled_time < hActual + 2m

        // $now = Carbon::now();

        // otra forma de inprimir para el error del info porque le pasamos array a este metodo si le devemos pasar array
        $headers = ['id', 'scheduled_date', 'scheduled_time', 'patient_id'];

        // citas para mañana
        $appointmentsTomorrow = $this->getAppointments24Hours($now->copy());
        
        // dd($appointments);   
        $this->table($headers, $appointmentsTomorrow->toArray());             

        foreach ($appointmentsTomorrow as $appointment) {
            $appointment->patient->sendFCM('No olvides tu cita mañana a esta hora. ');
            $this->info('Mensaje FCM enviado 24h antes al paciente (ID): ' . $appointment->id);
        }

        // citas dentro de una hora
        $appointmentsNextHour = $this->getAppointments24NextHours($now->copy());

        $this->table($headers, $appointmentsNextHour->toArray());

        foreach ($appointmentsNextHour as $appointment) {
            $appointment->patient->sendFCM('Tienes una cita en 1 hora. Te esperamos. ');
            $this->info('Mensaje FCM enviado faltando 1h al paciente (ID): ' . $appointment->id);
        }

    }

    private function getAppointments24Hours($now){

        return Appointment::where('status', 'Confirmada')
            ->where('scheduled_date', $now->addDay()->toDateString())
            // copy para que no se modifique
            ->where('scheduled_time', '>=' , $now->copy()->subMinutes(3)->toTimeString())
            ->where('scheduled_time', '<' , $now->copy()->addMinutes(2)->toTimeString())
            ->get(['id', 'scheduled_date', 'scheduled_time', 'patient_id']);
    }

    private function getAppointments24NextHours($now){

        return Appointment::where('status', 'Confirmada')
            ->where('scheduled_date', $now->addHour()->toDateString())
            // copy para que no se modifique
            ->where('scheduled_time', '>=' , $now->copy()->subMinutes(3)->toTimeString())
            ->where('scheduled_time', '<' , $now->copy()->addMinutes(2)->toTimeString())
            ->get(['id', 'scheduled_date', 'scheduled_time', 'patient_id']);
    }

    // para que este comando se ejecute App/Console/Kernel
}
