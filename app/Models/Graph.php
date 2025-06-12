<?php

namespace App\Models;

class Graph
{

    public $courses = array();
    public $links = array();

    public function addCourse(Course $newCourse)
    {
        if (count($this->courses) === 0) {
            $this->courses[] = $newCourse;
            return;
        }

        /**
         * @var Subject $subject
         */
        $newCourseSubject = $newCourse->subject;

        $newCourseProfessor = $newCourseSubject->professor;
        $newCourseLevels = $newCourseSubject->levels()->get()->all();

        /**
         * @var Course $course
         */
        foreach ($this->courses as $course) {
            /**
             * @var Subject $subject;
             */
            $subject = $course->subject;

            /* Contrainte pour la même professeur */
            if (
                $subject->professor === $newCourseProfessor
            ) {
                $this->addLink($course, $newCourse);
                $this->addLink($newCourse, $course);
                continue;
            } else {
                /** Contrainte pour la même niveau */
                $subjectLevels = $subject->levels()->get()->all();
                foreach ($subjectLevels as $level) {
                    if (in_array($level, $newCourseLevels)) {
                        $this->addLink($course, $newCourse);
                        $this->addLink($newCourse, $course);
                        break;
                    }
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
     * @param Course $a
     * @param Course $b
     * @return bool
     */
    public function linked(Course $a, Course $b)
    {
        if (
            in_array([$a, $b], $this->links)
        ) {
            return true;
        }
        return false;
    }
    /**
     * @param Course $course 
     * @return int
     */
    public function getVertexDegree(Course $course)
    {
        $count = 0;
        foreach ($this->links as $link) {
            if (in_array($course, $link)) $count++;
        }

        return $count / 2;
    }

    public function getNeighbors(Course $course): array
    {
        $results = array();
        foreach ($this->links as $link) {
            if (in_array($course, $link)) {
                $neighbor = $link[0] === $course ? $link[1] : $link[0];

                if (!in_array($neighbor, $results)) {
                    $results[] = $neighbor;
                }
            }
        }
        return $results;
    }
}
