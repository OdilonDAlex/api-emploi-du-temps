<?php

namespace App\Exports;

use App\Models\Logger;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TimetableExport implements WithMultipleSheets
{
    /**
     * @return array
     */

    public function __construct(
        public string $weekOf,
        private array $timetables
    ) {}


    public function sheets(): array
    {
        $sheets = [];

        /**
         * @var Carbon $weekDate
         */
        $weekDate = new Carbon($this->weekOf);

        $months = ["Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre"];

        $day = $weekDate->format('d');
        $month = $months[(int)($weekDate->format('m')) - 1];
        $year = $weekDate->format('Y');

        $today = Carbon::today();
        $todayDay = $today->format('d');
        $todayMonth = $months[(int)$today->format('m') - 1];
        $todayYear = $today->format('Y');

        Logger::log($day . ' ' . $month . ' ' . $year);
        foreach ($this->timetables as $name => $timetable) {
            $sheets[] = new AcademicTrackTimetableExport("Semaine du {$day} {$month} {$year}", $name, $timetable, "{$todayDay} {$todayMonth} {$todayYear}");
        }
        return $sheets;
    }
}
