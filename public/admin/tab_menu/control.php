<?php
/* -------------------------------
 * Vista /tab3
 * ------------------------------- */
Flight::route('GET /menu', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $path_public;                          // asegúrate de tener esta var en tu bootstrap
    include $path_public . '/admin/tab_menu/inicio.php';
});

Flight::route('GET /menu/listar', function () {

    $rows = DB::query("
        SELECT 
            m.menu_id,
            m.titulo,
            m.orden,
            t.descripcion AS tipo_admin,
            m.tipo_administrador_id,
            (
              SELECT COUNT(*) 
              FROM submenu s 
              WHERE s.menu_id = m.menu_id
            ) AS total_submenus
        FROM menu m
        LEFT JOIN tipo_administrador t 
            ON t.tipo_administrador_id = m.tipo_administrador_id
        ORDER BY m.orden ASC
    ");

    Flight::json($rows);
});


Flight::route('POST /menu/cambiar-orden', function () {

    $id    = intval(Flight::request()->data->menu_id);
    $delta = intval(Flight::request()->data->delta); // +1 o -1

    DB::query("
        UPDATE menu 
        SET orden = orden + %i 
        WHERE menu_id = %i
    ", $delta, $id);

    Flight::json(['status'=>'ok']);
});


Flight::route('POST /menu/guardar', function () {

    $d = Flight::request()->data->getData();

    if (empty($d['menu_id'])) {
        DB::insert('menu', [
            'titulo' => $d['titulo'],
            'orden'  => intval($d['orden']),
            'tipo_administrador_id' => intval($d['tipo_administrador_id'])
        ]);
    } else {
        DB::update('menu', [
            'titulo' => $d['titulo'],
            'tipo_administrador_id' => intval($d['tipo_administrador_id'])
        ], "menu_id=%i", intval($d['menu_id']));
    }

    Flight::json(['status'=>'ok']);
});


Flight::route('POST /menu/eliminar', function () {

    $id = intval(Flight::request()->data->menu_id);

    DB::delete('submenu', "menu_id=%i", $id);
    DB::delete('menu', "menu_id=%i", $id);

    Flight::json(['status'=>'ok']);
});


Flight::route('GET /submenu/listar/@menu_id', function ($menu_id) {

    $rows = DB::query("
        SELECT * 
        FROM submenu 
        WHERE menu_id=%i 
        ORDER BY orden
    ", $menu_id);

    Flight::json($rows);
});


Flight::group('/api', function() {

  // Menús por tipo de administrador
  Flight::route('GET /menus/por-tipo/@tipo:[0-9]+', function($tipo) {
    header('Content-Type: application/json; charset=utf-8');
    try {
      DB::query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");

      $rows = DB::query(
        "SELECT
           m.menu_id,
           m.titulo   AS menu_titulo,
           m.orden    AS menu_orden,
           s.submenu_id,
           s.titulo   AS submenu_titulo,
           s.url,
           s.target,
           s.orden    AS submenu_orden
         FROM menu m
         LEFT JOIN submenu s ON s.menu_id = m.menu_id
         WHERE m.tipo_administrador_id = %i
         ORDER BY m.orden, s.orden, s.submenu_id",
        $tipo
      );

      // Agrupar por menú
      $byMenu = [];
      foreach ($rows as $r) {
        $mid = (int)$r['menu_id'];
        if (!isset($byMenu[$mid])) {
          $byMenu[$mid] = [
            'menu_id' => $mid,
            'titulo'  => $r['menu_titulo'],
            'orden'   => (int)$r['menu_orden'],
            'lista_submenu' => []
          ];
        }
        if (!is_null($r['submenu_id'])) {
          $byMenu[$mid]['lista_submenu'][] = [
            'submenu_id' => (int)$r['submenu_id'],
            'titulo'     => $r['submenu_titulo'],
            'url'        => $r['url'],
            'target'     => $r['target'],
            'orden'      => (int)$r['submenu_orden']
          ];
        }
      }

      $menus = array_values($byMenu);
      echo json_encode(['ok' => true, 'menus' => $menus], JSON_UNESCAPED_UNICODE);
    } catch (Throwable $e) {
      http_response_code(500);
      echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    }
  });

  // (Opcional) Todos los menús de un tipo con búsqueda simple ?q=texto
  Flight::route('GET /menus/buscar/@tipo:[0-9]+', function($tipo) {
    header('Content-Type: application/json; charset=utf-8');
    $q = trim(Flight::request()->query['q'] ?? '');
    $like = '%' . $q . '%';

    try {
      DB::query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");

      $rows = DB::query(
        "SELECT
           m.menu_id, m.titulo AS menu_titulo, m.orden AS menu_orden,
           s.submenu_id, s.titulo AS submenu_titulo, s.url, s.target, s.orden AS submenu_orden
         FROM menu m
         LEFT JOIN submenu s ON s.menu_id = m.menu_id
         WHERE m.tipo_administrador_id = %i
           AND (%ls = '' OR m.titulo LIKE %ss OR s.titulo LIKE %ss OR s.url LIKE %ss)
         ORDER BY m.orden, s.orden, s.submenu_id",
        $tipo, $q, $like, $like, $like
      );

      // (Idéntico agrupamiento)
      $byMenu = [];
      foreach ($rows as $r) {
        $mid = (int)$r['menu_id'];
        if (!isset($byMenu[$mid])) {
          $byMenu[$mid] = [
            'menu_id' => $mid,
            'titulo'  => $r['menu_titulo'],
            'orden'   => (int)$r['menu_orden'],
            'lista_submenu' => []
          ];
        }
        if (!is_null($r['submenu_id'])) {
          $byMenu[$mid]['lista_submenu'][] = [
            'submenu_id' => (int)$r['submenu_id'],
            'titulo'     => $r['submenu_titulo'],
            'url'        => $r['url'],
            'target'     => $r['target'],
            'orden'      => (int)$r['submenu_orden']
          ];
        }
      }
      echo json_encode(['ok' => true, 'menus' => array_values($byMenu)], JSON_UNESCAPED_UNICODE);
    } catch (Throwable $e) {
      http_response_code(500);
      echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    }
  });

});

function lista_menu_con_submenus_por_tipo_administrador_id($tipo_administrador_id) {
        $query = <<<EOF
        SELECT 
            menu.menu_id, 
            menu.titulo, 
            menu.orden, 
            menu.tipo_administrador_id, 
            submenu.submenu_id, 
            submenu.titulo AS submenu_titulo, 
            submenu.url, 
            submenu.orden AS submenu_orden, 
            submenu.target
        FROM menu 
        INNER JOIN submenu ON menu.menu_id = submenu.menu_id
        WHERE 
        menu.tipo_administrador_id = $tipo_administrador_id
        ORDER BY menu.orden, submenu.orden
EOF;

        // Ejecutar la consulta
        $res = DB::query($query);

        // Estructurar los datos en un array asociativo
        $menus = [];
        foreach ($res as $row) {
            $menu_id = $row['menu_id'];

            // Si el menú aún no está en el array, agregarlo
            if (!isset($menus[$menu_id])) {
                $menus[$menu_id] = [
                    'menu_id' => $row['menu_id'],
                    'titulo' => $row['titulo'],
                    'orden' => $row['orden'],
                    'tipo_administrador_id' => $row['tipo_administrador_id'],
                    'lista_submenu' => []
                ];
            }

            // Agregar el submenú a la lista de submenús del menú correspondiente
            $menus[$menu_id]['lista_submenu'][] = [
                'submenu_id' => $row['submenu_id'],
                'titulo' => $row['submenu_titulo'],
                'url' => $row['url'],
                'orden' => $row['submenu_orden'],
                'target' => $row['target']
            ];
        }

        // Devolver los menús estructurados como array indexado
        return array_values($menus);
}

Flight::route('POST /menu/actualizar-orden', function () {

    $data = Flight::request()->data->getData();

    if (empty($data['orden']) || !is_array($data['orden'])) {
        Flight::json(['status'=>'error','msg'=>'Orden inválido'], 400);
        return;
    }

    DB::startTransaction();
    try {

        foreach ($data['orden'] as $row) {
            DB::update(
                'menu',
                ['orden' => intval($row['orden'])],
                "menu_id=%i",
                intval($row['menu_id'])
            );
        }

        DB::commit();
        Flight::json(['status'=>'ok']);

    } catch (Exception $e) {
        DB::rollback();
        Flight::json(['status'=>'error','msg'=>$e->getMessage()], 500);
    }
});

Flight::route('POST /submenu/actualizar-orden', function () {

    $data = Flight::request()->data->getData();

    if (empty($data['orden']) || !is_array($data['orden'])) {
        Flight::json(['status'=>'error','msg'=>'Orden inválido'], 400);
        return;
    }

    DB::startTransaction();
    try {

        foreach ($data['orden'] as $row) {
            DB::update(
                'submenu',
                ['orden' => intval($row['orden'])],
                "submenu_id=%i",
                intval($row['submenu_id'])
            );
        }

        DB::commit();
        Flight::json(['status'=>'ok']);

    } catch (Exception $e) {
        DB::rollback();
        Flight::json(['status'=>'error','msg'=>$e->getMessage()], 500);
    }
});


Flight::route('POST /submenu/guardar', function () {

    $data = Flight::request()->data->getData();

    if (empty($data['menu_id']) || empty($data['titulo'])) {
        Flight::json(['status'=>'error','msg'=>'Datos incompletos'], 400);
        return;
    }

    if (!empty($data['submenu_id'])) {
        // EDITAR
        DB::update('submenu', [
            'titulo' => $data['titulo'],
            'url'    => $data['url'],
            'orden'  => intval($data['orden']),
            'target' => $data['target']
        ], "submenu_id=%i", intval($data['submenu_id']));
    } else {
        // CREAR
        DB::insert('submenu', [
            'menu_id' => intval($data['menu_id']),
            'titulo'  => $data['titulo'],
            'url'     => $data['url'],
            'orden'   => intval($data['orden']),
            'target'  => $data['target']
        ]);
    }

    Flight::json(['status'=>'ok']);
});


Flight::route('POST /submenu/eliminar', function () {

    $data = Flight::request()->data->getData();

    DB::delete(
        'submenu',
        "submenu_id=%i",
        intval($data['submenu_id'])
    );

    Flight::json(['status'=>'ok']);
});
