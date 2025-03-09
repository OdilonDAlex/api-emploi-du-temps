<?php

namespace App\Models;

use ArrayObject;
use Exception;
use Hamcrest\Core\IsCollectionContaining;

use function PHPUnit\Framework\isArray;

class Graph
{

    public $courses = array();
    public $links = array();

    public function addVertex(Course $newCourse)
    {
        if (count($this->courses) === 0) {
            $this->courses[] = $newCourse;
            return;
        }

        $newCourseProfessor = $newCourse->subject->professor->name;
        $newCourseLevels = $newCourse->subject->levels()->get()->all();
        $newCourseSubjectName = $newCourse->subject->name;

        /**
         * @var Course $course
         */
        foreach ($this->courses as $course) {
            /**
             * @var Subject $subject;
             */

            $subject = $course->subject;
            if (
                $subject->professor->name === $newCourseProfessor
                ||
                $subject->name === $newCourseSubjectName
            ) {
                $this->addLink($course, $newCourse);
                continue;
            }

            $subjectLevels = $subject->levels()->get()->all();
            foreach ($subjectLevels as $level) {
                if (in_array($level, $newCourseLevels)) {
                    $this->addLink($course, $newCourse);
                    break;
                }
            }
        }

        $this->courses[] = $newCourse;
    }

    public function addLink(Course $a, Course $b)
    {
        if (! $this->linked($a, $b)) {
            $this->links[] = [$a, $b];
        }
    }
    /**
     * @param Array<int, ClassRoom> $classrooms
     */
    public function colorize($classrooms)
    {
        $courses = (new ArrayObject($this->courses))->getArrayCopy();

        $stables = array();

        $arrayObjectClassRoom = new ArrayObject($classrooms);

        while (count($courses) !== 0) {
            $classroomCopy = $arrayObjectClassRoom->getArrayCopy();

            usort($courses, function (Course $a, Course $b) {


                return
                    $this->getVertexDegree($a)
                    >=
                    $this->getVertexDegree($b) ?
                    -1 : 1;
            });


            $x = [array_shift($courses)];
            foreach ($courses as $course) {
                $linked = false;

                foreach ($x as $c) {
                    if ($this->linked($c, $course)) {
                        $linked = true;
                        break;
                    }
                }

                if (! $linked) $x[] = $course;
            }


            $x_with_class = array();

            usort($x, function (Course $a, Course $b) {
                $aLevels = $a->subject->levels()->get()->all();
                $bLevels = $b->subject->levels()->get()->all();

                $sumLevelA = array_sum(array_map(fn($level) => (int)($level->studentsNumber),  $aLevels));
                $sumLevelB = array_sum(array_map(fn($level) => (int)($level->studentsNumber), $bLevels));

                return $sumLevelA >= $sumLevelB ? -1 : 1;
            });


            usort($classroomCopy, function (ClassRoom $a, ClassRoom $b) {
                return $a->capacity >= $b->capacity ? -1 : 1;
            });


            foreach ($x as $course) {
                if (count($classroomCopy) === 0) break;

                $levels = $course->subject->levels()->get()->all();

                if (count($levels) > 1) {
                    usort($levels, function ($a, $b) {
                        return $a->studentsNumber >= $b->studentsNumber ? -1 : 1;
                    });
                }

                /**
                 * @var Level $level
                 */
                $level = $levels[0];
                $preferenceClassRoomIndex = $level->getPreferenceClassRoomIndex($classroomCopy);

                if ($preferenceClassRoomIndex !== null) {
                    $x_with_class[] = [$course, $classroomCopy[$preferenceClassRoomIndex]];

                    unset($classroomCopy[$preferenceClassRoomIndex]);
                    continue;
                }

                if (
                    array_sum(array_map(fn($level) => (int)($level->studentsNumber), $levels))
                    <= $classrooms[0]->capacity
                ) {
                    $x_with_class[] = [$course, array_shift($classroomCopy)];
                }
            }

            foreach ($x_with_class as $courseAndClassTuple) {
                try {
                    $courses = array_filter($courses, fn($c) => $c !== $courseAndClassTuple[0]);
                } catch (Exception $e) {
                    // 
                }
            }

            if (count($x_with_class) > 0) {
                $stables[] = $x_with_class;
            }
        }
        return $stables;
    }

    /**
     * @param Course $a
     * @param Course $b
     * @return bool
     */
    public function linked(Course $a, Course $b)
    {
        if (
            in_array([$a, $b], $this->links)
            ||
            in_array([$b, $a], $this->links)
        ) {
            return true;
        }
        return false;
    }
    /**
     * @param Course $course 
     * @return int
     */
    public function getVertexDegree($course)
    {
        $count = 0;
        foreach ($this->links as $link) {
            if (in_array($course, $link)) $count++;
        }

        return $count / 2; {
            $count = 0;
            foreach ($this->links as $link) {
                if (in_array($course, $link)) $count++;
            }
            return $count / 2;
        }
    }
}
