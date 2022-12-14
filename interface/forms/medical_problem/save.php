<?php

/**
 * Medical Problem form save.php
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Alejandro Benavides <alejandro@meddyg.com>
 * @copyright Copyright (c) 2022 CIIPS (Argentina) - Meddyg <alejandro@meddyg.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once(__DIR__ . "/../../globals.php");
require_once $GLOBALS['srcdir'] . '/lists.inc';
require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Services\PatientIssuesService;

if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"])) {
    CsrfUtils::csrfNotVerified();
}

if (!$encounter) { // comes from globals.php
    die(xlt("Internal error: we do not seem to be in an encounter!"));
}

$form_id = (int) (isset($_GET['id']) ? $_GET['id'] : '');

$icd11Codes         = $_POST['form_titles'];
$form_begin         = $_POST['form_begin'];
$form_end           = $_POST['form_end'];
$form_verification  = $_POST['form_verification'];
$form_outcome       = $_POST['form_outcome'];

$issueRecord = [
    'type' => 'medical_problem',
    'begdate' => $form_begin,
    'enddate' => $form_end,
    'returndate' => null,
    'erx_uploaded' => '0',
    'id' => $issue ?? null,
    'pid' => $_SESSION['pid'],
    'title' => 'Medical Problem ' . date("Y-m-d H:m:s"),
    'date' => date("Y-m-d H:m:s"),
    'activity' => 1,
    'user' => $_SESSION['authUser'],
    'groupname' => $_SESSION['authProvider'],
    'diagnosis' => implode(";", $icd11Codes)
];

$patientIssuesService = new PatientIssuesService();
$patientIssuesService->createIssue($issueRecord);

// For record/reporting purposes, place entry in lists_touch table.
setListTouch($_SESSION['pid'], 'medical_problem');

formHeader("Redirecting....");
formJump();
formFooter();