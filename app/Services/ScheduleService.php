<?php namespace App\Services;

use App\WorkDay;
use App\Appointment;
use App\Interfaces\ScheduleServiceInterface;
use Carbon\Carbon;

class ScheduleService implements ScheduleServiceInterface
{
    public function isAvailableInterval($date, $doctorId, Carbon $start)
    {
        // no existe una cita para esta hora con este médico
        $exists = Appointment::where('doctor_id', $doctorId)
            ->where('scheduled_date', $date)
            ->where('scheduled_time', $start->format('H:i:s'))
            ->exists(); 

        return !$exists;
    }

    private function getDayFromDate($date)
    {
        $dateCarbon = new Carbon($date);

        // dayOfWeek para devolver el dia de la semana
        $i = $dateCarbon->dayOfWeek;
        $day = ($i==0 ? 6 : $i-1 );
        return $day;
    }

    public function getAvailableIntervals($date, $doctorId)
    {
        $workDay = WorkDay::where('active', true)
                           ->where('day',$this->getDayFromDate($date))
                           ->where('user_id', $doctorId)
                           ->first([
                               'morning_start', 'morning_end',
                               'afternoon_start' , 'afternoon_end'
                           ]);
        if($workDay) {    
            $morningStart = new Carbon($workDay->morning_start);
            $morningEnd   = new Carbon($workDay->morning_end);
            
            $morningIntervals = $this->getIntervals(
                $workDay->morning_start, $workDay->morning_end , $date , $doctorId
            );

            $afternoonIntervals = $this->getIntervals(
                $workDay->afternoon_start, $workDay->afternoon_end, $date , $doctorId
            );
            // dd($afternoonIntervals);
        } else {
            $morningIntervals = [];
            $afternoonIntervals = [];
        }

        $data = [];
        $data['morning'] = $morningIntervals;
        $data['afternoon'] = $afternoonIntervals;
        
        return $data;
    }

    private function getIntervals($start, $end , $date, $doctorId) {
        $start = new Carbon($start);
        $end   = new Carbon($end);
        
        $intervals = [];
        while($start  < $end)
        {
            $interval = [];

            $interval['start'] = $start->format('g:i A');

            // no existe una cita para esta hora con este médico
            $available = $this->isAvailableInterval($date, $doctorId, $start);

            $start->addMinutes(30);
            $interval['end'] = $start->format('g:i A');
            if($available) {
                $intervals []= $interval;
            }
        }
        return $intervals;
    }
}