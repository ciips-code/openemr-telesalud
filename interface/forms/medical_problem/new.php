<?php

/**
 * Medical Problem form new.php TeleHealth
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Alejandro Benavides <alejandro@meddyg.com>
 * @copyright Copyright (c) 2022 CIIPS (Argentina) - Meddyg <alejandro@meddyg.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once("../../globals.php");
require_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
 
use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Common\Uuid\UuidRegistry;
use OpenEMR\Core\Header;

$returnurl = 'encounter_top.php';
$formid = (int) ($_GET['id'] ?? 0);

?>

<html>
<head>
    <title><?php echo xlt("Medical Problem Form"); ?></title>

    <?php Header::setupHeader(['datetime-picker']); ?>
    <script>

    </script>
</head>
<body>
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-12">
                <h2><?php echo xlt('Medical Problem Form'); ?></h2>
                <form method='post' name='my_form' action='<?php echo $rootdir ?>/forms/medical_problem/save.php?id=<?php echo attr_url($formid) ?>'>
                    <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
                    <div class="container-fluid">

                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

