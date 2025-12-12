<?php
// Listar submenús
Flight::route('GET /admin/listaSubmenu', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $path_public;
    include $path_public . "/admin/mod_submenu/inicio.php";
});

// Agregar submenú (formulario)
Flight::route('GET /admin/agregarSubmenu', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $path_public;
    include $path_public . "/admin/mod_submenu/agregar_submenu.php";
});

// Agregar submenú (proceso)
Flight::route('POST /admin/agregarSubmenu', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $apphost;

    $p = array_map('trim', $_POST);

    submenu::agregar_submenu(
        util::guardar_palabra_latina($p['txt_titulo'] ?? ''),
        util::guardar_palabra_latina($p['txt_url'] ?? ''),
        $p['cbo_menu_id_agregar'] ?? 0,
        $p['txt_target'] ?? '_self'
    );

    Flight::redirect($apphost . '/admin/listaSubmenu');
});

// Buscar submenú
Flight::route('POST /admin/buscarSubmenu', function () {
    include DEFINITION;
    $p = array_map('trim', $_POST);
    global $path_public;
    include $path_public . "/admin/mod_submenu/inicio_buscar.php";
});

// Editar submenú (formulario)
Flight::route('GET /admin/editarSubmenu/@submenu_id', function ($submenu_id) {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $path_public;
    $detalle_submenu = submenu::detalle_submenu($submenu_id);
    include $path_public . "/admin/mod_submenu/submenu_editar.php";
});

// Filtrar por menu_id
Flight::route('GET /admin/filtroPorMenu_id/@menu_id', function ($menu_id) {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $path_public;
    $cbo_filtro = "MENU_ID";
    $txt_criterio = $menu_id;
    include $path_public . "/admin/mod_submenu/inicio_buscar.php";
});

// Editar submenú (proceso)
Flight::route('POST /admin/editarSubmenu', function () {
    include DEFINITION;
    global $apphost;
    $p = array_map('trim', $_POST);

    submenu::editar_submenu($p['txt_submenu_id'], 'titulo', util::guardar_palabra_latina($p['txt_titulo']));
    submenu::editar_submenu($p['txt_submenu_id'], 'url', util::guardar_palabra_latina($p['txt_url']));
    submenu::editar_submenu($p['txt_submenu_id'], 'menu_id', $p['cbo_menu_id_editar']);
    submenu::editar_submenu($p['txt_submenu_id'], 'target', $p['cbo_target_editar']);

    Flight::redirect($apphost . '/admin/listaSubmenu');
});

// Editar orden de submenús
Flight::route('POST /admin/editarOrdenSubMenu', function () {
    include DEFINITION;
    $info = $_POST['info'] ?? [];

    foreach ($info as $item) {
        submenu::editar_submenu($item['submenu_id'], 'orden', $item['orden']);
    }
});

// Eliminar un submenú
Flight::route('POST /admin/eliminarSubmenu', function () {
    include DEFINITION;
    $p = array_map('trim', $_POST);
    submenu::eliminar_submenu($p['submenu_id']);
});

// Eliminar varios submenús
Flight::route('POST /admin/eliminarVariosSubmenu', function () {
    include DEFINITION;
    $info = $_POST['info'] ?? [];
    foreach ($info as $item) {
        submenu::eliminar_submenu($item);
    }
    echo util::ok();
});

// Editar título rápido (inline)
Flight::route('POST /admin/eduSubmenuTitulo', function () {
    include DEFINITION;
    $p = array_map('trim', $_POST);

    submenu::editar_submenu(
        $p['txt_edu_submenu_id'],
        "titulo",
        util::guardar_palabra_latina($p['txt_edu_titulo'])
    );
    echo util::ok();
});

// Editar menu_id rápido (inline)
Flight::route('POST /admin/eduSubmenuMenuId', function () {
    include DEFINITION;
    $p = array_map('trim', $_POST);

    submenu::editar_submenu($p['cbo_edu_submenu_id'], 'menu_id', $p['cbo_edu_menu_id']);

    $detalle_menu = menu::detalle_menu($p['cbo_edu_menu_id']);
    echo json_encode($detalle_menu['titulo']);
});

// Buscar submenú por filtro y criterio desde /mud
Flight::route('GET /mud/buscarSubmenuCriterio', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();

    $request = Flight::request();
    $cbo_filtro = strtolower($request->query['cbo_filtro'] ?? '');
    $txt_criterio = $request->query['txt_criterio'] ?? '';
    $titulo_app = "";
    
    include $path_public . "/admin/mod_submenu/inicio_buscar/inicio_buscar.php";
});

/* routes_submenu.php (o donde agrupes rutas) */
Flight::route('POST /admin/actualizarOrdenSubmenu', function () {

    $d = json_decode(Flight::request()->getBody(), true);

    /* Validación mínima */
    if (!isset($d['submenu_id'], $d['orden']) || !is_numeric($d['orden'])) {
        Flight::json(['success' => false, 'message' => 'Datos incompletos'], 400);
        return;
    }

    $submenu_id = (int) $d['submenu_id'];
    $orden      = (int) $d['orden'];

    /* Update usando MeekroDB (DB::…) */
    DB::update('submenu',
        ['orden' => $orden],
        'submenu_id = %i', $submenu_id
    );

    Flight::json(['success' => true]);
});
