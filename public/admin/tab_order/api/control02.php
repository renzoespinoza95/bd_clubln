<?php
/* 1. Detalle de Orden */
Flight::route('GET /product_order/detalle/@id', function ($id) {
    $order = DB::queryFirstRow("
        SELECT o.*,
               tp.descripcion AS tipo_pago
        FROM product_order o
        LEFT JOIN tipo_pago tp
               ON tp.tipo_pago_id = o.tipo_pago_id
        WHERE o.product_order_id = %i
          AND o.borrado_el IS NULL
    ", $id);

    if (!$order) {
        Flight::json(['status' => 'error', 'msg' => 'Orden no encontrada'], 404);
        return;
    }

    $det = DB::query("
        SELECT d.*,
               p.name AS product_name
        FROM product_order_detail d
        LEFT JOIN product p
               ON p.product_id = d.product_id
        WHERE d.order_id = %i
          AND d.borrado_el IS NULL
        ORDER BY d.product_order_detail_id ASC
    ", $id);

    Flight::json([
        'order'    => $order,
        'detalles' => $det
    ]);
});

/* 2. Crear Detalle */
Flight::route('POST /product_order_detail/crear', function () {
    $d = Flight::request()->data->getData();
    $now = time() * 1000;

    DB::startTransaction();
    try {
        DB::insert('product_order_detail', [
            'order_id'           => $d['order_id'],
            'product_id'         => $d['product_id'],
            'product_name'       => DB::queryFirstField("SELECT name FROM product WHERE product_id=%i", $d['product_id']),
            'amount'             => $d['amount'],
            'price_item'         => $d['price_item'],
            'created_at'         => $now,
            'last_update'        => $now,
            'fecha_creacion'     => date('Y-m-d H:i:s'),
            'fecha_modificacion' => date('Y-m-d H:i:s')
        ]);

        registrar_movimiento_inventario(
            $d['product_id'],
            'SALIDA',
            'VENTA',
            $d['amount'],
            $d['price_item'],
            $d['order_id'],
            'product_order'
        );

        actualizar_estado_orden($d['order_id'], 'AGREGADO');
        recalcular_total_orden($d['order_id']);

        DB::commit();
        Flight::json(['status' => 'ok']);
    } catch (Exception $e) {
        DB::rollback();
        Flight::json(['status' => 'error', 'msg' => $e->getMessage()], 500);
    }
});

/* 3. Eliminar Detalle */
Flight::route('POST /product_order_detail/eliminar', function () {
    $d = Flight::request()->data->getData();
    $product_order_detail_id = (int)$d['product_order_detail_id'];

    DB::startTransaction();
    try {
        $detalle = DB::queryFirstRow("
            SELECT *
            FROM product_order_detail
            WHERE product_order_detail_id=%i
              AND borrado_el IS NULL
        ", $product_order_detail_id);

        if (!$detalle) {
            DB::rollback();
            Flight::json(['status' => 'error', 'msg' => 'Detalle no encontrado'], 404);
            return;
        }

        registrar_movimiento_inventario(
            $detalle['product_id'],
            'ENTRADA',
            'DEVOLUCION',
            $detalle['amount'],
            $detalle['costo_unitario'],
            $detalle['order_id'],
            'product_order'
        );

        DB::update(
            'product_order_detail',
            [
                'borrado_el'         => date('Y-m-d H:i:s'),
                'fecha_modificacion' => date('Y-m-d H:i:s')
            ],
            "product_order_detail_id=%i",
            $product_order_detail_id
        );

        recalcular_total_orden($detalle['order_id']);

        DB::commit();
        Flight::json(['status' => 'ok']);
    } catch (Exception $e) {
        DB::rollback();
        Flight::json(['status' => 'error', 'msg' => $e->getMessage()], 500);
    }
});

/* 4. Editar Detalle */
Flight::route('POST /product_order_detail/editar', function () {
    $d = Flight::request()->data->getData();

    $old = DB::queryFirstRow(
        "SELECT * FROM product_order_detail WHERE product_order_detail_id=%i",
        $d['product_order_detail_id']
    );

    DB::startTransaction();
    try {
        registrar_movimiento_inventario(
            $old['product_id'],
            'ENTRADA',
            'AJUSTE',
            $old['amount'],
            $old['price_item'],
            $old['order_id'],
            'product_order'
        );

        registrar_movimiento_inventario(
            $d['product_id'],
            'SALIDA',
            'VENTA',
            $d['amount'],
            $d['price_item'],
            $old['order_id'],
            'product_order'
        );

        actualizar_estado_orden($old['order_id'], 'EDITADO');

        DB::update('product_order_detail', [
            'amount'             => $d['amount'],
            'price_item'         => $d['price_item'],
            'last_update'        => time() * 1000,
            'fecha_modificacion' => date('Y-m-d H:i:s')
        ], "product_order_detail_id=%i", $d['product_order_detail_id']);

        recalcular_total_orden($old['order_id']);

        DB::commit();
        Flight::json(['status' => 'ok']);
    } catch (Exception $e) {
        DB::rollback();
        Flight::json(['status' => 'error', 'msg' => $e->getMessage()], 500);
    }
});

/* 5. Liberar Mesa por Orden */
Flight::route('POST /product_order/liberar_mesa', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();

    $d = Flight::request()->data->getData();
    $order_id = (int)$d['product_order_id'];

    $order = DB::queryFirstRow(
        "SELECT mesa_id FROM product_order WHERE product_order_id=%i",
        $order_id
    );

    if (!$order || !$order['mesa_id']) {
        Flight::json(['status' => 'error'], 400);
        return;
    }

    DB::startTransaction();
    try {
        DB::update('product_order', [
            'fecha_fin'          => date('Y-m-d H:i:s'),
            'status'             => 'CERRADA',
            'fecha_modificacion' => date('Y-m-d H:i:s'),
            'last_update'        => time() * 1000
        ], "product_order_id=%i", $order_id);

        DB::update('mesa', [
            'estado' => 'DISPONIBLE'
        ], "mesa_id=%i", $order['mesa_id']);

        DB::commit();
        Flight::json(['status' => 'ok']);
    } catch (Exception $e) {
        DB::rollback();
        Flight::json(['status' => 'error', 'msg' => $e->getMessage()], 500);
    }
});