<?php
// Dashboard de administrador
Flight::route('GET /admin/dash', function() {
    include DEFINITION;

    global $nombre_app, $apphost, $sesion_admin_administrador_id;

    $administrador_id = login_admin::autentificar_administrador();
    $valor_key = $nombre_app . vari("KEY");
    $administrador_id = str_replace("*", "", util::decrypt($administrador_id, $valor_key));

    $info_admin = login_admin::informacion_administrador_por_id(
        util::decrypt($sesion_admin_administrador_id, $valor_key)
    );

    Flight::redirect($apphost . $info_admin['url']);
});

// Vista login
Flight::route('GET /loginVault', function() {    
    include DEFINITION;

    $mBase = $varhost . "/public/admin/login/";
    include $path_public . "/admin/login/inicio.php";
});

// Procesa login
Flight::route('POST /loginVault', function() {
    include DEFINITION;

    $json = Flight::request()->getBody();
    $datos_usuario = json_decode($json);

    $usuario = $datos_usuario->usuario ?? '';
    $clavel = $datos_usuario->clavel ?? '';

    $is_valido = login_admin::verificar_datos_usuarios($usuario, $clavel);

    if ($is_valido) {
        global $nombre_app;

        $info_admin = login_admin::informacion_administrador_por_email($usuario);
        $valor_key = $nombre_app . vari("KEY");

        $email = util::preparar_para_encriptar($usuario);
        $enc_email = util::encrypt($usuario, $valor_key);

        $info_admin['administrador_id'] = util::preparar_para_encriptar($info_admin['administrador_id']);
        $enc_info_admin_id = util::encrypt($info_admin['administrador_id'], $valor_key);

        setcookie("sesion_admin_administrador_email_" . $nombre_app, $enc_email, 0, "/");
        setcookie("sesion_admin_administrador_id_" . $nombre_app, $enc_info_admin_id, 0, "/");

        echo util::ok();
    } else {
        echo util::error();
    }
});

// Cerrar sesión
Flight::route('GET /finAdmin', function() {
    include DEFINITION;

    global $nombre_app, $apphost;

    setcookie("sesion_admin_administrador_email_" . $nombre_app, '', time() - 3600, "/");
    setcookie("sesion_admin_administrador_id_" . $nombre_app, '', time() - 3600, "/");

    Flight::redirect($apphost . "/loginVault");
});

// Ruta de test
Flight::route('GET /tt01', function() {
    include DEFINITION;

    $usuario = "renzo";
    $clavel = "renzo";

    $res = login_admin::verificar_datos_usuarios($usuario, $clavel);
    var_dump($res);
});

// OPTIONS para CORS (si es necesario)
Flight::route('OPTIONS /api/login_admin/j_login_app', function() {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
});

// API Login Admin (POST)
Flight::route('POST /api/login_admin/j_login_app', function() {
    include DEFINITION;

    $body = Flight::request()->getBody();
    $data = json_decode($body, true);

    global $file_log, $ruta_log;

    if (!($file_log->load($ruta_log)->write($data))) {
        die("error file_log");
    }

    if (!isset($data['tipo_administrador_id'], $data['email'], $data['clavel'])) {
        echo json_encode([
            "respuesta" => [
                "tipo" => "ERROR",
                "descripcion" => "Datos incompletos"
            ]
        ]);
        return;
    }

    $tipo_administrador_id = (int) $data['tipo_administrador_id'];
    $email = $data['email'];
    $clavel = $data['clavel'];

    $query = "SELECT 
                a.administrador_id, 
                a.nombres_apellidos, 
                a.email, 
                a.clavel, 
                a.fecha_creacion, 
                a.fecha_ultimo_acceso, 
                a.is_activo, 
                a.tipo_administrador_id, 
                t.descripcion 
              FROM administradortbl a 
              INNER JOIN tipo_administrador t ON a.tipo_administrador_id = t.tipo_administrador_id 
              WHERE a.tipo_administrador_id = %i AND a.email LIKE %s AND a.clavel LIKE %s";

    $resultado = DB::queryFirstRow($query, $tipo_administrador_id, $email, $clavel);

    if ($resultado) {
        echo json_encode([
            "respuesta" => [
                "tipo" => "EXITO",
                "descripcion" => "Administrador encontrado",
                "listado" => $resultado
            ]
        ]);
    } else {
        echo json_encode([
            "respuesta" => [
                "tipo" => "ERROR",
                "descripcion" => "No se encontro el administrador"
            ]
        ]);
    }
});
