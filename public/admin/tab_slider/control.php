<?php
/**
 * Recibe $_FILES['img'] y un nombre de destino.
 * - No escala hacia arriba si la imagen es más pequeña.
 * - Castea a int para evitar warnings de float->int.
 */
function procesarImagenSlider(array $file, string $destName): array {
    $si = new SimpleImage();
    $si->load($file['tmp_name']);
    $originalWidth = $si->getWidth();

    // FULL
    $maxFull = (int) vari('IMG_FULL');
    if ($originalWidth > $maxFull) {
        $si->resizeToWidth($maxFull);
    }
    // guarda full (tal cual si no se redimensionó)
    $rutaFull = VARPATH . vari('PICS_SLIDER_FULL') . '/' . $destName;
    $si->save($rutaFull);

    // MINI: recargamos la original para no aplicar doble resize
    $si->load($file['tmp_name']);
    $originalWidth = $si->getWidth();
    $maxMini = (int) vari('IMG_MINI');
    if ($originalWidth > $maxMini) {
        $si->resizeToWidth($maxMini);
    }
    $rutaMini = VARPATH . vari('PICS_SLIDER_MINI') . '/' . $destName;
    $si->save($rutaMini);

    return ['full' => $rutaFull, 'thumb' => $rutaMini];
}
/* -------------------------- */
/* Vistas SLIDER             */
/* -------------------------- */
Flight::route('GET /slider/inicio', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $path_public;
    include $path_public . '/admin/tab_slider/inicio.php';
});

/* ----------------- */
/* CRUD de “slider” */
/* ----------------- */

/* LISTAR */
// ANTES (Local path):
// CONCAT('" . vari('PICS_SLIDER_FULL') . "/', img) AS img_thumb
// DESPUÉS (Devuelve solo el campo 'img' y Vue construye la URL):

Flight::route('GET /slider/listar', function () {
    DB::query("SET NAMES 'utf8'");
    $rows = DB::query("
        SELECT *
        FROM slider
        ORDER BY slider_id DESC
    ");
    // El campo 'img' ahora contiene el nombre del archivo único (ej: 'slider_xxx.jpg')
    Flight::json($rows);
});

/* ----------------- */
/* CRUD de “slider” */
/* ----------------- */

/* CREAR */
Flight::route('POST /slider/crear', function () {
    // *** 1. RECIBIR DATOS COMO JSON (enviado por Flask) ***
    // Se utiliza Flight::request()->getBody() para obtener el cuerpo crudo de la petición
    $data = json_decode(Flight::request()->getBody(), true);

    $orden          = $data['orden']          ?? 0;
    $is_visible     = $data['is_visible']     ?? 1;
    $fecha_creacion = $data['fecha_creacion'] ?? null;
    $fecha_fin      = $data['fecha_fin']      ?? null;
    $neg_id         = $data['neg_id']         ?? null;
    $name           = $data['filename']       ?? null; // <-- Nombre del archivo ya subido al CDN
    $grupo = $data['grupo'] ?? '';


    // 2. Validación: El nombre de archivo es crucial para grabar el registro
    if (empty($name)) {
        Flight::halt(400, json_encode([
            'success' => false,
            'error'   => 'Falta el filename (nombre del archivo subido a CDN por Flask)'
        ]));
    }
    
    // 3. Inserta en BD
    DB::insert('slider', [
        'img'            => $name, // <-- Usa el nombre de archivo de BunnyCDN
        'orden'          => $orden,
        'is_visible'     => $is_visible,
        'fecha_creacion' => $fecha_creacion,
        'fecha_fin'      => $fecha_fin,
        'grupo' => $grupo,
        'neg_id'         => $neg_id
    ]);

    // 4. Respuesta (simplificada y adaptada para Vue.js/Flask)
    Flight::json([
        'success'   => true,
        'slider_id' => DB::insertId(),
        'filename'  => $name // Devuelve el nombre del archivo para referencia
    ]);
});

/* EDITAR */
Flight::route('POST /slider/editar', function () {
    // *** 1. RECIBIR DATOS COMO JSON (de Flask) ***
    $data = json_decode(Flight::request()->getBody(), true);

    $slider_id      = $data['slider_id']      ?? null;
    $orden          = $data['orden']          ?? 0;
    $is_visible     = $data['is_visible']     ?? 1;
    $fecha_creacion = $data['fecha_creacion'] ?? null;
    $fecha_fin      = $data['fecha_fin']      ?? null;
    $neg_id         = $data['neg_id']         ?? null;
    $new_filename   = $data['filename']       ?? null; // <--- Campo opcional: nombre del archivo ya subido al CDN
    $grupo = $data['grupo'] ?? null;


    if (!$slider_id) {
        // ... Flight::halt ...
        Flight::halt(400, json_encode([
            'success' => false,
            'error'   => 'slider_id es requerido'
        ]));
    }

    $updateData = [
        'orden'          => $orden,
        'is_visible'     => $is_visible,
        'fecha_creacion' => $fecha_creacion,
        'fecha_fin'      => $fecha_fin,
        'grupo'          => $grupo,
        'neg_id'         => $neg_id
    ];

    // Si Flask envió un nuevo nombre de archivo, agregar al UPDATE
    if ($new_filename) {
        $updateData['img'] = $new_filename;
    }

    // 2. Realizar el UPDATE en la BD
    DB::update('slider', $updateData, 'slider_id = %i', $slider_id);
    
    // 3. Respuesta
    Flight::json([
        'success' => true,
        'slider_id' => $slider_id,
        'updated_img' => (bool)$new_filename
    ]);
});

/* ELIMINAR */
Flight::route('POST /slider/eliminar', function () {
    $d = json_decode(Flight::request()->getBody(), true);
    DB::delete('slider', 'slider_id = %i', $d['slider_id']);
    Flight::json(['success' => true]);
});

/* DETALLE */
// ANTES (Local path):
// CONCAT('" . vari('PICS_SLIDER_MINI') . "/', img) AS img_thumb
// DESPUÉS (Devuelve solo el campo 'img' y Vue construye la URL):

Flight::route('GET /slider/detalle/@id', function ($id) {
    DB::query("SET NAMES 'utf8'");
    $row = DB::queryFirstRow(
        "SELECT *
         FROM slider
         WHERE slider_id = %i",
        $id
    );
    // El campo 'img' ahora contiene el nombre del archivo único (ej: 'slider_xxx.jpg')
    Flight::json($row);
});