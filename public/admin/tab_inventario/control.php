<?php
Flight::route('GET /inventario', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $path_public;

    include $path_public . '/admin/tab_inventario/inicio.php';
});

/* ============================================================
 * LISTAR INVENTARIO
 * ============================================================ */
Flight::route('GET /inventario/listar', function () {

    $sql = "
        SELECT 
            i.inventario_id,
            i.producto_id,
            p.name AS producto,
            i.stock_actual,
            i.stock_min,
            i.stock_max
        FROM inventario i
        INNER JOIN product p ON p.id = i.producto_id
        ORDER BY p.name ASC
    ";

    Flight::json(DB::query($sql));
});

/* ============================================================
 * LISTAR PRODUCTOS (para combos)
 * ============================================================ */
Flight::route('GET /inventario/productos', function () {
    $rows = DB::query("SELECT id, name FROM product ORDER BY name ASC");
    Flight::json($rows);
});

/* ============================================================
 * CREAR INVENTARIO
 * ============================================================ */
Flight::route('POST /inventario/crear', function () {

    $d = Flight::request()->data;

    DB::insert("inventario", [
        "producto_id"  => $d["producto_id"],
        "stock_actual" => $d["stock_actual"],
        "stock_min"    => $d["stock_min"],
        "stock_max"    => $d["stock_max"]
    ]);

    Flight::json(["status" => "ok"]);
});

/* ============================================================
 * DETALLE INVENTARIO (trae movimientos)
 * ============================================================ */
Flight::route('GET /inventario/detalle/@id', function ($id) {

    $inv = DB::queryFirstRow("
        SELECT 
            i.*, 
            p.name AS producto
        FROM inventario i
        INNER JOIN product p ON p.id = i.producto_id
        WHERE inventario_id=%i
    ", $id);

    $mov = DB::query("
        SELECT *
        FROM inventario_movimiento
        WHERE producto_id=%i
        ORDER BY fecha DESC
    ", $inv['producto_id']);

    Flight::json([
        'inventario' => $inv,
        'movimientos' => $mov
    ]);
});

/* ============================================================
 * EDITAR INVENTARIO
 * ============================================================ */
Flight::route('POST /inventario/editar', function () {

    $d = Flight::request()->data;

    DB::update("inventario", [
        "stock_min" => $d["stock_min"],
        "stock_max" => $d["stock_max"]
    ], "inventario_id=%i", $d["inventario_id"]);

    Flight::json(["status" => "ok"]);
});

/* ============================================================
 * ELIMINAR INVENTARIO
 * ============================================================ */
Flight::route('POST /inventario/eliminar', function () {

    $id = intval(Flight::request()->data->inventario_id);

    DB::delete("inventario", "inventario_id=%i", $id);

    Flight::json(["status" => "ok"]);
});

/* ============================================================
 * REGISTRAR MOVIMIENTO
   BODY:
   {
     producto_id: 3,
     tipo: 'ENTRADA',
     origen: 'AJUSTE',
     cantidad: 10,
     precio_unitario: 5,
     referencia_id: null,
     referencia_tabla: null
   }
 * ============================================================ */
Flight::route('POST /inventario/movimiento', function () {

    $d = Flight::request()->data;
    $prod = intval($d['producto_id']);
    $cant = intval($d['cantidad']);

    /* Obtener stock actual */
    $inv = DB::queryFirstRow("SELECT * FROM inventario WHERE producto_id=%i", $prod);

    if (!$inv) {
        Flight::json(['status'=>'error','msg'=>'Inventario no encontrado'], 400);
        return;
    }

    $nuevo_stock = $inv['stock_actual'];

    if ($d['tipo'] === 'ENTRADA') {
        $nuevo_stock += $cant;
    } elseif ($d['tipo'] === 'SALIDA') {
        $nuevo_stock -= $cant;
    }

    DB::startTransaction();

    try {

        /* Registrar movimiento */
        DB::insert("inventario_movimiento", [
            "producto_id"      => $prod,
            "tipo"             => $d["tipo"],
            "origen"           => $d["origen"],
            "cantidad"         => $cant,
            "precio_unitario"  => $d["precio_unitario"],
            "referencia_id"    => $d["referencia_id"],
            "referencia_tabla" => $d["referencia_tabla"],
            "stock_resultante" => $nuevo_stock
        ]);

        /* Actualizar inventario */
        DB::update("inventario", [
            "stock_actual" => $nuevo_stock
        ], "inventario_id=%i", $inv['inventario_id']);

        DB::commit();
        Flight::json(["status"=>"ok"]);

    } catch(Exception $e) {
        DB::rollback();
        Flight::json(['status'=>'error','msg'=>$e->getMessage()], 500);
    }
});