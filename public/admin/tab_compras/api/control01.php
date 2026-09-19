<?php
// Función de dominio compartida para movimientos de stock
if (!function_exists('registrar_movimiento_inventario')) {
    function registrar_movimiento_inventario(
        $producto_id,
        $tipo,
        $origen,
        $cantidad,
        $precio_unitario,
        $referencia_id,
        $referencia_tabla
    ) {
        $inv = DB::queryFirstRow(
            "SELECT inventario_id, stock_actual FROM inventario WHERE product_id=%i",
            $producto_id
        );

        if (!$inv) {
            DB::insert('inventario', [
                'product_id'   => $producto_id,
                'stock_actual' => 0,
                'stock_min'    => 0,
                'stock_max'    => 0
            ]);
            $stock_actual = 0;
        } else {
            $stock_actual = (int)$inv['stock_actual'];
        }

        if ($tipo === 'ENTRADA') {
            $nuevo_stock = $stock_actual + $cantidad;
        } elseif ($tipo === 'SALIDA') {
            $nuevo_stock = $stock_actual - $cantidad;
        } else {
            $nuevo_stock = $stock_actual;
        }

        DB::insert('inventario_movimiento', [
            'product_id'       => $producto_id,
            'tipo'             => $tipo,
            'origen'           => $origen,
            'cantidad'         => $cantidad,
            'precio_unitario'  => $precio_unitario,
            'referencia_id'    => $referencia_id,
            'referencia_tabla' => $referencia_tabla,
            'stock_resultante' => $nuevo_stock,
            'fecha'            => date('Y-m-d H:i:s')
        ]);

        DB::update(
            'inventario',
            ['stock_actual' => $nuevo_stock],
            "product_id=%i",
            $producto_id
        );
    }
}

/* 1. Vista Principal /compras */
Flight::route('GET /compras', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();

    global $path_public, $apphost, $varhost;

    // Buffer de assets y headers
    ob_start();
    include $path_public . "/admin/header.php";
    $header_html = ob_get_clean();

    ob_start();
    include $path_public . "/admin/menu.php";
    $menu_html = ob_get_clean();

    ob_start();
    include $path_public . "/admin/footer.php";
    $footer_html = ob_get_clean();

    $dir_comp = __DIR__ . '/../comp_compras/';
    $partials = [
        'head'                     => file_get_contents($dir_comp . 'head.html'),
        'tabla_compras'            => file_get_contents($dir_comp . 'tabla_compras.html'),
        'modal_detalle'            => file_get_contents($dir_comp . 'modal_detalle.html'),
        'modal_crear'              => file_get_contents($dir_comp . 'modal_crear.html'),
        'modal_editar'             => file_get_contents($dir_comp . 'modal_editar.html'),
        'modal_reporte_fechas'     => file_get_contents($dir_comp . 'modal_reporte_fechas.html'),
        'modal_add_items'          => file_get_contents($dir_comp . 'modal_add_items.html'),
        'modal_agregar_item_temp'  => file_get_contents($dir_comp . 'modal_agregar_item_temp.html'),
        'scripts'                  => file_get_contents($dir_comp . 'scripts.html')
    ];

    $template = file_get_contents(__DIR__ . '/../compras.html');
    $m = new Mustache();
    echo $m->render($template, [
        'header_html' => $header_html,
        'menu_html'   => $menu_html,
        'footer_html' => $footer_html,
        'apphost'     => $apphost,
        'varhost'     => $varhost
    ], $partials);
});

/* 2. GET /compra/listar */
Flight::route('GET /compra/listar', function () {
    $sql = "
        SELECT 
            c.compra_id,
            c.proveedor_id,
            p.nombre AS razon_social,
            c.fecha_creacion AS fecha_compra,
            c.total_compra AS total,
            c.observaciones
        FROM compra c
        LEFT JOIN proveedor p ON p.proveedor_id = c.proveedor_id
        WHERE c.borrado_el IS NULL
        ORDER BY c.compra_id DESC
    ";
    Flight::json(DB::query($sql));
});

