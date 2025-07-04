<?php

namespace App\Http\Controllers;

use App\Enums\DayPart;
use App\Exports\TimetableExport;
use App\Models\AcademicTrack;
use App\Models\Course;
use App\Models\CSP;
use App\Models\Graph;
use App\Models\Logger;
use App\Models\Timetable;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Ramsey\Collection\Map\AssociativeArrayMap;

class TimetableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Timetable::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'weekOf' => ['required', 'date'],
        ]);

        $timetable = Timetable::create($data);

        return [
            'message' => 'Timetable Created',
            'status' => 201,
            'Timetable' => $timetable
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(string | int $id)
    {
        try {
            $timetable = Timetable::findOrFail((int)$id);

            return [
                'status' => 200,
                'Timetable' => $timetable->load('courses'),
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Timetable not found',
                'status' => 404,
                'errors' => $e->getMessage()
            ];
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string | int $id)
    {
        $data = $request->validate([
            'weekOf' => ['nullable', 'date']
        ]);

        try {
            $timetable = Timetable::findOrFail((int)$id);
            $timetable->update($data);

            return [
                'message' => 'Timetable Updated',
                'status' => 200,
                'Timetable' => $timetable->fresh()
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Timetable not found',
                'status' => 404,
                'errors' => $e->getMessage()
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string | int $id)
    {
        try {

            Timetable::destroy((int)$id);
            return [
                'message' => 'Timetable Deleted',
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Timetable not found',
                'status' => 422,
                'errors' => $e->getMessage()
            ];
        }
    }

    public function generate(int $timetableId)
    {
        $timetable = Timetable::findOrFail((int)$timetableId);

        /**
         * @var Array<int, Course> $courses
         */
        $courses = $timetable->courses()->get()->all();


        $g = new Graph();

        foreach ($courses as $course) {
            $g->addCourse($course);
        }

        $result = CSP::backtrackingSearch($g);
        Logger::log($result['solved'] ? 'Resolu' : 'Impossible');
        foreach ($result['assignation'] as $k => $a) {
            Logger::log((Course::find((int)$k))->subject->name . ": (" . $a->day->value . "-" . $a->dayPart->value . "-" . $a->classroom->name . ")");
        }

        foreach (CSP::removeAssignedVar($result['assignation'], $g->courses) as $course) {
            Logger::log($course->subject->name . ": Nothing");
        }

        $formattedResult = TimetableController::formatResult($g->courses, $result['assignation']);

        return [
            'solved' => $result['solved'],
            'solution' => $formattedResult
        ];
    }

    public static function formatResult(array $courses, array $assignation)
    {
        $results = array();
        $days = [
            "Lundi",
            "Mardi",
            "Mercredi",
            "Jeudi",
            "Vendredi"
        ];

        foreach ($courses as $course) {

            if (array_key_exists((string)$course->id, $assignation) === false) {
                continue;
            }

            $value = $assignation[(string)$course->id];



            /**
             * @var array<int, AcademicTrack> $academicTracks
             */
            $academicTracks = $course->subject->academicTracks()->get()->all();

            $course->dayName = $days[(int)$value->day->value];
            $course->dayPart = $value->dayPart === DayPart::MORNING ? "Matin" : "Après-midi";
            $course->classroom = $value->classroom->name;

            $course->save();

            $results[] = [
                'course' => $course->subject->name,
                'levels' => $academicTracks[0]->level->name . ': ' .  array_reduce($academicTracks, fn($carry, $item) => $carry === null ? $item->name : $carry . ", " . $item->name),
                'day' => $course->dayName,
                'dayPart' => $course->dayPart,
                "classroom" => $value->classroom
            ];
        }

        return $results;
    }

    public function download(int $id)
    {
        $timetable = Timetable::findOrFail($id);

        $timetables = [];

        /**
         * Groupement par parcours
         */
        $courses = $timetable->courses()->get()->all();
        foreach ($courses as $course) {
            $academicTracks = $course->subject->academicTracks()->get()->all();
            foreach ($academicTracks as $academicTrack) {
                $timetables[$academicTrack->level->abbrName() . " " . $academicTrack->name][] = $this->formatCourseForPrint($course);
            }
        }


        /**
         * Tri des jours ( lundi, et matin passe devant les autres )
         */

        $sortedDay = [
            "Lundi" => 0,
            "Mardi" => 1,
            "Mercredi" => 2,
            "Jeudi" => 3,
            "Vendredi" => 4
        ];

        foreach ($timetables as $key => $value) {
            usort($value, static function ($courseA, $courseB) use ($sortedDay) {
                if ($courseA["dayName"] === $courseB["dayName"]) {
                    return ($courseA["dayPart"] === "Matin") ? -1 : 1;
                }

                return ($sortedDay[$courseA["dayName"]] < $sortedDay[$courseB["dayName"]]) ? -1 : 1;
            });
            $timetables[$key] = $value;
        }

        /**
         * Groupement par jour
         */

        foreach ($timetables as $academicTrackName => $timateble_) {
            $newValues = [];

            foreach ($timateble_ as $course_) {
                $newValues[$course_["dayName"]][] = [
                    'name' => $course_["name"],
                    'dayPart' => $course_["dayPart"],
                    'classroom' => $course_["classroom"],
                    "professor" => $course_["professor"]
                ];
            }

            $timetables[$academicTrackName] = $newValues;
        }

        return Excel::store(new TimetableExport($timetable->weekOf, $timetables), "timetable-" . Carbon::now()->timestamp . ".xlsx");
    }

    public function formatCourseForPrint(Course $course)
    {
        $subject = $course->subject;
        $professor = $subject->professor;
        return [
            'name' => $subject->name,
            'dayName' => $course->dayName,
            'dayPart' => $course->dayPart,
            'classroom' => $course->classroom,
            "professor" => $professor->name . " " . $professor->firstname
        ];
    }
}
