<?php
// Ruta raíz

Flight::route('GET /', function() {
    include DEFINITION;
    $mBase = $varhost . "/html/barsi/";
    $clave_txt = 'BARSI_INICIO';
    include VARPATH."/html/barsi/inicio.php";
});

Flight::route('GET /barsi/inicio', function() {
    include DEFINITION;
    $mBase = $varhost . "/html/barsi/";
    $clave_txt = 'BARSI_INICIO';
    include VARPATH."/html/barsi/inicio.php";
});

Flight::route('GET /barsi/privacidad', function() {
   include DEFINITION;
    $mBase = $varhost . "/html/barsi/";
    $clave_txt = 'BARSI_PRIVACIDAD';
    include VARPATH."/html/barsi/inicio.php";
});

/* ---------- Helper: lista parrweb por clave_txt de pagweb ---------- */
function listarParrwebPorClave(string $clave_txt): array {
    $clave_txt = trim($clave_txt);
    if ($clave_txt === '') return [];

    DB::query("SET NAMES 'utf8'");
    $rows = DB::query("
        SELECT
            r.*,
            p.pagweb_id,
            p.clave_txt,
            p.titulo AS pagweb_titulo,
            /* minis ya listos para frontend */
            CASE WHEN IFNULL(r.url_img01,'')<>'' THEN CONCAT('" . vari('PICS_PARR_WEB_MINI') . "/', r.url_img01) END AS img01_mini,
            CASE WHEN IFNULL(r.url_img02,'')<>'' THEN CONCAT('" . vari('PICS_PARR_WEB_MINI') . "/', r.url_img02) END AS img02_mini,
            CASE WHEN IFNULL(r.url_img03,'')<>'' THEN CONCAT('" . vari('PICS_PARR_WEB_MINI') . "/', r.url_img03) END AS img03_mini,
            CASE WHEN IFNULL(r.url_img04,'')<>'' THEN CONCAT('" . vari('PICS_PARR_WEB_MINI') . "/', r.url_img04) END AS img04_mini
        FROM parrweb r
        INNER JOIN pagweb p ON p.pagweb_id = r.pagweb_id
        WHERE p.clave_txt = %s
        ORDER BY r.parrweb_id DESC
    ", $clave_txt);

    return $rows;
}

/* ---------- Ruta: GET /parrweb/listar_por_clave/@clave ---------- */
Flight::route('GET /parrweb/listar_por_clave/@clave', function($clave) {
    $clave = urldecode($clave);            // admite %20, etc.
    $rows  = listarParrwebPorClave($clave);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
});

/* ===== Detalle Pagweb por clave_txt ===== */

function detallePagwebPorClave(string $clave_txt) {
    $clave_txt = trim($clave_txt);
    if ($clave_txt === '') {
        Flight::json(['status'=>'error','msg'=>'Clave vacía'], 400);
        return;
    }

    DB::query("SET NAMES 'utf8'");
    // Si tu colación es case-insensitive, no hace falta LOWER().
    $row = DB::queryFirstRow("SELECT * FROM pagweb WHERE clave_txt = %s LIMIT 1", $clave_txt);

    if (!$row) {
        Flight::json(['status'=>'error','msg'=>'No encontrado'], 404);
        return;
    }

    $pics = [
        'mini' => vari('PICS_PAG_WEB_MINI'),
        'full' => vari('PICS_PAG_WEB_FULL')
    ];
    Flight::json(['status'=>'ok','data'=>$row,'pics'=>$pics]);
}

/* Ruta: GET /pagweb/detalle_por_clave/@clave */
Flight::route('GET /pagweb/detalle_por_clave/@clave', function($clave) {
    // admite %20, etc.
    $clave = urldecode($clave);
    detallePagwebPorClave($clave);
});


function infoPagwebPorClave(string $clave_txt) {
    $clave_txt = trim($clave_txt);
    if ($clave_txt === '') {
        Flight::json(['status'=>'error','msg'=>'Clave vacía'], 400);
        return;
    }

    DB::query("SET NAMES 'utf8'");
    // Si tu colación es case-insensitive, no hace falta LOWER().
    $row = DB::queryFirstRow("SELECT * FROM pagweb WHERE clave_txt = %s LIMIT 1", $clave_txt);

    return $row;
}

// GET /parrweb/detalle_por_clave/@clave
Flight::route('GET /parrweb/detalle_por_clave/@clave', function($clave){
    $clave = trim(urldecode($clave));
    if ($clave === '') {
        Flight::json(['status'=>'error','msg'=>'Clave vacía'], 400);
        return;
    }

    DB::query("SET NAMES 'utf8'");
    $row = DB::queryFirstRow("
        SELECT r.*
        FROM parrweb r
        WHERE r.clave_txt = %s
        ORDER BY r.parrweb_id DESC
        LIMIT 1
    ", $clave);

    if (!$row) {
        Flight::json(['status'=>'error','msg'=>'No encontrado'], 404);
        return;
    }

    $pics = [
        'mini' => vari('PICS_PARR_WEB_MINI'),
        'full' => vari('PICS_PARR_WEB_FULL')
    ];
    Flight::json(['status'=>'ok','data'=>$row,'pics'=>$pics]);
});
