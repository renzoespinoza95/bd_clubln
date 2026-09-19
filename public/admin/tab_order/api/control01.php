<?php
if (!function_exists('generarCodigoOrden')) {
    function generarCodigoOrden() {
        return strtoupper(bin2hex(random_bytes(4)));
    }
}

if (!function_exists('actualizar_estado_orden')) {
    function actualizar_estado_orden($order_id, $estado) {
        DB::update(
            'product_order',
            [
                'status'             => $estado,
                'fecha_modificacion' => date('Y-m-d H:i:s'),
                'last_update'        => time() * 1000
            ],
            "product_order_id=%i",
            $order_id
        );
    }
}

if (!function_exists('recalcular_total_orden')) {
    function recalcular_total_orden($order_id) {
        // 🔥 Corrección: Excluir ítems con borrado lógico (borrado_el IS NULL)
        $total = DB::queryFirstField("
            SELECT IFNULL(SUM(amount * price_item), 0)
            FROM product_order_detail
            WHERE order_id = %i 
              AND borrado_el IS NULL
        ", $order_id);

        DB::update('product_order', [
            'total_fees'         => $total,
            'fecha_modificacion' => date('Y-m-d H:i:s'),
            'last_update'        => time() * 1000
        ], "product_order_id = %i", $order_id);
    }
}

/* 1. Render Vista Principal */
Flight::route('GET /order', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();

    global $path_public, $apphost, $varhost;

    ob_start();
    include $path_public . "/admin/header.php";
    $header_html = ob_get_clean();

    ob_start();
    include $path_public . "/admin/menu.php";
    $menu_html = ob_get_clean();

    ob_start();
    include $path_public . "/admin/footer.php";
    $footer_html = ob_get_clean();

    $dir_comp = __DIR__ . '/../comp_order/';
    $partials = [
        'head'                            => file_get_contents($dir_comp . 'head.html'),
        'tabla_order'                     => file_get_contents($dir_comp . 'tabla_order.html'),
        'modal_detalle'                   => file_get_contents($dir_comp . 'modal_detalle.html'),
        'modal_crear'                     => file_get_contents($dir_comp . 'modal_crear.html'),
        'modal_editar'                    => file_get_contents($dir_comp . 'modal_editar.html'),
        'modal_crear_detail'              => file_get_contents($dir_comp . 'modal_crear_detail.html'),
        'modal_editar_detail'             => file_get_contents($dir_comp . 'modal_editar_detail.html'),
        'modal_agregar_item_nueva_orden'  => file_get_contents($dir_comp . 'modal_agregar_item_nueva_orden.html'),
        'modal_clientes'                  => file_get_contents($dir_comp . 'modal_clientes.html'),
        'modal_nuevo_cliente'             => file_get_contents($dir_comp . 'modal_nuevo_cliente.html'),
        'modal_editar_cliente'            => file_get_contents($dir_comp . 'modal_editar_cliente.html'),
        'modal_mesas'                     => file_get_contents($dir_comp . 'modal_mesas.html'),
        'modal_reporte_ventas'            => file_get_contents($dir_comp . 'modal_reporte_ventas.html'),
        'modal_reporte_ventas_admin'      => file_get_contents($dir_comp . 'modal_reporte_ventas_admin.html'),
        'modal_resumen_ventas'            => file_get_contents($dir_comp . 'modal_resumen_ventas.html'),
        'scripts'                         => file_get_contents($dir_comp . 'scripts.html')
    ];

    $template = file_get_contents(__DIR__ . '/../order.html');
    $m = new Mustache();
    echo $m->render($template, [
        'header_html' => $header_html,
        'menu_html'   => $menu_html,
        'footer_html' => $footer_html,
        'apphost'     => $apphost,
        'varhost'     => $varhost
    ], $partials);
});

