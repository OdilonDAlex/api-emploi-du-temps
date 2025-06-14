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
                $classrooms = ClassRoom::all();
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

        // Logger::log("Graphs arcs: ");
        foreach($graph->links as $link) {
            // Logger::log("Arc: (" . $link[0]->subject->name. ", " . $link[1]->subject->name . ")");
        }    
    }

    public static function backtrackingSearch(Graph $graph)
    {
        CSP::prepareForCSP($graph);
        return CSP::backtracking(array(), $graph);
    }

    public static function backtracking(array $assignation, Graph $graph)
    {
        if (CSP::complete($assignation, $graph)) return $assignation;

        $course = CSP::unassignedVar($assignation, $graph);

        // Logger::log((string)count($assignation) . " " . $course->subject->name);

        $sortedDomainValues = CSP::domainValues($course, $graph, $assignation);

        foreach ($sortedDomainValues as $domain) {
            if (CSP::compatible($course, $domain)) {
                $oldDomains = $course->domains;

                $assignation[(string)$course->id] = $domain;

                $course->domains = [$domain];

                $result = CSP::inference($assignation, clone ($graph));

                if ($result['ok']) {
                    return CSP::backtracking($assignation, $graph);
                }

                $course->domains = $oldDomains;
                unset($assignation[(string)$course->id]);
            }
        }
        return false;
    }

    public static function revise(Course $toAC, Course $useToAC)
    {
        $changed = false;
        $toRemove = array();

        // Logger::log("Revise: (" . $toAC->subject->name . ", " . $useToAC->subject->name . ")");
        foreach ($toAC->domains as $toACDomain) {
            $contraintViolation = true;
            foreach ($useToAC->domains as $useToACDomain) {
                /**
                 * Verification des contraintes mêmes professeur ou même classe
                 */
                if (! CSP::sameTimeDomain($toACDomain, $useToACDomain)) {
                    $contraintViolation = false;
                    break;
                }
            }

            if ($contraintViolation) {
                $toRemove[] = $toACDomain;
                $changed = true;
            }
        }


        if ($changed) {
            $toAC->domains = array_filter($toAC->domains, fn($d) => !in_array($d, $toRemove));

            // Logger::log("Removed: ");
            foreach ($toRemove as $domain) {
                // Logger::log("(" . $domain->day->value . ", " . $domain->dayPart->value . ", "  . $domain->classroom->name . ")");
            }

            // Logger::log("From: " . $toAC->subject->name);
        }

        return [
            'reviseCourse' => $toAC,
            'changed' => $changed
        ];
    }

    public static function sameTimeDomain(Domain $a, Domain $b): bool
    {
        return ($a->day === $b->day) && ($a->dayPart === $b->dayPart);
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
    public static function inference(array $assignation, Graph $graph)
    {
        /**
         * @var Array<int, Array<int, Course>> $arcs
         */
        $arcs = $graph->links;
        $iteration = 0;

        while (count($arcs) > 0) {

            // Logger::log("Arcs count: " . count($arcs));

            $link = array_shift($arcs);

            // Logger::log("After shift: " . count($arcs));

            // Logger::log("Shifted link: " . "(" . $link[0]->subject->name . ", " . $link[1]->subject->name . " )");

            $iteration++;
            /**
             * Mettre $link sous forme AC ( Arc-Consistency) ( Arc Compatible )
             */

            $reviseResult = CSP::revise($link[0], $link[1]);

            if ($reviseResult['changed']) {

                if (count($link[0]->domains) === 0) {
                    return [
                        'ok' => false,
                    ];
                }

                $allNeighbors = $graph->getNeighbors($link[0]);
                $unAssignedNeighbors = CSP::removeAssignedVar($assignation, $allNeighbors);

                foreach ($unAssignedNeighbors as $n) {
                    $arcs[] = [$n, $link[0]];
                }
            }
        }

        return [
            'ok' => true,
            'graph' => $graph
        ];
    }

    public static function unassignedVar(array $assignation, Graph $graph): Course
    {
        $unAssignedVar = CSP::removeAssignedVar($assignation, $graph->courses);

        if (count($unAssignedVar) == 0) {
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

    public static function domainValues(Course $course, Graph $graph, array $assignation): array
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

        return $domainValues;
    }

    /**
     * Verification de la contrainte de nombre d'etudiant
     */
    public static function compatible(Course $course, Domain $domain): bool
    {
        return $course->getStudentsNumber() <= $domain->classroom->capacity;
    }
}
