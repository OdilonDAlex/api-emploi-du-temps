<?php

namespace App\Models;

class Graph
{

    public $courses = array();
    public $links = array();

    public function addCourse(Course $newCourse)
    {

        // Logger::log("Add Course: " . $newCourse->subject->name);
        if (count($this->courses) === 0) {
            // Logger::log("Courses empty");
            $this->courses[] = $newCourse;
            return;
        }


        if (in_array($newCourse, $this->courses)) {
            // Logger::log("Courses exists");
            return;
        };

        /**
         * @var Subject $subject
         */
        $newCourseSubject = $newCourse->subject;
        $newCourseProfessor = $newCourseSubject->professor;
        $newCourseAcademicTracksId = $newCourseSubject->academicTracks()->pluck('id')->toArray();

        /**
         * @var Course $course
         */
        foreach ($this->courses as $course) {

            // Logger::log("Comparing: " . $newCourseSubject->name . " AND " . $course->subject->name);
            /**
             * @var Subject $subject;
             */
            $subject = $course->subject;

            /* Contrainte pour la même professeur */
            if (
                $subject->professor->id === $newCourseProfessor->id
            ) {
                // Logger::log("Same Professor: " . $newCourseSubject->name . " AND " . $course->subject->name);
                $this->addLink($course, $newCourse);
                $this->addLink($newCourse, $course);
                continue;
            } else {
                /** Contrainte pour la même niveau */
                $subjectAcademicTracksId  = $subject->academicTracks()->pluck('id')->toArray();

                // Logger::log("Not Same Professor, comparing levels: " . $newCourseSubject->name . " AND " . $course->subject->name);
                foreach ($subjectAcademicTracksId as $levelId) {
                    if (in_array($levelId, $newCourseAcademicTracksId)) {
                        // Logger::log("Same Level: " . $newCourseSubject->name . " AND " . $course->subject->name);
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
