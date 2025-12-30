<?php
Flight::route('POST /app/login', function () {

    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        Flight::json(['success' => false]);
        return;
    }

    // 🔐 Validar administrador
    $admin = DB::queryFirstRow("
        SELECT administrador_id, nombres_apellidos
        FROM administradortbl
        WHERE email = %s
          AND clavel = %s
          AND is_activo = 1
        LIMIT 1
    ", $email, $password);

    if (!$admin) {
        Flight::json(['success' => false]);
        return;
    }

    // 📅 Fecha de hoy
    $hoy = date('Y-m-d');

    // 💰 Buscar caja del día
    $caja = DB::queryFirstRow("
        SELECT caja_id, estado
        FROM caja
        WHERE administrador_id = %i
          AND DATE(fecha_apertura) = %s
        ORDER BY caja_id DESC
        LIMIT 1
    ", $admin['administrador_id'], $hoy);

    if ($caja) {
        $estado_caja = $caja['estado']; // ABIERTA o CERRADA
        $caja_id     = $caja['caja_id'];
    } else {
        $estado_caja = 'CERRADA';
        $caja_id     = null;
    }

    // 🕒 Actualizar último acceso
    DB::update('administradortbl', [
        'fecha_ultimo_acceso' => date('Y-m-d H:i:s')
    ], 'administrador_id=%i', $admin['administrador_id']);

    // 📤 Respuesta final
    Flight::json([
        'success'             => true,
        'administrador_id'    => $admin['administrador_id'],
        'nombres_apellidos'   => $admin['nombres_apellidos'],
        'estado_caja'         => $estado_caja,
        'caja_id'             => $caja_id
    ]);
});


