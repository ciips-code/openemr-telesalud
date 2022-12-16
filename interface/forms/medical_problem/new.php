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
use OpenEMR\Services\PatientIssuesService;

$returnurl = 'encounter_top.php';
$formid = (int) ($_GET['id'] ?? 0);
$thistype = 'medical_problem';
$irow = [];
$patientIssuesService = new PatientIssuesService();

?>

<html>
<head>
    <title><?php echo xlt("Medical Problem Form"); ?></title>

    <?php Header::setupHeader(['common', 'datetime-picker', 'select2']); ?>
    <script src="<?php echo $webroot ?>/interface/main/tabs/js/include_opener.js?v=<?php echo $v_js_includes; ?>"></script>
    <script>
        <?php require $GLOBALS['srcdir'] . '/formatting_DateToYYYYMMDD_js.js.php'; ?>
    </script>
</head>
<body>
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-12">
                <h2><?php echo xlt('Medical Problem Form'); ?></h2>
                <form method='post' name='my_form' class="row g-3 needs-validation" action='<?php echo $rootdir ?>/forms/medical_problem/save.php?id=<?php echo attr_url($formid) ?>' novalidate>
                    <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
                    <div class="container-fluid">
                        
                        <div class="row">
                            <div class="col-md-6 mt-2">
                                <label for="form_titles" class="form-label"><?php echo xlt('Type'); ?>: <?php echo xlt('Problem'); ?> </label>
                                <select name='form_titles[]' id='form_titles' class="form-control" required>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mt-2">
                                <label for="form_begin" class="form-label"><?php echo ('Date of onset') ?>: </label>
                                <input type="date" class="form-control" name='form_begin' id='form_begin'>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mt-2">
                                <label for="form_end" class="form-label"><?php echo ('Resolution date') ?>: </label>
                                <input type="date" class="form-control" name='form_end' id='form_end'>
                                (<?php echo xlt('leave blank if still active'); ?>)
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mt-3">
                                <label for="form_verification" class="form-label"><?php echo xlt('Confirmation status'); ?>: </label>
                                <?php
                                    $codeListName = ($thistype == 'medical_problem') ? 'condition-verification' : 'allergyintolerance-verification';
                                    echo generate_select_list('form_verification', $codeListName, null, '', '', '', '');
                                ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mt-3">
                                <label for="form_outcome" class="form-label"><?php echo xlt('Diagnosis Condition'); ?>: </label>
                                <?php
                                    echo generate_select_list('form_outcome', 'outcome', null, '', '', '', 'outcomeClicked(this);');
                                ?>
                            </div>
                        </div>
                        
                        <hr>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type='submit' name='form_save' class="btn btn-primary btn-save" value='<?php echo xla('Save'); ?>'>
                                    <?php echo xlt('Save'); ?>
                                </button>
                                <button type='button' class="btn btn-secondary btn-cancel" value='<?php echo xla("Cancel"); ?>' onclick="parent.closeTab(window.name, false)">
                                    <?php echo xla("Cancel"); ?>
                                </button>
                            </div>
                        </div>
                        
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        (() => {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            const forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })

            /**
             * TODO: Precargar: https://select2.org/programmatic-control/add-select-clear-items#preselecting-options-in-an-remotely-sourced-ajax-select2
             */
            $('#form_titles').select2({
                theme: 'bootstrap4',
                multiple: true,
                tags: true,
                tokenSeparators: [","],
                maximumSelectionSize: 10,
                minimumResultsForSearch: Infinity,
                minimumInputLength: 1,
                placeholder: <?php echo xlj('Buscar problema CIE-11'); ?>,
                templateResult: formatSearchResult,
                ajax: {
                    url: "<?php echo $webroot ?>/interface/patient_file/summary/search_icd11.php",
                    type: "post",
                    dataType: 'json',
                    delay: 500,
                    quietMillis: 100,
                    params: {
                        contentType: "application/json"
                    },
                    data: function (params) {
                        return {
                            searchTerm: params.term, // search term
                            delay: 0
                        }
                    },
                    processResults: function(response) {

                        return {
                            results: response
                        }
                    },
                    cache: true
                }
            });

            document.getElementById('form_begin').addEventListener('change', () => {
                validate()
            })
            document.getElementById('form_end').addEventListener('change', () => {
                validate()
            })

        })()

        // Called when resolved outcome is chosen and the end date is entered.
        function outcomeClicked(cb) {
            let f = document.forms[0];
            if (cb.value == '1') {
                let today = new Date();
                f.form_end.value = '' + (today.getYear() + 1900) + '-' +
                    ("0" + (today.getMonth() + 1)).slice(-2) + '-' + ("0" + today.getDate()).slice(-2);
                f.form_end.focus();
            }
        }

        function formatSearchResult(result) {
            if (!result.id) {
                return result.text;
            }

            var query = $('.select2-search__field').val();
            var str = result.text;

            var regex = new RegExp(query, 'i');
            var indexQuery = (str.toLowerCase()).indexOf(query.toLowerCase());

            var highlightText = str.substring(indexQuery, indexQuery + query.length);
            var newStr = str.replace(regex, '<span style="font-weight:bold">' + highlightText + '</span>')

            return $('<span>' + newStr + '</span>');
        }

        function validate() {
            var f = document.forms[0];
            var begin_date_val = f.form_begin.value;
            begin_date_val = begin_date_val ? DateToYYYYMMDD_js(begin_date_val) : begin_date_val;
            var end_date_val = f.form_end.value;
            end_date_val = end_date_val ? DateToYYYYMMDD_js(end_date_val) : end_date_val;
            var begin_date = new Date(begin_date_val);
            var end_date = new Date(end_date_val);

            if ((end_date_val) && (begin_date > end_date)) {
                alert(<?php echo xlj('Please Enter End Date greater than Begin Date!'); ?>);
                f.form_end.value = ''
                return false;
            } 

            // top.restoreSession();
            // return true;
        }

    </script>
</body>
</html>

