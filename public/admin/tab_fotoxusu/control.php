<?php
// routes/fotoxusu.php

// GET /fotoxusu/inicio (opcional)
Flight::route('GET /fotoxusu/inicio', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $path_public;
    include $path_public . '/admin/tab_fotoxusu/inicio.php';
});

// GET /fotoxusu/listar
Flight::route('GET /fotoxusu/listar', function () {
    DB::query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
    $rows = DB::query("
        SELECT fotoxusu_id, usu_nom, img, is_valido, santo_id, me_gusta,
               DATE_FORMAT(fecha_creacion, '%Y-%m-%d %H:%i:%s') AS fecha_creacion
        FROM fotoxusu
        ORDER BY fotoxusu_id DESC
    ");
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
});

// helper: validar enum
function _val_santo($santo) {
    $allowed = ['santa rosa','sr de los milagros','san martin de porres'];
    return in_array($santo, $allowed, true) ? $santo : null;
}

// helper: guardar imagen como JPG (convierte PNG/GIF a JPG)
function _save_image_as_jpg($tmpPath, $destPathJpg) {
    $info = getimagesize($tmpPath);
    if(!$info) return false;
    $mime = $info['mime'] ?? '';

    if ($mime === 'image/jpeg') {
        return move_uploaded_file($tmpPath, $destPathJpg);
    }

    switch($mime){
        case 'image/png': $src = imagecreatefrompng($tmpPath); break;
        case 'image/gif': $src = imagecreatefromgif($tmpPath); break;
        default: $src = @imagecreatefromstring(file_get_contents($tmpPath)); break;
    }
    if(!$src) return false;

    $w = imagesx($src); $h = imagesy($src);
    $dst = imagecreatetruecolor($w, $h);
    $white = imagecolorallocate($dst, 255,255,255);
    imagefill($dst, 0, 0, $white);
    imagecopy($dst, $src, 0, 0, 0, 0, $w, $h);
    $ok = imagejpeg($dst, $destPathJpg, 85);
    imagedestroy($src); imagedestroy($dst);
    @unlink($tmpPath);
    return $ok;
}

// POST /fotoxusu/crear (multipart/form-data)
Flight::route('POST /fotoxusu/crear', function () {
    DB::query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");

    $usu_nom   = trim($_POST['usu_nom'] ?? '');
    $santo_id  = _val_santo($_POST['santo_id'] ?? '');
    $is_valido = isset($_POST['is_valido']) ? intval($_POST['is_valido']) : 1;

    if ($usu_nom === '' || !$santo_id) {
        Flight::json(['status'=>'error','msg'=>'Datos inválidos'], 400);
        return;
    }

    DB::startTransaction();
    try {
        DB::insert('fotoxusu', [
            'usu_nom'        => $usu_nom,
            'img'            => null, // se setea si suben archivo
            'is_valido'      => $is_valido,
            'santo_id'       => $santo_id,
            'me_gusta'       => 0,
            'fecha_creacion' => date('Y-m-d H:i:s')
        ]);
        $id = DB::insertId();

        // Imagen
        if (!empty($_FILES['img_file']['tmp_name'])) {

            $dir = VARPATH .'/pics/fotos';
            if (!is_dir($dir)) @mkdir($dir, 0775, true);
            $dest = $dir . "/{$id}.jpg";

            $tmpPath = $_FILES['img_file']['tmp_name'];
            if (_save_image_as_jpg($tmpPath, $dest)) {
                $url = "/pics/fotos/{$id}.jpg";
                DB::update('fotoxusu', ['img' => $url], "fotoxusu_id=%i", $id);
            }
        }

        DB::commit();
        Flight::json(['status'=>'ok','id'=>$id]);
    } catch (Exception $e) {
        DB::rollback();
        Flight::json(['status'=>'error','msg'=>$e->getMessage()], 500);
    }
});

// POST /fotoxusu/editar (multipart/form-data)
Flight::route('POST /fotoxusu/editar', function () {
    DB::query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");

    $fotoxusu_id = intval($_POST['fotoxusu_id'] ?? 0);
    $usu_nom     = trim($_POST['usu_nom'] ?? '');
    $santo_id    = _val_santo($_POST['santo_id'] ?? '');
    $is_valido   = isset($_POST['is_valido']) ? intval($_POST['is_valido']) : 1;

    if ($fotoxusu_id <= 0 || $usu_nom === '' || !$santo_id) {
        Flight::json(['status'=>'error','msg'=>'Datos inválidos'], 400);
        return;
    }

    DB::startTransaction();
    try {
        DB::update('fotoxusu', [
            'usu_nom'   => $usu_nom,
            'santo_id'  => $santo_id,
            'is_valido' => $is_valido
        ], "fotoxusu_id=%i", $fotoxusu_id);

        if (!empty($_FILES['img_file']['tmp_name'])) {
            global $path_public;
            $dir = VARPATH .'/pics/fotos';
            if (!is_dir($dir)) @mkdir($dir, 0775, true);
            $dest = $dir . "/{$fotoxusu_id}.jpg";

            $tmpPath = $_FILES['img_file']['tmp_name'];
            if (_save_image_as_jpg($tmpPath, $dest)) {
                $url = "/pics/fotos/{$fotoxusu_id}.jpg";
                DB::update('fotoxusu', ['img' => $url], "fotoxusu_id=%i", $fotoxusu_id);
            }
        }

        DB::commit();
        Flight::json(['status'=>'ok']);
    } catch (Exception $e) {
        DB::rollback();
        Flight::json(['status'=>'error','msg'=>$e->getMessage()], 500);
    }
});

// POST /fotoxusu/eliminar
Flight::route('POST /fotoxusu/eliminar', function () {
    DB::query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
    $data = Flight::request()->data->getData(); // JSON o x-www-form-urlencoded
    $fotoxusu_id = isset($data['fotoxusu_id']) ? intval($data['fotoxusu_id']) : 0;

    if ($fotoxusu_id <= 0) {
        Flight::json(['status'=>'error','msg'=>'ID inválido'], 400);
        return;
    }

    DB::startTransaction();
    try {
        global $path_public;
        $dest = rtrim($path_public, '/')."/pics/fotos/{$fotoxusu_id}.jpg";
        if (is_file($dest)) @unlink($dest);

        DB::delete('fotoxusu', "fotoxusu_id=%i", $fotoxusu_id);

        DB::commit();
        Flight::json(['status'=>'ok']);
    } catch (Exception $e) {
        DB::rollback();
        Flight::json(['status'=>'error','msg'=>$e->getMessage()], 500);
    }
});
