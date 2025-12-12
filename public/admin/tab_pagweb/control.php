<?php
// ========== INICIO ADMIN (opcional) ==========
Flight::route('GET /pagweb/inicio', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $path_public;
    include $path_public . '/admin/tab_pagweb/inicio.php';
});

// =================== PAGWEB ===================

// GET /pagweb/listar
Flight::route('GET /pagweb/listar', function() {
    DB::query("SET NAMES 'utf8'");
    $rows = DB::query("
        SELECT p.*,
          CASE WHEN IFNULL(p.url_img01,'')<>'' THEN CONCAT('" . vari('PICS_PAG_WEB_MINI') . "/', p.url_img01) END AS img01_mini,
          CASE WHEN IFNULL(p.url_img02,'')<>'' THEN CONCAT('" . vari('PICS_PAG_WEB_MINI') . "/', p.url_img02) END AS img02_mini
        FROM pagweb p
        ORDER BY p.pagweb_id DESC
    ");
    Flight::json($rows);
});


// POST /pagweb/crear
Flight::route('POST /pagweb/crear', function() {
    $data = Flight::request()->data->getData();
    $clave_txt  = trim($data['clave_txt']  ?? '');
    $titulo     = trim($data['titulo']     ?? '');
    $metatag01  = trim($data['metatag01']  ?? '');
    $metatag02  = trim($data['metatag02']  ?? '');
    $url_img01  = trim($data['url_img01']  ?? '');
    $url_img02  = trim($data['url_img02']  ?? '');

    if ($clave_txt === '' || $titulo === '') {
        Flight::json(['status'=>'error','msg'=>'Clave y Título son obligatorios'], 400);
        return;
    }
    DB::insert('pagweb', [
        'clave_txt' => $clave_txt,
        'titulo'    => $titulo,
        'metatag01' => $metatag01,
        'metatag02' => $metatag02,
        'url_img01' => $url_img01,
        'url_img02' => $url_img02
    ]);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status'=>'ok'], JSON_UNESCAPED_UNICODE);
});

// POST /pagweb/editar
Flight::route('POST /pagweb/editar', function() {
    $data = Flight::request()->data->getData();
    $id        = intval($data['pagweb_id'] ?? 0);
    $clave_txt = trim($data['clave_txt']   ?? '');
    $titulo    = trim($data['titulo']      ?? '');
    $metatag01 = trim($data['metatag01']   ?? '');
    $metatag02 = trim($data['metatag02']   ?? '');
    $url_img01 = trim($data['url_img01']   ?? '');
    $url_img02 = trim($data['url_img02']   ?? '');

    if ($id <= 0 || $clave_txt === '' || $titulo === '') {
        Flight::json(['status'=>'error','msg'=>'Datos inválidos'], 400);
        return;
    }
    DB::update('pagweb', [
        'clave_txt' => $clave_txt,
        'titulo'    => $titulo,
        'metatag01' => $metatag01,
        'metatag02' => $metatag02,
        'url_img01' => $url_img01,
        'url_img02' => $url_img02
    ], "pagweb_id=%i", $id);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status'=>'ok'], JSON_UNESCAPED_UNICODE);
});

// POST /pagweb/eliminar
Flight::route('POST /pagweb/eliminar', function() {
    $data = Flight::request()->data->getData();
    $id = intval($data['pagweb_id'] ?? 0);
    if ($id <= 0) {
        Flight::json(['status'=>'error','msg'=>'ID inválido'], 400);
        return;
    }

    DB::startTransaction();
    try {
        // Si quieres borrar en cascada los parrweb de esta página:
        DB::query("DELETE FROM parrweb WHERE pagweb_id = %i", $id);
        DB::delete('pagweb', "pagweb_id=%i", $id);
        DB::commit();
        Flight::json(['status'=>'ok']);
    } catch (Exception $e) {
        DB::rollback();
        Flight::json(['status'=>'error','msg'=>$e->getMessage()], 500);
    }
});

// =================== PARRWEB ===================

// GET /parrweb/listar/@pagweb_id   (detalle por página)
Flight::route('GET /parrweb/listar/@pagweb_id', function($pagweb_id) {
    $pid = intval($pagweb_id);
    if ($pid <= 0) { Flight::json([], 200); return; }
    DB::query("SET NAMES 'utf8'");
    $rows = DB::query("
        SELECT r.*,
          CASE WHEN IFNULL(r.url_img01,'')<>'' THEN CONCAT('" . vari('PICS_PARR_WEB_MINI') . "/', r.url_img01) END AS img01_mini,
          CASE WHEN IFNULL(r.url_img02,'')<>'' THEN CONCAT('" . vari('PICS_PARR_WEB_MINI') . "/', r.url_img02) END AS img02_mini,
          CASE WHEN IFNULL(r.url_img03,'')<>'' THEN CONCAT('" . vari('PICS_PARR_WEB_MINI') . "/', r.url_img03) END AS img03_mini,
          CASE WHEN IFNULL(r.url_img04,'')<>'' THEN CONCAT('" . vari('PICS_PARR_WEB_MINI') . "/', r.url_img04) END AS img04_mini
        FROM parrweb r
        WHERE r.pagweb_id = %i
        ORDER BY r.parrweb_id DESC
    ", $pid);
    Flight::json($rows);
});


// (Opcional) GET /parrweb/listar  — todos
Flight::route('GET /parrweb/listar', function() {
    DB::query("SET NAMES 'utf8'");
    $rows = DB::query("SELECT * FROM parrweb ORDER BY parrweb_id DESC");
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
});

// POST /parrweb/crear
Flight::route('POST /parrweb/crear', function() {
    $data = Flight::request()->data->getData();
    $pagweb_id  = intval($data['pagweb_id'] ?? 0);
    $titulo     = trim($data['titulo'] ?? '');
    $contenido  = $data['contenido'] ?? ''; // HTML de Summernote

    $url_video01 = trim($data['url_video01'] ?? '');
    $url_video02 = trim($data['url_video02'] ?? '');
    $url_video03 = trim($data['url_video03'] ?? '');
    $url_video04 = trim($data['url_video04'] ?? '');

    $url_img01 = trim($data['url_img01'] ?? '');
    $url_img02 = trim($data['url_img02'] ?? '');
    $url_img03 = trim($data['url_img03'] ?? '');
    $url_img04 = trim($data['url_img04'] ?? '');

    if ($pagweb_id <= 0 || $titulo === '') {
        Flight::json(['status'=>'error','msg'=>'Seleccione pagweb y escriba título'], 400);
        return;
    }

    DB::insert('parrweb', [
        'pagweb_id'  => $pagweb_id,
        'titulo'     => $titulo,
        'clave_txt'   => $clave_txt,
        'contenido'  => $contenido,
        'url_video01'=> $url_video01,
        'url_video02'=> $url_video02,
        'url_video03'=> $url_video03,
        'url_video04'=> $url_video04,
        'url_img01'  => $url_img01,
        'url_img02'  => $url_img02,
        'url_img03'  => $url_img03,
        'url_img04'  => $url_img04
    ]);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status'=>'ok'], JSON_UNESCAPED_UNICODE);
});

// POST /parrweb/editar
Flight::route('POST /parrweb/editar', function() {
    $data = Flight::request()->data->getData();
    $parrweb_id = intval($data['parrweb_id'] ?? 0);
    $pagweb_id  = intval($data['pagweb_id']  ?? 0);
    $titulo     = trim($data['titulo'] ?? '');
    $contenido  = $data['contenido'] ?? '';

    $clave_txt  = trim($data['clave_txt'] ?? ''); 

    $url_video01 = trim($data['url_video01'] ?? '');
    $url_video02 = trim($data['url_video02'] ?? '');
    $url_video03 = trim($data['url_video03'] ?? '');
    $url_video04 = trim($data['url_video04'] ?? '');

    $url_img01 = trim($data['url_img01'] ?? '');
    $url_img02 = trim($data['url_img02'] ?? '');
    $url_img03 = trim($data['url_img03'] ?? '');
    $url_img04 = trim($data['url_img04'] ?? '');

    if ($parrweb_id <= 0 || $pagweb_id <= 0 || $titulo === '') {
        Flight::json(['status'=>'error','msg'=>'Datos inválidos'], 400);
        return;
    }

    DB::update('parrweb', [
        'pagweb_id'  => $pagweb_id,
        'titulo'     => $titulo,
        'contenido'  => $contenido,
        'url_video01'=> $url_video01,
        'url_video02'=> $url_video02,
        'url_video03'=> $url_video03,
        'url_video04'=> $url_video04,
        'url_img01'  => $url_img01,
        'url_img02'  => $url_img02,
        'url_img03'  => $url_img03,
        'url_img04'  => $url_img04,
        'clave_txt'  => $clave_txt
    ], "parrweb_id=%i", $parrweb_id);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status'=>'ok'], JSON_UNESCAPED_UNICODE);
});

// POST /parrweb/eliminar
Flight::route('POST /parrweb/eliminar', function() {
    $data = Flight::request()->data->getData();
    $id = intval($data['parrweb_id'] ?? 0);
    if ($id <= 0) {
      Flight::json(['status'=>'error','msg'=>'ID inválido'], 400);
      return;
    }
    DB::delete('parrweb', "parrweb_id=%i", $id);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status'=>'ok'], JSON_UNESCAPED_UNICODE);
});

// ================== Helpers de imagen ==================
function asegurar_dir($absPath) {
    $dir = dirname($absPath);
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

function procesarImagenGenerica(array $fileInfo, string $subdirFull, string $subdirMini, string $filename): array {
    // $subdirFull y $subdirMini deben ser como 'pics/pag_web/full' (sin barra inicial)
    $si = new SimpleImage();

    // FULL
    $si->load($fileInfo['tmp_name']);
    $maxFull = (int) vari('IMG_FULL');
    if ($si->getWidth() > $maxFull) {
        $si->resizeToWidth($maxFull);
    }
    $fullAbs = rtrim(VARPATH, '/').'/'.trim($subdirFull, '/').'/'.$filename;
    asegurar_dir($fullAbs);
    $si->save($fullAbs, $si->tipo_de_imagen(), 75);

    // MINI (recarga original)
    $si->load($fileInfo['tmp_name']);
    $maxMini = (int) vari('IMG_MINI');
    if ($si->getWidth() > $maxMini) {
        $si->resizeToWidth($maxMini);
    }
    $miniAbs = rtrim(VARPATH, '/').'/'.trim($subdirMini, '/').'/'.$filename;
    asegurar_dir($miniAbs);
    $si->save($miniAbs, $si->tipo_de_imagen(), 75);

    // URLs públicas (asumiendo que subdir* es público bajo la misma estructura)
    $fullUrl = '/'.trim($subdirFull, '/').'/'.$filename;
    $miniUrl = '/'.trim($subdirMini, '/').'/'.$filename;

    return ['full_abs'=>$fullAbs,'mini_abs'=>$miniAbs,'full_url'=>$fullUrl,'mini_url'=>$miniUrl];
}

function nombre_archivo_seguro($originalName, $prefix='img') {
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION) ?: 'jpg');
    $base = $prefix.'_'.date('Ymd_His').'_'.mt_rand(1000,9999);
    // evita espacios / caracteres raros
    return preg_replace('/[^a-zA-Z0-9_\-\.]/','', $base).'.'.$ext;
}

// ================== SUBIDA: PAGWEB ==================
// POST /pagweb/subir_img (multipart)
// campos: pagweb_id (int), campo (url_img01|url_img02), archivo (file)
Flight::route('POST /pagweb/subir_img', function() {
    $pagweb_id = intval($_POST['pagweb_id'] ?? 0);
    $campo     = trim($_POST['campo'] ?? '');
    $fileInfo  = $_FILES['archivo'] ?? null;

    if ($pagweb_id <= 0 || !in_array($campo, ['url_img01','url_img02'], true) || !$fileInfo) {
        Flight::json(['status'=>'error','msg'=>'Datos inválidos'], 400);
        return;
    }

    // Generar nombre de archivo
    $ext = strtolower(pathinfo($fileInfo['name'] ?? '', PATHINFO_EXTENSION) ?: 'jpg');
    $filename = generar_nombre_corto_unico(
        $ext,
        trim(vari('PICS_PAG_WEB_FULL'), '/'),
        trim(vari('PICS_PAG_WEB_MINI'), '/')
    );

    $res = procesarImagenGenerica(
        $fileInfo,
        trim(vari('PICS_PAG_WEB_FULL'), '/'),
        trim(vari('PICS_PAG_WEB_MINI'), '/'),
        $filename
    );

    // Guardar filename en DB
    DB::update('pagweb', [ $campo => $filename ], "pagweb_id=%i", $pagweb_id);

    // respuesta
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status'   => 'ok',
        'filename' => $filename,
        'full_url' => $res['full_url'],
        'mini_url' => $res['mini_url']
    ], JSON_UNESCAPED_UNICODE);
});

