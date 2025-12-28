<?php
Flight::route('POST /app/login', function () {

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        Flight::json(['success' => false]);
        return;
    }

    $admin = DB::queryFirstRow("
        SELECT administrador_id, nombres_apellidos
        FROM administradortbl
        WHERE email = %s
          AND clavel = %s
          AND is_activo = 1
        LIMIT 1
    ", $email, $password);

    if ($admin) {
        DB::update('administradortbl', [
            'fecha_ultimo_acceso' => date('Y-m-d H:i:s')
        ], 'administrador_id=%i', $admin['administrador_id']);

        Flight::json([
            'success' => true,
            'administrador_id' => $admin['administrador_id'],
            'nombres_apellidos' => $admin['nombres_apellidos']
        ]);
    } else {
        Flight::json(['success' => false]);
    }
});

