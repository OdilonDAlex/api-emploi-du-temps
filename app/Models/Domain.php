<?php

namespace App\Models;

use App\Enums\DayPart;
use App\Enums\WeekDay;

class Domain
{

    public WeekDay $day;
    public DayPart $dayPart;
    public ClassRoom $classroom;

    public function __construct(WeekDay $day, DayPart $dayPart, ClassRoom $classroom)
    {
        $this->day = $day;
        $this->dayPart = $dayPart;
        $this->classroom = $classroom;
    }

    public function equals(Domain $domain): bool
    {
        return $this->day === $domain->day && $this->dayPart === $domain->dayPart && $this->classroom === $domain->classroom;
    }
}
