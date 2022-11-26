<?php

/**
 * Telesalud Video Consultations Module for OpenEMR.
 *
 * @package   TelesaludVideoConsultationModule
 * @link      https://github.com/ciips-code/openemr-telesalud/
 * @author    YOIS <sioy23@gmail.com>
 * @author    YOIS <sioy23@gmail.com>
 * @copyright Copyright (c) 2012-2013 YOIS <sioy23@gmail.com>
 * @copyright Copyright (c) 2019 YOIS <sioy23@gmail.com>
 * @license   https://github.com/ciips-code/openemr-telesalud/LICENCE
 */

echo "listado ";
// require_once(dirname(__FILE__) . '/../../globals.php');
// require_once($GLOBALS["srcdir"] . "/api.inc");

// function treatment_plan_report($pid, $encounter, $cols, $id)
// {
//     $count = 0;
//     $data = formFetch("form_treatment_plan", $id);
//     if ($data) {
//         print "<table><tr>";
//         foreach ($data as $key => $value) {
//             if ($key == "id" || $key == "pid" || $key == "user" || $key == "groupname" || $key == "authorized" || $key == "activity" || $key == "date" || $value == "" || $value == "0000-00-00 00:00:00") {
//                 continue;
//             }

//             if ($value == "on") {
//                 $value = "yes";
//             }

//             $key = ucwords(str_replace("_", " ", $key));
//             print "<td><span class=bold>" . xlt($key) . ": </span><span class=text>" . text($value) . "</span></td>";
//             $count++;
//             if ($count == $cols) {
//                 $count = 0;
//                 print "</tr><tr>\n";
//             }
//         }
//     }

//     print "</tr></table>";
// }

