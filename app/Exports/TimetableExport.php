<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TimetableExport implements WithMultipleSheets
{
    /**
     * @return array
     */

    public function __construct(private array $timetables) {}


    public function sheets(): array
    {
        $sheets = [];
        foreach ($this->timetables as $name => $timetable) {
            $sheets[] = new AcademicTrackTimetableExport($name, $timetable);
        }
        return $sheets;
    }
}