/* 3. POST /compra/crear */
Flight::route('POST /compra/crear', function () {
    $data = Flight::request()->data->getData();
    $proveedor_id = intval($data['proveedor_id']);

    $fecha = !empty($data['fecha_compra']) 
        ? date('Y-m-d H:i:s', strtotime($data['fecha_compra'])) 
        : date('Y-m-d H:i:s');

    $items         = $data['items'] ?? [];
    $observaciones = $data['observaciones'] ?? '';

    if (empty($items)) {
        Flight::json(['status' => 'error', 'msg' => 'No hay items'], 400);
        return;
    }

    DB::startTransaction();
    try {
        DB::insert('compra', [
            'proveedor_id'   => $proveedor_id,
            'fecha_creacion' => $fecha,
            'observaciones'  => $observaciones,
            'total_compra'   => 0
        ]);

        $compra_id = DB::insertId();
        $total = 0;

        foreach ($items as $it) {
            $producto_id = intval($it['product_id']);
            $cantidad    = intval($it['cantidad']);
            $costo       = floatval($it['costo_unitario']);
            $subtotal    = $cantidad * $costo;
            $total      += $subtotal;

            DB::insert('compra_detalle', [
                'compra_id'       => $compra_id,
                'product_id'      => $producto_id,
                'cantidad'        => $cantidad,
                'precio_unitario' => $costo,
                'subtotal'        => $subtotal
            ]);

            registrar_movimiento_inventario(
                $producto_id,
                'ENTRADA',
                'COMPRA',
                $cantidad,
                $costo,
                $compra_id,
                'compra'
            );
        }

        DB::update('compra', ['total_compra' => $total], "compra_id=%i", $compra_id);
        DB::commit();

        Flight::json(['status' => 'ok', 'compra_id' => $compra_id]);
    } catch (Exception $e) {
        DB::rollback();
        Flight::json(['status' => 'error', 'msg' => $e->getMessage()], 500);
    }
});

/* 4. POST /compra/editar */
Flight::route('POST /compra/editar', function () {
    $data = Flight::request()->data->getData();
    $compra_id = intval($data['compra_id']);

    $updateData = [
        'observaciones' => $data['observaciones'] ?? ''
    ];

    if (!empty($data['proveedor_id'])) {
        $updateData['proveedor_id'] = intval($data['proveedor_id']);
    }

    if (!empty($data['fecha_compra'])) {
        $updateData['fecha_creacion'] = date('Y-m-d H:i:s', strtotime($data['fecha_compra']));
    }

    DB::update('compra', $updateData, "compra_id=%i", $compra_id);

    Flight::json(['status' => 'ok']);
});

/* 5. POST /compra/eliminar */
Flight::route('POST /compra/eliminar', function () {
    $compra_id = intval(Flight::request()->data->compra_id);
    DB::startTransaction();

    try {
        $fecha_borrado = date('Y-m-d H:i:s');
        $det = DB::query("
            SELECT * FROM compra_detalle
            WHERE compra_id=%i AND borrado_el IS NULL
        ", $compra_id);

        foreach ($det as $it) {
            $costo_anterior = DB::queryFirstField("
                SELECT im.precio_unitario
                FROM inventario_movimiento im
                LEFT JOIN compra comp ON comp.compra_id = im.referencia_id AND im.referencia_tabla = 'compra'
                WHERE im.product_id = %i
                  AND im.referencia_id != %i
                  AND (im.referencia_tabla != 'compra' OR comp.borrado_el IS NULL)
                ORDER BY im.inventario_movimiento_id DESC
                LIMIT 1
            ", $it['product_id'], $compra_id);

            if ($costo_anterior === null) {
                $costo_anterior = 0.00;
            }

            registrar_movimiento_inventario(
                $it['product_id'],
                'SALIDA',
                'DEVOLUCION',
                $it['cantidad'],
                $costo_anterior,
                $compra_id,
                'compra'
            );
        }

        DB::update('compra_detalle', ['borrado_el' => $fecha_borrado], "compra_id=%i AND borrado_el IS NULL", $compra_id);
        DB::update('compra', ['borrado_el' => $fecha_borrado], "compra_id=%i AND borrado_el IS NULL", $compra_id);

        DB::commit();
        Flight::json(['status' => 'ok']);
    } catch (Exception $e) {
        DB::rollback();
        Flight::json(['status' => 'error', 'msg' => $e->getMessage()], 500);
    }
});