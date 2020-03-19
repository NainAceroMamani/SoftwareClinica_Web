<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Specialty;
use App\User;
use App\CancelledAppointment;

use Carbon\Carbon;

class Appointment extends Model
{
    protected $fillable = [
        'description',
        'specialty_id',
        'doctor_id',
        'patient_id',
        'scheduled_date',
        'scheduled_time',
        'type'
    ];   

    protected $hidden = [
        // ocultamos la hora porque el accesor ya lo traera formateada
        'specialty_id', 'doctor_id', 'scheduled_time'
    ];

    // para que en las consultas te traiga el accesor 
    protected $appends = [
        'scheduled_time_12'
    ];

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    // $appointments->doctor
    public function doctor()
    {
        return $this->belongsTo(User::class);
    }

    // $appointments->patients
    public function patient()
    {
        return $this->belongsTo(User::class);
    }

    // accessor
    public function getScheduledTime12Attribute() {
        return (new Carbon($this->scheduled_time))
            ->format('g:i A');
    }

    // 1 - 1/0 => hasOne - belongsTo
    public function cancellation()
    {
        return $this->hasOne(CancelledAppointment::class);
    }
}