// ================== SUBIDA: PARRWEB ==================
// POST /parrweb/subir_img (multipart)
// campos: parrweb_id (int), campo (url_img01..url_img04), archivo (file)
Flight::route('POST /parrweb/subir_img', function() {
    $parrweb_id = intval($_POST['parrweb_id'] ?? 0);
    $campo      = trim($_POST['campo'] ?? '');
    $fileInfo   = $_FILES['archivo'] ?? null;

    if ($parrweb_id <= 0 || !in_array($campo, ['url_img01','url_img02','url_img03','url_img04'], true) || !$fileInfo) {
        Flight::json(['status'=>'error','msg'=>'Datos inválidos'], 400);
        return;
    }

    $ext = strtolower(pathinfo($fileInfo['name'] ?? '', PATHINFO_EXTENSION) ?: 'jpg');
    $filename = generar_nombre_corto_unico(
        $ext,
        trim(vari('PICS_PARR_WEB_FULL'), '/'),
        trim(vari('PICS_PARR_WEB_MINI'), '/')
    );

    $res = procesarImagenGenerica(
        $fileInfo,
        trim(vari('PICS_PARR_WEB_FULL'), '/'),
        trim(vari('PICS_PARR_WEB_MINI'), '/'),
        $filename
    );


    DB::update('parrweb', [ $campo => $filename ], "parrweb_id=%i", $parrweb_id);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status'   => 'ok',
        'filename' => $filename,
        'full_url' => $res['full_url'],
        'mini_url' => $res['mini_url']
    ], JSON_UNESCAPED_UNICODE);
});


