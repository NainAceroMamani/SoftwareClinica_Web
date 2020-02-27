<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Appointment;
use DB;
use App\User;
use Carbon\Carbon;

class ChartController extends Controller
{
    public function appointements()
    {
        // created_time => datetime 
        $monthlyCounts = Appointment::select(
            DB::raw('MONTH(created_at) as month'),// se devuelve el mes que hubo citas
            DB::raw('COUNT(1) as count')// se devuelve la cantidad de citas del mes
        )->groupBy('month')->get()->toArray();
        // [ ['month' => 11 , 'count' => 3 ] ]
        // [ 0,0,...3,0 ] => lo que queremos

        $counts = array_fill(0,12,0); // index,qty,value
        foreach ($monthlyCounts as $monthlyCount)
        {
            $index = $monthlyCount['month'] - 1;
            $counts[$index] = $monthlyCount['count'];
        }
        return view('charts.appointements', compact('counts'));
    }

    public function doctors()
    {
        $now = Carbon::now();
        $end = $now->format('Y-m-d');
        $start = $now->subYear()->format('Y-m-d');
        return view('charts.doctors', compact('start', 'end'));
    }

    public function doctorsJson(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');
        // withCount => cuenta la relación que esta asociada
        $doctors = User::doctors()
            ->select('name')
            ->withCount([
                'attendedAppointments' => function($query) use ($start, $end) {
                    $query->whereBetween('scheduled_date', [$start, $end]);
                }, // funcion anonima para hacer el where
                'cancelledAppointments' => function($query) use ($start, $end) {
                    $query->whereBetween('scheduled_date', [$start, $end]);
                }  // funcion anonima para hacer el where
            ])
            // as_doctor_appointments_count => genere e; withCount
            ->orderBy('attended_appointments_count', 'desc')
            ->take(5)
            ->get();
        // dd($doctors);

        $data = [];
        $data['categories'] = $doctors->pluck('name'); // pluck => para que me traiga un array

        $series = [];

        // Atendidas
        $series1['name'] = 'Citas atendidas';
        $series1['data'] = $doctors->pluck('attended_appointments_count');
        
        // Canceladas
        $series2['name'] = 'Citas canceladas';
        $series2['data'] = $doctors->pluck('cancelled_appointments_count');
        
        $series[] = $series1;
        $series[] = $series2;

        $data['series'] = $series;
        
        return $data;
    }
}
