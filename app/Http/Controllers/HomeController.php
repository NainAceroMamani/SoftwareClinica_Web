<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Appointment;
use DB;
use Cache;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    private function daysToMinutes($days) {
        $hours = $days * 24;
        return $hours * 60;
    }

    public function index()
    {   
        // raw => para ejecutar metodos MYSQL
        // DAYOFWEEK => Method para traer el dia de la semana a traes de un date 
        // 1 = Sunday , 2 = Monday , ... , 7 = Saturday

        $minutes = $this->daysToMinutes(7); // minutes de la cache 
        $appointmentsByDay = Cache::remember('aapointments_by_day', $minutes, function () {
            $appointmentsResult = Appointment::select([
                DB::raw('DAYOFWEEK(scheduled_date) as day'),
                DB::raw('count(*) as count')
            ])
            ->groupBy(DB::raw('DAYOFWEEK(scheduled_date)'))
            // ->where('status', 'Confirmada')
            ->whereIn('status', ['Confirmada', 'Atendida'])
            // ->get();
            ->get(['day', 'count'])
            // para mapear [day => 1 , day = > 4] para hacer el recorrido y completar con ceros
            ->mapWithKeys(function($item) {
                return [$item['day'] => $item['count']];
            })->toArray();
        
            // completamos con ceros porque si un dia no trae resultado => [1,1] tien que tener 7 elementos el array 
            $counts = [];
            for($i=1 ; $i<=7; ++$i){
                if(array_key_exists($i, $appointmentsResult))
                    $counts[] = $appointmentsResult[$i];
                else
                    $counts[] = 0;
            }

            return $counts;
        });
        
        // dd($appointmentsByDay);
        return view('home', compact('appointmentsByDay'));

        // php artisan cache:clear => borrar memoria cache
    }
}
