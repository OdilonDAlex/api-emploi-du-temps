<?php

namespace App\Models;

use App\Enums\DayPart;

class Domain
{

    public string $dayName;
    public DayPart $dayPart;
    public ClassRoom $classroom;

    public function __construct(string $dayName, DayPart $dayPart, ClassRoom $classroom)
    {
        $this->dayName = $dayName;
        $this->dayPart = $dayPart;
        $this->$classroom = $classroom;
    }

    public function equals(Domain $domain): bool
    {
        return $this->dayName === $domain->dayName && $this->dayPart === $domain->dayPart && $this->classroom === $domain->classroom;
    }
}
