<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Specialty;
use App\Appointment;
use App\CancelledAppointment;
use Carbon\Carbon;
use App\Interfaces\ScheduleServiceInterface;
use Validator;
use App\Http\Requests\StoreAppointment;

class AppointmentController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role;
        
        if($role == 'admin')
        {
            $pendingAppointments = Appointment::where('status', 'Reservada')
                ->whereDate('scheduled_date','>=',Carbon::now()->format('Y-m-d'))
                ->orderBy('scheduled_date', 'asc')
                ->paginate(10);
            $confirmedAppointments = Appointment::where('status', 'Confirmada')
                ->whereDate('scheduled_date','>=',Carbon::now()->format('Y-m-d'))
                ->orderBy('scheduled_date', 'asc')
                ->paginate(10);
            $oldAppointments = Appointment::whereIn('status', ['Atendida', 'Cancelada'])
                ->orderBy('scheduled_date', 'asc')
                ->paginate(10);
        }   
        elseif($role == 'doctor') 
        {
            $pendingAppointments = Appointment::where('status', 'Reservada')
                ->whereDate('scheduled_date','>=',Carbon::now()->format('Y-m-d'))
                ->where('doctor_id', auth()->id())
                ->orderBy('scheduled_date', 'asc')
                ->paginate(10);
            $confirmedAppointments = Appointment::where('status', 'Confirmada')
                ->whereDate('scheduled_date','>=',Carbon::now()->format('Y-m-d'))
                ->where('doctor_id', auth()->id())
                ->orderBy('scheduled_date', 'asc')
                ->paginate(10);
            $oldAppointments = Appointment::whereIn('status', ['Atendida', 'Cancelada'])
                ->where('doctor_id', auth()->id())
                ->orderBy('scheduled_date', 'asc')
                ->paginate(10);
        } elseif($role == 'patient')
        {
            $pendingAppointments = Appointment::where('status', 'Reservada')
                ->whereDate('scheduled_date','>=',Carbon::now()->format('Y-m-d'))
                ->where('patient_id', auth()->id())
                ->orderBy('scheduled_date', 'asc')
                ->paginate(10);
            $confirmedAppointments = Appointment::where('status', 'Confirmada')
                ->whereDate('scheduled_date','>=',Carbon::now()->format('Y-m-d'))
                ->where('patient_id', auth()->id())
                ->orderBy('scheduled_date', 'asc')
                ->paginate(10);
            $oldAppointments = Appointment::whereIn('status', ['Atendida', 'Cancelada'])
                ->where('patient_id', auth()->id())
                ->orderBy('scheduled_date', 'asc')
                ->paginate(10);
        }
        return view('appointments.index', 
            compact('pendingAppointments', 'confirmedAppointments', 'oldAppointments', 'role')
        );
    }

    public function show(Appointment $appointment) 
    {
        $role = auth()->user()->role;
        return view('appointments.show' , compact('appointment', 'role'));
    }

    public function create(ScheduleServiceInterface $scheduleService)
    {
        $specialties = Specialty::all();

        $specialtyId = old('specialty_id');
        if ($specialtyId) {
            $specialty = Specialty::find($specialtyId);
            $doctors = $specialty->users;
        } else {
            $doctors = collect();
        }

        $scheduleDate = old('scheduled_date');
        $doctorId = old('doctor_id');
        if($scheduleDate && $doctorId){
            $intervals = $scheduleService->getAvailableIntervals($scheduleDate, $doctorId);
        }else {
            $intervals = null;
        }

        return view('appointments.create', compact('specialties', 'doctors', 'intervals'));
    }

    // public function store(Request $request, ScheduleServiceInterface $scheduleService)
    public function store(StoreAppointment $request)
    {
        /*
        $validator->after(function ($validator) use ($request, $scheduleService) {
            $date = $request->input('scheduled_date');
            $doctorId = $request->input('doctor_id');
            $scheduled_time = $request->input('scheduled_time');
            if($date && $doctorId && $scheduled_time) {
                $start = new Carbon($scheduled_time);
                
                if(!$scheduleService->isAvailableInterval($date, $doctorId, $start)) {
                    $validator->errors()
                        ->add('available_time', 'La hora seleccionada ya se encuentra 
                        reservada por otro paciente.');
                }
            }
        });
        
        if ($validator->fails()) {
            return back()
                        ->withErrors($validator)
                        ->withInput();
        }
        $data = $request->only([
            'description',
            'specialty_id',
            'doctor_id',
            'scheduled_date',
            'scheduled_time',
            'type'
        ]);
        $data['patient_id'] = auth()->id();
        $carbonTime = Carbon::createFromFormat('g:i A', $data['scheduled_time']);
        $data['scheduled_time'] = $carbonTime->format('H:i:s');
        Appointment::create($data);
        */   
        
        $created =  Appointment::createFormPatient($request, auth()->id());
        $notification = ($created)? 'La cita se ha registrado correctamente' : 'Ocurrio un problema al registrar la cita médica' ;
        return back()->with(compact('notification'));
    }

    public function showCancelForm(Appointment $appointment)
    {
        $role = auth()->user()->role;
        if($appointment->status == 'Confirmada' || $role == 'admin' || $role == 'doctor')
        {
            return view('appointments.cancel', compact('appointment', 'role'));
        }
        return redirect('/appointments');
    }

    public function postCancel(Appointment $appointment , Request $request)
    {
        if($request->has('justification')){
            $cancellation = new CancelledAppointment();
            $cancellation->justification = $request->input('justification');
            $cancellation->cancelled_by_id = auth()->id();
           //  $cancellation->appointment_id = $appointment->id;
            $appointment->cancellation()->save($cancellation);
        }
        $appointment->status = 'Cancelada';
        $appointment->save();

        $notification = 'La cita se ha cancelado correctamente.';
        return redirect('/appointments')->with(compact('notification'));
    }
    public function postConfirm(Appointment $appointment)
    {
        $appointment->status = 'Confirmada';
        $appointment->save();

        $notification = 'La cita se ha confirmado correctamente.';
        return redirect('/appointments')->with(compact('notification'));
    }
}
