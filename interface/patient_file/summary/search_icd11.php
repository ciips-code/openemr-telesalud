<?php

/**
 * add or edit a medical problem.
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Rod Roark <rod@sunsetsystems.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @author    Thomas Pantelis <tompantelis@gmail.com>
 * @copyright Copyright (c) 2005-2016 Rod Roark <rod@sunsetsystems.com>
 * @copyright Copyright (c) 2017-2018 Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2020 Thomas Pantelis <tompantelis@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once '../../globals.php';
require_once $GLOBALS['srcdir'] . '/lists.inc';
require_once $GLOBALS['srcdir'] . '/patient.inc';
require_once $GLOBALS['srcdir'] . '/options.inc.php';
require_once $GLOBALS['fileroot'] . '/custom/code_types.inc.php';
require_once $GLOBALS['srcdir'] . '/csv_like_join.php';

$term = $_POST['searchTerm'];
$response = [];
$lang = getICD11LangId();

$query = sqlStatement(
    "SELECT codes as id, title as text
    FROM list_options
    WHERE
        list_id = 'medical_problem_issue_list' AND
        activity = 1 AND
        lang_id = $lang AND
        (title LIKE '$term%' OR codes LIKE '$term%')
    ORDER BY CHAR_LENGTH(title) LIMIT 100");

while ($row = sqlFetchArray($query)) {
    $response[] = $row;
}

echo json_encode($response);
