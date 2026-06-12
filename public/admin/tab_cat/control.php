<?php
// este es mi backend usando php8.2, flightphp y meekrodb2
Flight::route('GET /cat', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $path_public;                          // asegúrate de tener esta var en tu bootstrap
    include $path_public . '/admin/tab_cat/inicio.php';
});

/* ============================================================
   LISTAR CATEGORÍAS
   ============================================================ */
Flight::route('GET /category/listar', function () {
    DB::query("SET NAMES 'utf8mb4'");
    $rows = DB::query("SELECT * FROM category WHERE borrado_el IS NULL ORDER BY id DESC");

    Flight::json($rows);
});

/* ============================================================
   CREAR CATEGORÍA
   ============================================================ */
Flight::route('POST /category/crear', function () {
    $d = Flight::request()->data->getData();

    $now = time()*1000;

    DB::insert('category', [
        'name'        => $d['name'],
        'icon'        => "icon_cat.jpg",
        'draft'       => 0,
        'brief'       => $d['brief'],
        'color'       => $d['color'],
        'priority'    => 0,
        'created_at'  => $now,
        'last_update' => $now
    ]);

    Flight::json(['status'=>'ok']);
});

/* ============================================================
   EDITAR CATEGORÍA
   ============================================================ */
Flight::route('POST /category/editar', function () {
    $d = Flight::request()->data->getData();

    $now = time()*1000;

    DB::update('category', [
        'name'        => $d['name'],
        'brief'       => $d['brief'],
        'color'       => $d['color'],
        'last_update' => $now
    ], "id=%i", $d['id']);

    Flight::json(['status'=>'ok']);
});

/* ============================================================
   ELIMINAR CATEGORÍA
   ============================================================ */
Flight::route('POST /category/eliminar', function () {

    $d = Flight::request()->data->getData();

    $category_id = (int)$d['id'];

    DB::startTransaction();

    try {

        $fecha_borrado = date('Y-m-d H:i:s');

        // =====================================
        // OBTENER PRODUCTOS DE LA CATEGORÍA
        // =====================================
        $productos = DB::query("
            SELECT DISTINCT product_id
            FROM product_category
            WHERE category_id = %i
              AND borrado_el IS NULL
        ", $category_id);

        // =====================================
        // BORRADO LÓGICO DE PRODUCTOS
        // =====================================
        foreach ($productos as $p) {

            DB::update(
                'product',
                [
                    'borrado_el' => $fecha_borrado
                ],
                "product_id=%i AND borrado_el IS NULL",
                $p['product_id']
            );
        }

        // =====================================
        // BORRADO LÓGICO PRODUCT_CATEGORY
        // =====================================
        DB::update(
            'product_category',
            [
                'borrado_el' => $fecha_borrado
            ],
            "category_id=%i AND borrado_el IS NULL",
            $category_id
        );

        // =====================================
        // BORRADO LÓGICO CATEGORY
        // =====================================
        DB::update(
            'category',
            [
                'borrado_el' => $fecha_borrado
            ],
            "id=%i AND borrado_el IS NULL",
            $category_id
        );

        DB::commit();

        Flight::json([
            'status' => 'ok'
        ]);

    } catch (Exception $e) {

        DB::rollback();

        Flight::json([
            'status' => 'error',
            'msg'    => $e->getMessage()
        ], 500);
    }
});