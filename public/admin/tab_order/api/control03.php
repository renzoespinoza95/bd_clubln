<?php
/* 1. Clientes */
Flight::route('GET /cliente/listar', function () {
    Flight::json(
        DB::query("SELECT cliente_id, dni, nombre FROM cliente WHERE is_activo=1")
    );
});

/* 2. Crear Cliente */
Flight::route('POST /cliente/crear', function () {
    $d = Flight::request()->data->getData();
    DB::insert('cliente', [
        'dni'    => $d['dni'],
        'nombre' => $d['nombre']
    ]);
    Flight::json(['ok' => 1]);
});

/* 3. Auth Administrador Actual */
Flight::route('GET /auth/administrador-actual', function () {
    include DEFINITION;

    if (!$sesion_admin_administrador_id) {
        Flight::json(['status' => 'error', 'msg' => 'No autenticado'], 401);
        return;
    }

    $valor_key = $nombre_app . vari("KEY");
    $administrador_id = str_replace(
        "*",
        "",
        util::decrypt($sesion_admin_administrador_id, $valor_key)
    );

    $admin = login_admin::informacion_administrador_por_id($administrador_id);
    if (!$admin) {
        Flight::json(['status' => 'error', 'msg' => 'Administrador no encontrado'], 404);
        return;
    }

    $hoy = date('Y-m-d');
    $caja = DB::queryFirstRow("
        SELECT *
        FROM caja
        WHERE administrador_id = %i
          AND DATE(fecha_apertura) = %s
        ORDER BY caja_id DESC
        LIMIT 1
    ", $administrador_id, $hoy);

    if (!$caja) {
        $caja = ['estado' => 'CERRADA'];
    }

    Flight::json([
        'status'        => 'ok',
        'administrador' => [
            'administrador_id' => $admin['administrador_id'],
            'nombre'           => $admin['nombres_apellidos'] ?? '',
            'email'            => $admin['email'] ?? ''
        ],
        'caja'          => $caja
    ]);
});

/* 4. Tipos de Pago */
Flight::route('GET /tipo_pago/listar', function () {
    $rows = DB::query("
        SELECT tipo_pago_id, descripcion
        FROM tipo_pago
        ORDER BY orden ASC
    ");
    Flight::json($rows);
});

/* 5. Mesas */
Flight::route('GET /mesa/listar', function () {
    DB::query("SET NAMES 'utf8mb4'");
    $rows = DB::query("
        SELECT mesa_id, nombre, estado
        FROM mesa
        ORDER BY mesa_id ASC
    ");
    Flight::json($rows);
});