/* 2. Listar Órdenes */
Flight::route('GET /product_order/listar', function () {
    $rows = DB::query("
        SELECT 
          po.product_order_id,
          po.serial,
          po.total_fees,
          po.modo_order_id,
          mo.nombre AS modo_order,
          cl.nombre AS cliente,
          m.nombre AS mesa_nombre,
          a.nombres_apellidos AS administrador,
          tp.descripcion AS tipo_pago,
          DATE_FORMAT(po.fecha_creacion,'%d/%m/%Y %H:%i') AS fecha
        FROM product_order po
        LEFT JOIN cliente cl ON cl.cliente_id = po.cliente_id
        LEFT JOIN mesa m ON m.mesa_id = po.mesa_id
        LEFT JOIN modo_order mo ON mo.modo_order_id = po.modo_order_id
        LEFT JOIN tipo_pago tp ON tp.tipo_pago_id = po.tipo_pago_id
        LEFT JOIN administradortbl a ON a.administrador_id = po.administrador_id
        WHERE po.borrado_el IS NULL
        ORDER BY po.product_order_id DESC
    ");
    Flight::json($rows);
});

/* 3. Crear Orden */
Flight::route('POST /product_order/crear', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();

    $administrador_id = (int)$info_admin['administrador_id'];

    $caja = DB::queryFirstRow("
        SELECT *
        FROM caja
        WHERE administrador_id = %i
          AND DATE(fecha_apertura) = CURDATE()
          AND estado = 'ABIERTA'
        ORDER BY fecha_apertura DESC
        LIMIT 1
    ", $administrador_id);

    if (!$caja) {
        Flight::json([
            'status' => 'error',
            'msg'    => 'La caja de este usuario está cerrada'
        ], 403);
        return;
    }

    $d = Flight::request()->data->getData();

    if (empty($d['cliente_id'])) {
        Flight::json(['status' => 'error', 'msg' => 'Debe seleccionar un cliente'], 400);
        return;
    }

    if (empty($d['tipo_pago_id'])) {
        Flight::json(['status' => 'error', 'msg' => 'Debe seleccionar tipo de pago'], 400);
        return;
    }

    if (empty($d['items']) || !is_array($d['items'])) {
        Flight::json(['status' => 'error', 'msg' => 'Debe agregar al menos un producto'], 400);
        return;
    }

    $mesa_id = isset($d['mesa_id']) ? (int)$d['mesa_id'] : -1;
    if ($mesa_id < 0) {
        Flight::json(['status' => 'error', 'msg' => 'Debe seleccionar una mesa o DIRECTO'], 400);
        return;
    }

    $modo_order_id = 1;
    $mesa_id_db    = null;
    $fecha_inicio  = null;

    if ($mesa_id > 0) {
        $ocupada = DB::queryFirstField("
            SELECT COUNT(*)
            FROM product_order
            WHERE mesa_id=%i
              AND modo_order_id=2
              AND borrado_el IS NULL
        ", $mesa_id);

        if ($ocupada) {
            Flight::json(['status' => 'error', 'msg' => 'Mesa ocupada'], 409);
            return;
        }

        $modo_order_id = 2;
        $mesa_id_db    = $mesa_id;
        $fecha_inicio  = date('Y-m-d H:i:s');
    }

    foreach ($d['items'] as $item) {
        $product_id = (int)$item['product_id'];
        $cantidad   = (int)$item['amount'];

        $producto = DB::queryFirstRow("
            SELECT
                p.name,
                IFNULL(i.stock_actual,0) AS stock_actual
            FROM product p
            LEFT JOIN inventario i
                ON i.product_id = p.product_id
               AND i.borrado_el IS NULL
            WHERE p.product_id=%i
              AND p.borrado_el IS NULL
        ", $product_id);

        if (!$producto) {
            Flight::json(['status' => 'error', 'msg' => 'Producto inexistente', 'product_id' => $product_id], 404);
            return;
        }

        if ((int)$producto['stock_actual'] < $cantidad) {
            Flight::json([
                'status'              => 'error',
                'msg'                 => 'Stock insuficiente',
                'producto'            => $producto['name'],
                'product_id'          => $product_id,
                'stock_actual'        => (int)$producto['stock_actual'],
                'cantidad_solicitada' => $cantidad
            ], 409);
            return;
        }
    }

    DB::startTransaction();
    try {
        if ($mesa_id > 0) {
            DB::update('mesa', ['estado' => 'OCUPADA'], "mesa_id=%i", $mesa_id);
        }

        $now = time() * 1000;

        DB::insert('product_order', [
            'serial'             => generarCodigoOrden(),
            'administrador_id'   => $administrador_id,
            'cliente_id'         => $d['cliente_id'],
            'caja_id'            => $caja['caja_id'],
            'tipo_pago_id'       => $d['tipo_pago_id'],
            'mesa_id'            => $mesa_id_db,
            'modo_order_id'      => $modo_order_id,
            'total_fees'         => 0,
            'tax'                => 0,
            'fecha_inicio'       => $fecha_inicio,
            'fecha_creacion'     => date('Y-m-d H:i:s'),
            'fecha_modificacion' => date('Y-m-d H:i:s'),
            'created_at'         => $now,
            'last_update'        => $now
        ]);

        $order_id = DB::insertId();

        foreach ($d['items'] as $i) {
            $costo = DB::queryFirstField("
                SELECT precio_unitario
                FROM inventario_movimiento
                WHERE product_id=%i
                ORDER BY inventario_movimiento_id DESC
                LIMIT 1
            ", $i['product_id']);

            if (!$costo) {
                $costo = 0;
            }

            DB::insert('product_order_detail', [
                'order_id'           => $order_id,
                'product_id'         => $i['product_id'],
                'product_name'       => DB::queryFirstField("SELECT name FROM product WHERE product_id=%i", $i['product_id']),
                'amount'             => $i['amount'],
                'price_item'         => $i['price_item'],
                'costo_unitario'     => $costo,
                'created_at'         => $now,
                'last_update'        => $now,
                'fecha_creacion'     => date('Y-m-d H:i:s'),
                'fecha_modificacion' => date('Y-m-d H:i:s'),
                'borrado_el'         => null
            ]);

            registrar_movimiento_inventario(
                $i['product_id'],
                'SALIDA',
                'VENTA',
                $i['amount'],
                $i['price_item'],
                $order_id,
                'product_order'
            );
        }

        recalcular_total_orden($order_id);
        DB::commit();

        Flight::json([
            'status'           => 'ok',
            'product_order_id' => $order_id,
            'mesa_id'          => $mesa_id_db
        ]);
    } catch (Exception $e) {
        DB::rollback();
        Flight::json(['status' => 'error', 'msg' => $e->getMessage()], 500);
    }
});

/* 4. Editar Orden */
Flight::route('POST /product_order/editar', function () {
    $d = Flight::request()->data->getData();
    $now = time() * 1000;

    DB::update('product_order', [
        'buyer'              => $d['buyer'] ?? '',
        'address'            => $d['address'] ?? '',
        'status'             => $d['status'] ?? '',
        'last_update'        => $now,
        'fecha_modificacion' => date("Y-m-d H:i:s")
    ], "product_order_id=%i", $d['product_order_id']);

    Flight::json(['status' => 'ok']);
});

/* 5. Eliminar Orden */
Flight::route('POST /product_order/eliminar', function () {
    $d = Flight::request()->data->getData();
    $product_order_id = (int)$d['product_order_id'];

    DB::startTransaction();
    try {
        $fecha_borrado = date('Y-m-d H:i:s');

        $orden = DB::queryFirstRow("
            SELECT *
            FROM product_order
            WHERE product_order_id=%i AND borrado_el IS NULL
        ", $product_order_id);

        if (!$orden) {
            DB::rollback();
            Flight::json(['status' => 'error', 'msg' => 'La orden no existe'], 404);
            return;
        }

        $detalles = DB::query("
            SELECT *
            FROM product_order_detail
            WHERE order_id=%i AND borrado_el IS NULL
        ", $product_order_id);

        foreach ($detalles as $it) {
            registrar_movimiento_inventario(
                $it['product_id'],
                'ENTRADA',
                'DEVOLUCION',
                $it['amount'],
                $it['costo_unitario'],
                $product_order_id,
                'product_order'
            );
        }

        DB::update(
            'product_order_detail',
            ['borrado_el' => $fecha_borrado],
            "order_id=%i AND borrado_el IS NULL",
            $product_order_id
        );

        DB::update(
            'product_order',
            [
                'borrado_el'         => $fecha_borrado,
                'fecha_modificacion' => $fecha_borrado
            ],
            "product_order_id=%i",
            $product_order_id
        );

        if (!empty($orden['mesa_id'])) {
            DB::update('mesa', ['estado' => 'DISPONIBLE'], "mesa_id=%i", $orden['mesa_id']);
        }

        DB::commit();
        Flight::json(['status' => 'ok']);
    } catch (Exception $e) {
        DB::rollback();
        Flight::json(['status' => 'error', 'msg' => $e->getMessage()], 500);
    }
});