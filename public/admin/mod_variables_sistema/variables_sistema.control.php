<?php
// GET: Listar variables del sistema
Flight::route('GET /admin/listavariables_sistema', function() {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $path_public;
    include $path_public . "/admin/mod_variables_sistema/inicio.php";
});

// GET: Mostrar formulario para agregar variable
Flight::route('GET /admin/agregarVariablesSistema', function() {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $path_public;
    include $path_public . "/admin/mod_variables_sistema/agregar_variables_sistema.php";
});

// POST: Procesar agregado de variable
Flight::route('POST /admin/agregarVariablesSistema', function() {
    include DEFINITION;
    login_admin::autentificar_administrador();

    $post_enviado = array_map('trim', $_POST);
    extract($post_enviado);

    $txt_nombre_variable = trim($txt_nombre_variable);

    if (variables_sistema::cantidad_variables_sistema(" WHERE nombre_variable = '$txt_nombre_variable'") == 0) {
        variables_sistema::agregar_variables_sistema($txt_nombre_variable, $txt_valor);
    }

    // Reutilizar la vista de lista
    Flight::redirect('/admin/listavariables_sistema');
});

// GET: Editar variable por nombre
Flight::route('GET /admin/editarVariablesSistema/@nombre_variable', function($nombre_variable) {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $path_public;
    $detalle_variables_sistema = variables_sistema::detalle_variables_sistema($nombre_variable);
    include $path_public . "/admin/mod_variables_sistema/editar_variables_sistema.php";
});

// POST: Procesar edición de variable
Flight::route('POST /admin/editarVariablesSistema', function() {
    include DEFINITION;

    $post_enviado = array_map('trim', $_POST);
    extract($post_enviado);

    variables_sistema::editar_variables_sistema($txt_nombre_variable_id, 'nombre_variable', $txt_nombre_variable);
    variables_sistema::editar_variables_sistema($txt_nombre_variable_id, 'valor', util::guardar_palabra_latina($txt_valor));

    Flight::redirect('/admin/listavariables_sistema');
});

// POST: Eliminar variable
Flight::route('POST /admin/eliminarVariablesSistema', function() {
    include DEFINITION;

    $post_enviado = array_map('trim', $_POST);
    extract($post_enviado);

    variables_sistema::eliminar_variables_sistema($nombre_variable);
});