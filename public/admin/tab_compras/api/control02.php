<?php
/* 1. GET /proveedor/listar */
Flight::route('GET /proveedor/listar', function () {
    $rows = DB::query("SELECT * FROM proveedor WHERE is_activo=1 ORDER BY nombre ASC");
    Flight::json($rows);
});

/* 2. GET /producto/listar */
Flight::route('GET /producto/listar', function () {
    $sql = "
      SELECT 
        p.product_id,
        p.name,
        p.price,
        IFNULL(MAX(i.stock_actual),0) AS stock
      FROM product p
      LEFT JOIN inventario i ON i.product_id = p.product_id
      ORDER BY p.name ASC
    ";
    Flight::json(DB::query($sql));
});

/* 3. GET /compra/detalle/@id */
Flight::route('GET /compra/detalle/@id', function ($compra_id) {
    $cab = DB::queryFirstRow("
        SELECT 
            c.compra_id,
            p.nombre AS razon_social,
            c.fecha_creacion AS fecha_compra,
            c.total_compra AS total,
            c.observaciones
        FROM compra c
        LEFT JOIN proveedor p ON p.proveedor_id=c.proveedor_id
        WHERE c.compra_id=%i
    ", $compra_id);

    $det = DB::query("
        SELECT 
            d.compra_detalle_id,
            d.cantidad,
            d.precio_unitario AS costo_unitario,
            d.subtotal,
            pr.name AS producto
        FROM compra_detalle d
        INNER JOIN product pr ON pr.product_id = d.product_id
        WHERE d.compra_id=%i
    ", $compra_id);

    Flight::json(['cabecera' => $cab, 'detalle' => $det]);
});

/* 4. POST /compra/agregar-items */
Flight::route('POST /compra/agregar-items', function () {
    $data = Flight::request()->data->getData();
    DB::startTransaction();

    try {
        foreach ($data['items'] as $it) {
            $subtotal = $it['cantidad'] * $it['costo_unitario'];

            DB::insert('compra_detalle', [
                'compra_id'       => $data['compra_id'],
                'product_id'      => $it['product_id'],
                'cantidad'        => $it['cantidad'],
                'precio_unitario' => $it['costo_unitario'],
                'subtotal'        => $subtotal
            ]);

            registrar_movimiento_inventario(
                $it['product_id'],
                'ENTRADA',
                'AJUSTE',
                $it['cantidad'],
                $it['costo_unitario'],
                $data['compra_id'],
                'compra'
            );
        }

        DB::query("
            UPDATE compra
            SET total_compra = (
              SELECT SUM(subtotal) FROM compra_detalle WHERE compra_id=%i
            )
            WHERE compra_id=%i
        ", $data['compra_id'], $data['compra_id']);

        DB::commit();
        Flight::json(['status' => 'ok']);
    } catch (Exception $e) {
        DB::rollback();
        Flight::json(['status' => 'error', 'msg' => $e->getMessage()], 500);
    }
});

/* 5. GET /compra/items/@compra_id */
Flight::route('GET /compra/items/@compra_id', function ($compra_id) {
    $rows = DB::query("
        SELECT 
            d.product_id,
            p.name AS producto,
            d.cantidad,
            d.precio_unitario
        FROM compra_detalle d
        INNER JOIN product p ON p.product_id = d.product_id
        WHERE d.compra_id = %i
    ", $compra_id);

    Flight::json($rows);
});