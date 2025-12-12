<?php
Flight::route('GET /admin/listaTipoAdministrador', function() {
    include DEFINITION;
    login_admin::autentificar_administrador();
    include $path_public . '/admin/mod_tipo_administrador/inicio.php';
});

Flight::route('GET /admin/agregarTipoAdministrador', function() {
    include DEFINITION;
    login_admin::autentificar_administrador();
    include $path_public . '/admin/mod_tipo_administrador/agregar_tipo_administrador.php';
});

Flight::route('POST /admin/agregarTipoAdministrador', function() {
    include DEFINITION;
    login_admin::autentificar_administrador();
    
    $post_enviado = array_map('trim', $_POST);
    extract($post_enviado);
    
    tipo_administrador::agregar_tipo_administrador($txt_descripcion, '0');
    // Tras insertar, redirigimos a la lista
    redirect('/admin/listaTipoAdministrador');
});

Flight::route('GET /admin/editarTipoAdministrador/@tipo_administrador_id', function($tipo_administrador_id) {
    include DEFINITION;
    login_admin::autentificar_administrador();
    
    $detalle_tipo_administrador = tipo_administrador::detalle_tipo_administrador($tipo_administrador_id);
    include $path_public . '/admin/mod_tipo_administrador/editar_tipo_administrador.php';
});

Flight::route('POST /admin/editarTipoAdministrador', function() {
    include DEFINITION;
    
    $post_enviado = array_map('trim', $_POST);
    extract($post_enviado);
    
    tipo_administrador::editar_tipo_administrador($txt_tipo_administrador_id, 'descripcion', $txt_descripcion);
    tipo_administrador::editar_tipo_administrador($txt_tipo_administrador_id, 'submenu_inicio', $cbo_submenu_inicio_agregar);
    
    Flight::redirect('/admin/listaTipoAdministrador');
});

Flight::route('POST /admin/eliminarTipoAdministrador', function() {
    include DEFINITION;
    
    $post_enviado = array_map('trim', $_POST);
    extract($post_enviado);
    
    tipo_administrador::eliminar_tipo_administrador($tipo_administrador_id);
    // opcional: devolver JSON o redirigir
    echo json_encode(['status' => 'ok']);
});

Flight::route('GET /admin/isActivoTipoAdministrador/@tipo_administrador_id/@valor', function($tipo_administrador_id, $valor) {
    include DEFINITION;
    
    tipo_administrador::is_activo_tipo_administrador($tipo_administrador_id, $valor);
    echo 'exito';
});