// GET /pagweb/detalle/@id
Flight::route('GET /pagweb/detalle/@id', function($id){
    $pid = intval($id);
    if ($pid <= 0) { Flight::json(['status'=>'error','msg'=>'ID inválido'], 400); return; }

    DB::query("SET NAMES 'utf8'");
    $row = DB::queryFirstRow("SELECT * FROM pagweb WHERE pagweb_id = %i", $pid);
    if (!$row) { Flight::json(['status'=>'error','msg'=>'No encontrado'], 404); return; }

    // agrega rutas desde vari()
    $pics = [
      'mini' => vari('PICS_PAG_WEB_MINI'),
      'full' => vari('PICS_PAG_WEB_FULL')
    ];
    Flight::json(['status'=>'ok','data'=>$row,'pics'=>$pics]);
});


// GET /parrweb/detalle/@id
Flight::route('GET /parrweb/detalle/@id', function($id){
    $rid = intval($id);
    if ($rid <= 0) { Flight::json(['status'=>'error','msg'=>'ID inválido'], 400); return; }

    DB::query("SET NAMES 'utf8'");
    $row = DB::queryFirstRow("SELECT * FROM parrweb WHERE parrweb_id = %i", $rid);
    if (!$row) { Flight::json(['status'=>'error','msg'=>'No encontrado'], 404); return; }

    $pics = [
      'mini' => vari('PICS_PARR_WEB_MINI'),
      'full' => vari('PICS_PARR_WEB_FULL')
    ];
    Flight::json(['status'=>'ok','data'=>$row,'pics'=>$pics]);
});

