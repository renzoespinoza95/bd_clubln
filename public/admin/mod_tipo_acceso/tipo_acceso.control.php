<?php
// Lista tipos de acceso
Flight::route('GET /admin/listaTipoAcceso', function() {
    include DEFINITION;
    login_admin::autentificar_administrador();
    include $path_public . '/admin/mod_tipo_acceso/inicio.php';
});

// Formulario para agregar tipo de acceso
Flight::route('GET /admin/agregarTipoAcceso', function() {
    include DEFINITION;
    login_admin::autentificar_administrador();
    include $path_public . '/admin/mod_tipo_acceso/agregar_tipo_acceso.php';
});

// Procesa alta de tipo de acceso
Flight::route('POST /admin/agregarTipoAcceso', function() {
    include DEFINITION;
    login_admin::autentificar_administrador();

    $post_enviado = array_map('trim', $_POST);
    extract($post_enviado);

    tipo_acceso::agregar_tipo_acceso(
        util::guardar_palabra_latina($txt_nombre)
    );

    Flight::redirect($apphost . '/admin/listaTipoAcceso');
});

// Búsqueda de tipo de acceso
Flight::route('POST /admin/buscarTipoAcceso', function() {
    include DEFINITION;

    $post_enviado = array_map('trim', $_POST);
    extract($post_enviado);

    include $path_public . '/admin/mod_tipo_acceso/inicio_buscar.php';
});

// Formulario para editar un tipo de acceso
Flight::route('GET /admin/editarTipoAcceso/@ta_id', function($ta_id) {
    include DEFINITION;
    login_admin::autentificar_administrador();

    $detalle_tipo_acceso = tipo_acceso::detalle_tipo_acceso($ta_id);
    include $path_public . '/admin/mod_tipo_acceso/tipo_acceso_editar.php';
});

// Procesa edición de tipo de acceso
Flight::route('POST /admin/editarTipoAcceso', function() {
    include DEFINITION;

    $post_enviado = array_map('trim', $_POST);
    extract($post_enviado);

    tipo_acceso::editar_tipo_acceso(
        $txt_ta_id,
        'nombre',
        util::guardar_palabra_latina($txt_nombre)
    );

    Flight::redirect($apphost . '/admin/listaTipoAcceso');
});

// Elimina un tipo de acceso
Flight::route('POST /admin/eliminarTipoAcceso', function() {
    include DEFINITION;

    $post_enviado = array_map('trim', $_POST);
    extract($post_enviado);

    tipo_acceso::eliminar_tipo_acceso($ta_id);
});

// Elimina varios tipos de acceso
Flight::route('POST /admin/eliminarVariosTipoAcceso', function() {
    include DEFINITION;

    // $_POST['info'] debe ser un array de IDs
    $info = $_POST['info'] ?? [];
    __::each($info, function($item) {
        tipo_acceso::eliminar_tipo_acceso($item);
    });
});

// Edita sólo el nombre de un tipo de acceso (desde un form específico)
Flight::route('POST /admin/eduTipoAccesoNombre', function() {
    include DEFINITION;

    $post_enviado = array_map('trim', $_POST);
    extract($post_enviado);

    tipo_acceso::editar_tipo_acceso(
        $txt_edu_ta_id,
        'nombre',
        util::guardar_palabra_latina($txt_edu_nombre)
    );
});
