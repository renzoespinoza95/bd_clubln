<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Lima');

define('VARPATH', dirname(__FILE__));
define ('DEFINITION', VARPATH . "/app/definition.php");

require 'flight/Flight.php';

require_once VARPATH."/inc/config.inc.php";
require_once VARPATH."/classes/util.class.php";
require_once VARPATH."/classes/h2.class.php";
require_once VARPATH."/classes/Meekrodb2.class.php";
require_once VARPATH."/classes/paginator.class.php";
require_once VARPATH."/classes/amarilis.class.php";
require_once VARPATH."/classes/Mustache.class.php";
// require_once VARPATH."/vendor/PHPExcel/Classes/PHPExcel.php";
require_once VARPATH."/classes/Lorem.class.php";
require_once VARPATH."/classes/boot.class.php";
require_once VARPATH."/classes/commons.php";


boot::config($varhost);   

// Meekro
DB::$user = $username;
DB::$password = $password;
DB::$dbName = $dbname;
DB::$host = $host;
DB::query("SET NAMES 'utf8'");

$path_public = VARPATH . "/public";

$sesion_admin_administrador_id = $_COOKIE['sesion_admin_administrador_id_' . $nombre_app] ?? null;
$sesion_admin_administrador_email = $_COOKIE['sesion_admin_administrador_email_' . $nombre_app] ?? null;


if (util::_es_apache()) {
    // Headers CORS (una sola vez)
    if (!headers_sent()) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Requested-With');
        header('Access-Control-Expose-Headers: Content-Length, Content-Range');
    }

    // Preflight OPTIONS (solo bajo Apache)
    Flight::route('OPTIONS *', function () {
        if (!headers_sent()) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Requested-With');
            header('Access-Control-Max-Age: 86400');
        }
        // 204 = No Content (mejor que 200 "OK" para preflight)
        Flight::halt(204);
    });
}


include VARPATH. "/classes/SimpleImage.class.php";
include VARPATH. "/classes/upload.class.php";

require_once VARPATH."/app/gatti.control.php";

$upload = new Upload;
$simple_image = new SimpleImage();
// $objPHPExcel = new PHPExcel();

// dd("ss", $sesion_admin_administrador_id);

if($sesion_admin_administrador_id) {
    // AUTENTIFICACION
    //================
    $valor_key = $nombre_app . vari("KEY");              
    $administrador_id = str_replace("*", "", util::decrypt($sesion_admin_administrador_id, $valor_key));              
    $info_admin = login_admin::informacion_administrador_por_id(util::decrypt($sesion_admin_administrador_id,$valor_key ));

} 

$administrador_actual = login_admin::informacion_administrador_por_id($sesion_admin_administrador_id);

Flight::set('flight.handle_errors', false);

Flight::start();