/**
 * Genera un nombre corto (4 chars alfanuméricos) + extensión,
 * garantizando que no exista ni en FULL ni en MINI.
 */
function generar_nombre_corto_unico(string $ext, string $subdirFull, string $subdirMini, int $len = 4, int $maxTries = 128): string {
    $ext = strtolower($ext ?: 'jpg');
    // Opcional: blanqueo de extensión a formatos comunes
    if (!in_array($ext, ['jpg','jpeg','png','gif','webp'], true)) {
        $ext = 'jpg';
    }

    $alphabet = '0123456789abcdefghijklmnopqrstuvwxyz'; // 36^4 = 1,679,616 combinaciones
    $baseFull = rtrim(VARPATH, '/') . '/' . trim($subdirFull, '/');
    $baseMini = rtrim(VARPATH, '/') . '/' . trim($subdirMini, '/');

    for ($i = 0; $i < $maxTries; $i++) {
        // Código aleatorio
        $code = '';
        for ($j = 0; $j < $len; $j++) {
            $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        $filename = $code . '.' . $ext;

        // Unicidad por existencia física
        if (!file_exists($baseFull . '/' . $filename) && !file_exists($baseMini . '/' . $filename)) {
            return $filename;
        }
    }

    // Fallback ultra improbable
    return substr(bin2hex(random_bytes(4)), 0, $len) . '.' . $ext;
}
