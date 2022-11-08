<?php
// Dependecia de las globales del OpenEmr
$p = $_SERVER['DOCUMENT_ROOT'];
require_once $p . '/vendor/autoload.php';

// use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
// use League\OAuth2\Server\Exception\OAuthServerException;
// use League\OAuth2\Server\ResourceServer;
// use Nyholm\Psr7\Factory\Psr17Factory;
// use Nyholm\Psr7Server\ServerRequestCreator;
// use OpenEMR\Common\Acl\AclMain;
// use OpenEMR\Common\Auth\OpenIDConnect\Repositories\AccessTokenRepository;
// use OpenEMR\Common\Http\HttpRestRequest;
use OpenEMR\Common\Logging\EventAuditLogger;
// use OpenEMR\Common\Logging\SystemLogger;
// use OpenEMR\Common\Session\SessionUtil;
// use OpenEMR\Services\TrustedUserService;
// use Psr\Http\Message\ResponseInterface;
// use Psr\Http\Message\ServerRequestInterface;
/**
 * Variables globales usadas dentro del modulo
 */
//
$telesalud_path = $p . '/telesalud';
//
$GLOBALS['OE_SITE_DIR'] = "$p/sites/default";
set_include_path(get_include_path() . PATH_SEPARATOR . $p);
require_once ($p . "/library/sql.inc");
require_once ($p . "/library/htmlspecialchars.inc.php");
require_once ($p . "/library/translation.inc.php");
// require_once ($p . "/custom/code_types.inc.php");
require_once ($p . "/interface/globals.php");
