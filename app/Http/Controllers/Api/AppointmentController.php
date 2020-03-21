<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Http\Requests\StoreAppointment;
use App\Appointment;

class AppointmentController extends Controller
{
    public function index()
    {
        $user = Auth::guard('api')->user(); // verivica si el user esta autentificado , with-> para poner las relaciones que nos interesan
        
        $apointments = $user->asPatientAppointments()
            ->with([
                // specialty -> nombre de la relacion function anónima para traer campos especificos 
                'specialty' =>  function($query) {
                    $query->select('id', 'name');
                },
                'doctor'    =>  function($query) {
                    $query->select('id', 'name');
                }
            ])
            ->get(["id","description","specialty_id","doctor_id","scheduled_date","scheduled_time","type","created_at","status"]);
        
            return $apointments;
    }

    public function store(Request $request){
        // Auth::guard('api')->id() => id de jwt
        $patientId = Auth::guard('api')->id();
        $appointment = Appointment::createFormPatient($request, $patientId);
        $success = ($appointment)? true : false;
        return compact('success');
    }
}
