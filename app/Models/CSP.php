<?php

namespace App\Models;

use App\Enums\DayPart;
use App\Enums\WeekDay;
use ConstraintType;
use Exception;

class CSP
{

    public static function prepareForCSP(Graph $graph)
    {

        /**
         * Création du domaines de depart
         */
        $domains = array();
        foreach (WeekDay::cases() as $day) {
            foreach (DayPart::cases() as $dayPart) {
                $classrooms = ClassRoom::all()->toArray();
                foreach ($classrooms as $classroom) {
                    $domains[] = new Domain($day, $dayPart, $classroom);
                }
            }
        }

        /**
         * Toutes les cours auront la même domaine au debut du processus
         */
        foreach ($graph->courses as $course) {
            $course->domains = $domains;
        }

        return $graph;
    }

    public static function backtrackingSearch(Graph $graph)
    {
        return CSP::backtracking(array(), CSP::prepareForCSP($graph));
    }

    public static function backtracking(array $assignation, Graph $graph)
    {
        if (CSP::complete($assignation, $graph)) return $assignation;

        $course = CSP::unassignedVar($assignation, $graph);

        $sortedDomainValues = CSP::domainValues($course, $graph, $assignation);

        foreach ($sortedDomainValues as $domain) {
            if (CSP::compatible($course, $domain)) {
                $assignation[(string)$course->id] = $domain;

                $result = CSP::inference();

                if ($result['ok']) {
                    $graph = $result['graph'];
                    return CSP::backtracking($assignation, $graph);
                }

                unset($assignation[(string)$course->id]);
            }
        }
        return false;
    }

    public static function revise(Course $toAC, Course $useToAC, Graph $graph)
    {
        foreach ($toAC->domains as $toACDomain) {
            $contraintViolatin = true;
            foreach ($useToAC->domains as $useToACDomain) {
            }
        }
    }

    public static function compatibleAssignation(Course $A, Domain $Adomain, Course $B, Graph $graph): bool
    {
        foreach ($B->domains as $domain) {
        }

        return true;
    }

    public static function contraintViolation(Domain $domain, Course $course, ConstraintType $contraintType)
    {


        switch ($contraintType) {
            case ConstraintType::SAME_PROFESSOR: {

                    break;
                }
        }
    }

    public static function sameProfessorViolationAssignation(Domain $domain, Course $course)
    {
        foreach ($course->domain as $d) {
            // if($domain->)
        }
    }

    // AC3 algorithm
    public static function inference(Graph $graph)
    {
        $arcs = $graph->links;

        while (count($arcs) > 0) {
            $link = array_shift($arcs);

            $reviseResult = CSP::revise($link[0], $link[1], $graph);
        }

        return true;
    }

    public static function unassignedVar(array $assignation, Graph $graph): Course
    {
        $unAssignedVar = CSP::removeAssignedVar($assignation, $graph->courses);
        if (count($unAssignedVar)) {
            throw new Exception("Aucun variable qui n'ont pas de valeur trouver, et pourtant l'algorithme n'est pas fini");
        }

        /**
         * MRV: Minimum Remaining Values
         */
        $mrv = function (Course $courseA, Course $courseB) {
            return count($courseA->domains) < count($courseB->domains) ? -1 : 1;
        };

        usort($unAssignedVar, $mrv);

        return $unAssignedVar[0];
    }

    public static function removeAssignedVar(array $assignation, array $courses)
    {
        return array_filter($courses, fn($course) => !array_key_exists((string)$course->id, $assignation));
    }

    public static function complete(array $assignation, Graph $graph): Bool
    {
        return count($assignation) === count($graph->courses);
    }

    public static function domainValues(Course $course, Graph $graph, array $assignation): Domain
    {
        $allNeighbors = $graph->getNeighbors($course);
        $unAssignedNeighbors = CSP::removeAssignedVar($assignation, $allNeighbors);
        $domainValues = $course->domains;

        /**
         * LCV: Least-Contraining Values
         */
        $lcv = function (Domain $a, Domain $b) use ($unAssignedNeighbors): int {
            $usingADomainImpactCount = 0;
            $usingBDomainImpactCount = 0;

            foreach ($unAssignedNeighbors as $n) {
                if (in_array($a, $n->domains)) $usingADomainImpactCount++;
                if (in_array($b, $n->domains)) $usingADomainImpactCount++;
            }

            return $usingADomainImpactCount < $usingBDomainImpactCount ? -1 : 1;
        };

        usort($domainValues, $lcv);

        return $domainValues[0];
    }

    public static function compatible(Course $course, Domain $domain): bool
    {
        return true;
    }
}
