<?php
// Lista de menús
Flight::route('GET /admin/listaMenu', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();
    include $path_public . "/admin/mod_menu/inicio.php";
});

// Agregar menú (formulario)
Flight::route('GET /admin/agregarMenu', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $path_public;
    include $path_public . "/admin/mod_menu/agregar_menu.php";
});

// Agregar menú (proceso)
Flight::route('POST /admin/agregarMenu', function () {
    include DEFINITION;
    $post = array_map('trim', $_POST);
    $titulo = util::guardar_html($post['txt_titulo'] ?? '');
    $tipo_admin_id = $post['cbo_tipo_administrador_id_agregar'] ?? 0;
    menu::agregar_menu($titulo, $tipo_admin_id);

    // Mostrar lista nuevamente
    login_admin::autentificar_administrador();
    global $path_public;
    include $path_public . "/admin/mod_menu/inicio.php";
});

// Editar menú (formulario)
Flight::route('GET /admin/editarMenu/@menu_id', function ($menu_id) {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $path_public;
    $detalle_menu = menu::detalle_menu($menu_id);
    include $path_public . "/admin/mod_menu/editar_menu.php";
});

// Editar menú (proceso)
Flight::route('POST /admin/editarMenu', function () {
    include DEFINITION;
    $post = array_map('trim', $_POST);
    $menu_id = $post['txt_menu_id'] ?? null;
    $titulo = util::guardar_html($post['txt_titulo'] ?? '');
    $tipo_admin_id = $post['cbo_tipo_administrador_id_editar'] ?? 0;
    if ($menu_id) {
        menu::editar_menu($menu_id, 'titulo', $titulo);
        menu::editar_menu($menu_id, 'tipo_administrador_id', $tipo_admin_id);
    }

    login_admin::autentificar_administrador();
    global $path_public;
    include $path_public . "/admin/mod_menu/inicio.php";
});

// Editar padre del menú
Flight::route('POST /admin/eduMenuPadre', function () {
    include DEFINITION;
    $post = array_map('trim', $_POST);
    $menu_id = $post['txt_edu_padre_menu_id'] ?? null;
    $padre = $post['cbo_padre'] ?? null;
    if ($menu_id && $padre) {
        menu::editar_menu($menu_id, 'padre', $padre);
    }

    login_admin::autentificar_administrador();
    global $path_public;
    include $path_public . "/admin/mod_menu/inicio.php";
});

// Editar orden de menús
Flight::route('POST /admin/editarOrdenMenu', function () {
    include DEFINITION;
    $info = $_POST['info'] ?? [];
    foreach ($info as $item) {
        if (isset($item['menu_id'], $item['orden'])) {
            menu::editar_menu($item['menu_id'], 'orden', $item['orden']);
        }
    }
});

// Eliminar un menú
Flight::route('POST /admin/eliminarMenu', function () {
    include DEFINITION;
    $menu_id = $_POST['menu_id'] ?? null;
    if ($menu_id) {
        menu::eliminar_menu($menu_id);
    }
});

Flight::route('POST /admin/actualizarOrdenMenu', function () {

    $d = json_decode(Flight::request()->getBody(), true);

    if (!isset($d['menu_id'], $d['orden']) || !is_numeric($d['orden'])) {
        Flight::json(['success' => false, 'message' => 'Datos incompletos'], 400);
        return;
    }

    $menu_id = (int) $d['menu_id'];
    $orden   = (int) $d['orden'];

    DB::update('menu', ['orden' => $orden], 'menu_id = %i', $menu_id);

    Flight::json(['success' => true]);
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
