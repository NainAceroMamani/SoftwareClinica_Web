<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'dni', 'address', 'phone', 'role', 'id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'pivot',
        'email_verified_at', 'created_at', 'updated_at'
    ];

    // static para accder User::$rules sin crear instancias
    public static $rules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:6', 'confirmed'],
    ];

    public static function createPatient(array $data) {
        return self::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'patient'
        ]);
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function specialties()
    {
        return $this->belongsToMany(Specialty::class)->withTimestamps();
    }

    public function scopePatients($query)
    {
        return $query->where('role', 'patient');
    }
    
    public function scopeDoctors($query)
    {
        return $query->where('role', 'doctor');
    }

    // $user    =>  asPatientAppointments
    // $doctors =>  asDoctorAppointments
    public function asDoctorAppointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');   
    }
    
    public function asPatientAppointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');   
    }
    // return Reservada y cancelada 

    public function attendedAppointments()
    {
        return $this->asDoctorAppointments()->where('status', 'Atendida');
    }

    public function cancelledAppointments()
    {
        return $this->asDoctorAppointments()->where('status', 'Cancelada');
    }

    public function sendFCM($message){

        if(!$this->device_token)
            return;

        $notificationBuilder = new PayloadNotificationBuilder(config('app.name'));
        $notificationBuilder->setBody($message)
                            ->setSound('default');
        
        $notification = $notificationBuilder->build();
        
        return $groupResponse = FCM::sendToGroup([
            $this->device_token // como estamos dentro del modelo user equivale a un this
        ], null, $notification, null);
    }
}

