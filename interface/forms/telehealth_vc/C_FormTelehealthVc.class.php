<?php

/**
 * Telehealth Video Consultations Controller
 *
 * @package   TelesaludVideoConsultationModule
 * @link      https://github.com/ciips-code/openemr-telesalud/
 * @author    YOIS <sioy23@gmail.com>
 * @author    YOIS <sioy23@gmail.com>
 * @copyright Copyright (c) 2022 - 2023 YOIS <sioy23@gmail.com>
 * @copyright Copyright (c) 2022 - 2023 YOIS<sioy23@gmail.com>
 * @license   https://github.com/ciips-code/openemr-telesalud/LICENCE
 */


require_once($GLOBALS['fileroot'] . "/library/forms.inc");
require_once("FormTelehealthVc.class.php");



use OpenEMR\Common\Csrf\CsrfUtils;

class C_FormTeleHealthVC extends Controller
{
    var $template_dir;

    function __construct($template_mod = "general")
    {
        parent::__construct();
        // $this->template_mod = $template_mod;
        // $this->template_dir = dirname(__FILE__) . "/templates/";
        // $this->assign("FORM_ACTION", $GLOBALS['web_root']);
        // $this->assign("DONT_SAVE_LINK", $GLOBALS['form_exit_url']);
        // $this->assign("STYLE", $GLOBALS['style']);
        // $this->assign("CSRF_TOKEN_FORM", CsrfUtils::collectCsrfToken());
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    function default_action()
    {
        $form = new FormSOAP();
        $this->assign("data", $form);
        return $this->fetch($this->template_dir . $this->template_mod . "_new.html");
    }
    /**
     * Undocumented function
     *
     * @param [type] $form_id
     * @return void
     */
    function view_action($form_id)
    {
        if (is_numeric($form_id)) {
            $form = new FormSOAP($form_id);
        } else {
            $form = new FormSOAP();
        }

        $dbconn = $GLOBALS['adodb']['db'];

        $this->assign("data", $form);

        return $this->fetch($this->template_dir . $this->template_mod . "_new.html");
    }
}
