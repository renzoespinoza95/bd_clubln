<?php

// GET: Lista de administradores
Flight::route('GET /listaAdministradores', function () {
    include DEFINITION;
    include $path_public . "/admin/administradortbl/inicio.php";
});

// GET: Formulario para agregar administrador
Flight::route('GET /admin/agregarAdministrador', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();
    include $path_public . "/admin/administradortbl/agregar_administrador.php";
});

// POST: Agregar nuevo administrador
Flight::route('POST /admin/agregarAdministrador', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();

    $post_enviado = array_map('trim', $_POST);
    extract($post_enviado);

    administradortbl::agregar_administrador(
        Util::guardar_palabra_latina($txt_nombres_apellidos),
        $txt_email,
        $txt_password,
        Util::fecha_hora_actual(),
        Util::fecha_hora_actual(),
        '1',
        $cbo_tipo_administrador_id_agregar
    );

    global $apphost;
    Flight::redirect($apphost . "/listaAdministradores");
});

// GET: Formulario para editar administrador
Flight::route('GET /editarAdministrador/@administrador_id', function ($administrador_id) {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $path_public;

    $detalle_administrador = administradortbl::detalle_administrador($administrador_id);
    include $path_public . "/admin/administradortbl/editar_administrador.php";
});

// POST: Procesa edición de administrador
Flight::route('POST /editarAdministrador', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();

    $post_enviado = array_map('trim', $_POST);
    extract($post_enviado);

    administradortbl::editar_administrador($AdminTxtAdministradorId, "nombres_apellidos", Util::guardar_palabra_latina($AdminTxtNombreApellidos));
    administradortbl::editar_administrador($AdminTxtAdministradorId, "email", $AdminTxtEmail);
    administradortbl::editar_administrador($AdminTxtAdministradorId, "clavel", $AdminTxtPassword);
    administradortbl::editar_administrador($AdminTxtAdministradorId, "tipo_administrador_id", $cbo_tipo_administrador_id_editar);

    global $apphost;
    Flight::redirect($apphost . "/listaAdministradores");
});

// POST: Eliminar administrador
Flight::route('POST /admin/eliminarAdministradortbl', function () {
    include DEFINITION;

    $post_enviado = array_map('trim', $_POST);
    extract($post_enviado);

    administradortbl::eliminar_administradortbl($administrador_id);
});

// GET: Activar o desactivar administrador
Flight::route('GET /admin/isActivoAdministradortbl/@administrador_id/@valor', function ($administrador_id, $valor) {
    include DEFINITION;

    administradortbl::is_activo_administradortbl($administrador_id, $valor);

    echo "exito";
});
