<?php

namespace App\Models;

use ConstraintType;

class CSP {

    public static function backtrackingSearch(Graph $graph) {
        return CSP::backtracking(array(), $graph);
    }

    public static function backtracking(Array $assignation, Graph $graph) {
        if(CSP::complete($assignation, $graph)) return $assignation;
        $course = CSP::unassignedVar($assignation, $graph);
        $sortedDomainValues = CSP::domainValues($course, $graph, $assignation);
        foreach($sortedDomainValues as $domain) {
            if(CSP::compatible($course, $domain)) {
                $assignation[(string)$course->id] = $domain;

                $result = CSP::inference();

                if($result['ok']) {
                    $graph = $result['graph'];
                    return CSP::backtracking($assignation, $graph);
                }

                unset($assignation[(string)$course->id]);
            }
        }
        return false;
    }

    public static function revise(Course $toAC, Course $useToAC, Graph $graph) {
        foreach($toAC->domains as $toACDomain) {
            $contraintViolatin = true;
            foreach($useToAC->domains as $useToACDomain) {

            }
        }
    }

    public static function compatibleAssignation(Course $A, Domain $Adomain, Course $B, Graph $graph): bool {
        foreach($B->domains as $domain) {
            
        }

        return true;
    }

    public static function contraintViolation(Domain $domain, Course $course, ConstraintType $contraintType) {


        switch($contraintType) {
            case ConstraintType::SAME_PROFESSOR: {

                break;
            }
        }
    }

    public static function sameProfessorViolationAssignation(Domain $domain, Course $course) {
        foreach($course->domain as $d) {
            // if($domain->)
        }
    }

    // AC3 algorithm
    public static function inference(Graph $graph) {
        $arcs = $graph->links;

        while (count($arcs) > 0) {
            $link = array_shift($arcs);

            $reviseResult = CSP::revise($link[0], $link[1], $graph);
        }

        return true;
    }

    public static function unassignedVar(Array $assignation, Graph $graph): Course {
        return new Course();
    }

    public static function complete(Array $assignation, Graph $graph): Bool {
        return count($assignation) === count($graph->courses);
    } 

    public static function domainValues(Course $course, Graph $graph, Array $assignation) {
        // least-constraining first
        return array();
    }

    public static function compatible(Course $course, Domain $domain): bool {
        return true;
    }
}