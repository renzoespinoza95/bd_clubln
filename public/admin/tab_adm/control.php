<?php
/* -------------------------------
 * Vista /tab3
 * ------------------------------- */
Flight::route('GET /administradores', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $path_public;                          // asegúrate de tener esta var en tu bootstrap
    include $path_public . '/admin/tab_adm/inicio.php';
});

Flight::route('POST /admin/crear', function () {

    $d = Flight::request()->data->getData();

    DB::insert('administradortbl', [
        'nombres_apellidos'   => $d['nombres_apellidos'],
        'email'               => $d['email'],
        'clavel'              => $d['clavel'],
        'fecha_creacion'      => date('Y-m-d H:i:s'),
        'is_activo'           => 1,
        'tipo_administrador_id'=> intval($d['tipo_administrador_id'])
    ]);

    Flight::json(['status'=>'ok']);
});


Flight::route('POST /admin/editar', function () {

    $d = Flight::request()->data->getData();

    $data = [
        'nombres_apellidos' => $d['nombres_apellidos'],
        'email'             => $d['email'],
        'is_activo'         => intval($d['is_activo']),
        'tipo_administrador_id'=> intval($d['tipo_administrador_id'])
    ];

    if (!empty($d['clavel'])) {
        $data['clavel'] = $d['clavel'];
    }

    DB::update('administradortbl', $data, "administrador_id=%i", intval($d['administrador_id']));

    Flight::json(['status'=>'ok']);
});


Flight::route('POST /admin/eliminar', function () {

    $id = intval(Flight::request()->data->administrador_id);

    DB::update('administradortbl', [
        'is_activo' => 0
    ], "administrador_id=%i", $id);

    Flight::json(['status'=>'ok']);
});

Flight::route('GET /tipo-administrador/listar', function () {

    $rows = DB::query("
        SELECT 
            tipo_administrador_id,
            descripcion
        FROM tipo_administrador
        WHERE is_activo = 1
        ORDER BY descripcion
    ");

    Flight::json($rows);
});

Flight::route('GET /admin/listar', function () {

    $rows = DB::query("
        SELECT 
            a.administrador_id,
            a.nombres_apellidos,
            a.email,
            a.is_activo,
            a.tipo_administrador_id,
            t.descripcion AS tipo
        FROM administradortbl a
        LEFT JOIN tipo_administrador t 
            ON t.tipo_administrador_id = a.tipo_administrador_id
        ORDER BY a.administrador_id DESC
    ");

    Flight::json($rows);
});
