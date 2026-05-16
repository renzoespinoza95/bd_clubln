<?php

/* ============================================
 * VISTA
 * ============================================ */
Flight::route('GET /balance', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();

    global $path_public;
    include $path_public . '/admin/tab_balance/inicio.php';
});


/* ============================================
 * LISTAR BALANCE
 * ============================================ */
Flight::route('GET /balance/listar', function () {

    $rows = DB::query("
        SELECT *
        FROM balance_mensual
        ORDER BY anio DESC, mes DESC
    ");

    Flight::json($rows);
});


/* ============================================
 * GENERAR BALANCE
 * ============================================ */
Flight::route('POST /balance/generar', function () {

    $d = Flight::request()->data->getData();

    $anio = intval($d['anio']);
    $mes  = intval($d['mes']);

    $inicio = "$anio-$mes-01 00:00:00";
    $fin = date("Y-m-t 23:59:59", strtotime($inicio));

    $ventas = DB::queryFirstField("
        SELECT IFNULL(SUM(total_fees),0)
        FROM product_order
        WHERE fecha_creacion BETWEEN %s AND %s
    ", $inicio, $fin);

    $caja_ing = DB::queryFirstField("
        SELECT IFNULL(SUM(monto),0)
        FROM caja_chica_movimiento
        WHERE tipo='INGRESO'
        AND fecha BETWEEN %s AND %s
    ", $inicio, $fin);

    $facturado = DB::queryFirstField("
        SELECT IFNULL(SUM(monto),0)
        FROM comprobante
        WHERE fecha BETWEEN %s AND %s
    ", $inicio, $fin);

    $caja_eg = DB::queryFirstField("
        SELECT IFNULL(SUM(monto),0)
        FROM caja_chica_movimiento
        WHERE tipo='EGRESO'
        AND fecha BETWEEN %s AND %s
    ", $inicio, $fin);

    $compras = DB::queryFirstField("
        SELECT IFNULL(SUM(total_compra),0)
        FROM compra
        WHERE fecha_creacion BETWEEN %s AND %s
    ", $inicio, $fin);

    $total_ingresos = $ventas + $caja_ing + $facturado;
    $total_egresos  = $caja_eg + $compras;
    $utilidad       = $total_ingresos - $total_egresos;

    DB::insert('balance_mensual', [
        'anio'=>$anio,
        'mes'=>$mes,
        'saldo_anterior'=>0,
        'total_ingresos'=>$total_ingresos,
        'total_egresos'=>$total_egresos,
        'utilidad'=>$utilidad
    ]);

    Flight::json(['status'=>'ok']);
});


/* ============================================
 * INGRESOS
 * ============================================ */
Flight::route('GET /balance/ingresos', function(){

    $rows = DB::query("
        SELECT fecha_creacion fecha, 'Venta' tipo, total_fees monto
        FROM product_order

        UNION ALL

        SELECT fecha, 'Caja chica', monto
        FROM caja_chica_movimiento WHERE tipo='INGRESO'

        UNION ALL

        SELECT fecha, 'Facturado', monto
        FROM comprobante
    ");

    Flight::json($rows);
});


/* ============================================
 * EGRESOS
 * ============================================ */
Flight::route('GET /balance/egresos', function(){

    $rows = DB::query("
        SELECT fecha, categoria tipo, monto
        FROM caja_chica_movimiento WHERE tipo='EGRESO'

        UNION ALL

        SELECT fecha_creacion, 'Compra', total_compra
        FROM compra
    ");

    Flight::json($rows);
});


/* ============================================
 * CAJA CHICA
 * ============================================ */
Flight::route('POST /LF4f/caja_chica/crear', function(){

    $d = Flight::request()->data->getData();

    DB::startTransaction();

    try {

        // 1. insertar caja chica
        DB::insert('caja_chica_movimiento', [
            'tipo'=>$d['tipo'],
            'categoria'=>$d['categoria'],
            'descripcion'=>$d['descripcion'],
            'monto'=>$d['monto'],
            'fecha'=>$d['fecha']
        ]);

        // 2. si es facturado → insertar comprobante
        if($d['categoria']=='FACTURADO'){

            DB::insert('comprobante', [
                'tipo_comprobante'=>$d['tipo_comprobante'],
                'tipo_entidad'=>$d['tipo']=='INGRESO' ? 'CLIENTE' : 'PROVEEDOR',
                'cliente_id'=>$d['cliente_id'] ?? null,
                'proveedor_id'=>$d['proveedor_id'] ?? null,
                'nombre_entidad'=>null,
                'numero'=>$d['numero'],
                'monto'=>$d['monto'],
                'fecha'=>$d['fecha'],
                'descripcion'=>$d['descripcion']
            ]);

        }

        DB::commit();

        Flight::json(['status'=>'ok']);

    } catch(Exception $e){
        DB::rollback();
        Flight::json(['status'=>'error','msg'=>$e->getMessage()]);
    }

});

Flight::route('GET /LF4f/caja_chica/listar', function(){

    $rows = DB::query("
        SELECT *
        FROM caja_chica_movimiento
        ORDER BY fecha DESC
    ");

    Flight::json($rows);
});

/* ============================================
 * COMPROBANTES
 * ============================================ */
Flight::route('POST /comprobante/crear', function(){

    DB::insert('comprobante', Flight::request()->data->getData());

    Flight::json(['status'=>'ok']);
});


Flight::route('GET /LF4f/comprobante/listar', function(){

    $rows = DB::query("
        SELECT 
            c.*,
            cli.nombre AS cliente_nombre,
            p.nombre AS proveedor_nombre
        FROM comprobante c
        LEFT JOIN cliente cli ON cli.cliente_id = c.cliente_id
        LEFT JOIN proveedor p ON p.proveedor_id = c.proveedor_id
        ORDER BY c.fecha DESC
    ");

    Flight::json($rows);
});

/* ============================================
 * DEUDAS
 * ============================================ */

Flight::route('POST /deuda/crear', function(){

    $d = Flight::request()->data->getData();

    DB::insert('deuda', [
        'tipo_entidad'=>$d['tipo_entidad'],
        'nombre_persona'=>$d['nombre_persona'],
        'proveedor_id'=>$d['proveedor_id'],
        'monto_total'=>$d['monto_total'],
        'saldo_pendiente'=>$d['monto_total'],
        'descripcion'=>$d['descripcion']
    ]);

    Flight::json(['status'=>'ok']);
});

/* ============================================
 * DETALLE DEUDA
 * ============================================ */
Flight::route('GET /deuda/detalle/@id', function($id){

    $cab = DB::queryFirstRow("SELECT * FROM deuda WHERE deuda_id=%i",$id);

    $pagos = DB::query("SELECT * FROM deuda_pago WHERE deuda_id=%i",$id);

    Flight::json([
        'cabecera'=>$cab,
        'pagos'=>$pagos
    ]);
});

/* ============================================
 * PROVEEDORES (para vue-select)
 * ============================================ */
Flight::route('GET /LF4f/proveedor/listar', function () {

    $rows = DB::query("
        SELECT proveedor_id, nombre
        FROM proveedor
        WHERE is_activo=1
        ORDER BY nombre ASC
    ");

    Flight::json($rows);
});


/* ============================================
 * DEUDAS
 * ============================================ */

Flight::route('GET /LF4f/deuda/listar', function(){

    $rows = DB::query("
        SELECT 
            d.deuda_id,
            d.tipo_entidad,
            d.nombre_persona,
            p.nombre AS proveedor_nombre,
            d.monto_total,
            d.saldo_pendiente,
            IFNULL(SUM(dp.monto),0) pagado
        FROM deuda d
        LEFT JOIN proveedor p ON p.proveedor_id = d.proveedor_id
        LEFT JOIN deuda_pago dp ON dp.deuda_id = d.deuda_id
        GROUP BY d.deuda_id
        ORDER BY d.deuda_id DESC
    ");

    Flight::json($rows);
});


Flight::route('POST /LF4f/deuda/crear', function(){

    $d = Flight::request()->data->getData();

    DB::insert('deuda', [
        'tipo_entidad'=>$d['tipo_entidad'],
        'nombre_persona'=>$d['nombre_persona'],
        'proveedor_id'=>$d['proveedor_id'],
        'monto_total'=>$d['monto_total'],
        'saldo_pendiente'=>$d['monto_total'],
        'descripcion'=>$d['descripcion']
    ]);

    Flight::json(['status'=>'ok']);
});


Flight::route('POST /LF4f/deuda/pagar', function(){

    DB::query("SET NAMES 'utf8mb4'");

    $d = Flight::request()->data->getData();

    $deuda_id   = intval($d['deuda_id'] ?? 0);
    $monto      = floatval($d['monto'] ?? 0);
    $medio_pago = trim($d['medio_pago'] ?? '');

    // 🔥 FECHA PERSONALIZABLE (CLAVE PARA EL BALANCE)
    $fecha = !empty($d['fecha']) 
        ? $d['fecha'] 
        : date('Y-m-d H:i:s');

    /* ============================================
     * VALIDACIONES
     * ============================================ */

    if(!$deuda_id || $monto <= 0){
        Flight::json([
            'status'=>'error',
            'msg'=>'Datos inválidos'
        ]);
        return;
    }

    $deuda = DB::queryFirstRow("
        SELECT *
        FROM deuda
        WHERE deuda_id = %i
    ", $deuda_id);

    if(!$deuda){
        Flight::json([
            'status'=>'error',
            'msg'=>'Deuda no encontrada'
        ]);
        return;
    }

    if($deuda['saldo_pendiente'] <= 0){
        Flight::json([
            'status'=>'error',
            'msg'=>'La deuda ya está cancelada'
        ]);
        return;
    }

    if($monto > $deuda['saldo_pendiente']){
        Flight::json([
            'status'=>'error',
            'msg'=>'El monto excede el saldo pendiente'
        ]);
        return;
    }

    /* ============================================
     * TRANSACCIÓN
     * ============================================ */

    DB::startTransaction();

    try {

        /* ============================================
         * 1. REGISTRAR PAGO
         * ============================================ */

        DB::insert('deuda_pago', [
            'deuda_id'   => $deuda_id,
            'monto'      => $monto,
            'medio_pago' => $medio_pago,
            'fecha'      => $fecha
        ]);

        /* ============================================
         * 2. ACTUALIZAR SALDO
         * ============================================ */

        DB::query("
            UPDATE deuda
            SET saldo_pendiente = saldo_pendiente - %d
            WHERE deuda_id = %i
        ", $monto, $deuda_id);

        /* ============================================
         * 3. IMPACTO EN CAJA (EGRESO REAL)
         * ============================================ */

        $descripcion = 'Pago deuda #' . $deuda_id;

        if(!empty($deuda['nombre_persona'])){
            $descripcion .= ' - ' . $deuda['nombre_persona'];
        }

        DB::insert('caja_chica_movimiento', [
            'tipo'        => 'EGRESO',
            'categoria'      => 'PAGO_DEUDA',
            'descripcion' => $descripcion,
            'monto'       => $monto,
            'fecha'       => $fecha
        ]);

        DB::commit();

        Flight::json([
            'status'=>'ok'
        ]);

    } catch(Exception $e){

        DB::rollback();

        Flight::json([
            'status'=>'error',
            'msg'=>$e->getMessage()
        ]);
    }

});

Flight::route('GET /LF4f/cliente/listar', function () {

    $rows = DB::query("
        SELECT cliente_id, nombre
        FROM cliente
        WHERE is_activo=1
        ORDER BY nombre ASC
    ");

    Flight::json($rows);
});

Flight::route('POST /LF4f/balance/resumen', function(){

    $d = Flight::request()->data->getData();

    $ini = $d['fecha_inicio'] . " 00:00:00";
    $fin = $d['fecha_fin'] . " 23:59:59";

    /* ============================================
     * INGRESOS (TOTAL EMPRESA)
     * ============================================ */

    $ventas = DB::queryFirstField("
        SELECT IFNULL(SUM(

            CASE 
                WHEN c.participa_reparto = 1
                THEN (d.amount * d.price_item) 
                     * (c.porcentaje_propietario / 100)

                ELSE (d.amount * d.price_item)
            END

        ),0)

        FROM product_order_detail d

        INNER JOIN product_order o 
            ON o.product_order_id = d.order_id

        INNER JOIN product p
            ON p.product_id = d.product_id

        INNER JOIN product_category pc 
            ON pc.product_id = d.product_id

        INNER JOIN category c 
            ON c.id = pc.category_id

        WHERE o.fecha_creacion BETWEEN %s AND %s

        AND o.borrado_el IS NULL
        AND p.borrado_el IS NULL
        AND c.borrado_el IS NULL
    ", $ini, $fin);


    $caja_ing = DB::queryFirstField("
        SELECT IFNULL(SUM(monto),0)

        FROM caja_chica_movimiento

        WHERE tipo='INGRESO'
        AND categoria='CAJA_CHICA'
        AND fecha BETWEEN %s AND %s
    ", $ini, $fin);


    $facturado_ing = DB::queryFirstField("
        SELECT IFNULL(SUM(monto),0)

        FROM caja_chica_movimiento

        WHERE tipo='INGRESO'
        AND categoria='FACTURADO'
        AND fecha BETWEEN %s AND %s
    ", $ini, $fin);


    /* ============================================
     * EGRESOS
     * ============================================ */

    $caja_eg = DB::queryFirstField("
        SELECT IFNULL(SUM(monto),0)

        FROM caja_chica_movimiento

        WHERE tipo='EGRESO'
        AND categoria='CAJA_CHICA'
        AND fecha BETWEEN %s AND %s
    ", $ini, $fin);


    $pago_deuda = DB::queryFirstField("
        SELECT IFNULL(SUM(monto),0)

        FROM caja_chica_movimiento

        WHERE tipo='EGRESO'
        AND categoria='PAGO_DEUDA'
        AND fecha BETWEEN %s AND %s
    ", $ini, $fin);


    $compras = DB::queryFirstField("
        SELECT IFNULL(SUM(total_compra),0)

        FROM compra

        WHERE fecha_creacion BETWEEN %s AND %s

        AND borrado_el IS NULL
    ", $ini, $fin);


    $facturado_eg = DB::queryFirstField("
        SELECT IFNULL(SUM(monto),0)

        FROM caja_chica_movimiento

        WHERE tipo='EGRESO'
        AND categoria='FACTURADO'
        AND fecha BETWEEN %s AND %s
    ", $ini, $fin);


    /* ============================================
     * DETALLE INGRESOS POR CATEGORY
     * ============================================ */

    $detalle_ingresos = DB::query("

        SELECT

            c.id,
            c.name,

            c.porcentaje_socio,
            c.porcentaje_propietario,

            SUM(d.amount * d.price_item) AS total_venta,

            SUM(

                CASE 

                    WHEN c.participa_reparto = 1

                    THEN (d.amount * d.price_item)
                         * (c.porcentaje_propietario / 100)

                    ELSE (d.amount * d.price_item)

                END

            ) AS ingreso_empresa,

            SUM(

                CASE 

                    WHEN c.participa_reparto = 1

                    THEN (d.amount * d.price_item)
                         * (c.porcentaje_socio / 100)

                    ELSE 0

                END

            ) AS ingreso_socio

        FROM product_order_detail d

        INNER JOIN product_order o 
            ON o.product_order_id = d.order_id

        INNER JOIN product p
            ON p.product_id = d.product_id

        INNER JOIN product_category pc 
            ON pc.product_id = d.product_id

        INNER JOIN category c 
            ON c.id = pc.category_id

        WHERE o.fecha_creacion BETWEEN %s AND %s

        AND o.borrado_el IS NULL
        AND p.borrado_el IS NULL
        AND c.borrado_el IS NULL

        GROUP BY c.id

        ORDER BY c.name ASC

    ", $ini, $fin);


    /* ============================================
     * DETALLE EGRESOS POR CATEGORY
     * ============================================ */

    $detalle_egresos = DB::query("

        SELECT

            c.id,
            c.name,

            c.porcentaje_socio,
            c.porcentaje_propietario,

            SUM(g.monto) AS total_gasto,

            SUM(
                g.monto * (c.porcentaje_propietario / 100)
            ) AS gasto_empresa,

            SUM(
                g.monto * (c.porcentaje_socio / 100)
            ) AS gasto_socio

        FROM pos_gasto_rubro g

        INNER JOIN category c 
            ON c.id = g.rubro_category_id

        WHERE g.fecha BETWEEN %s AND %s

        AND c.borrado_el IS NULL

        GROUP BY c.id

        ORDER BY c.name ASC

    ", $ini, $fin);


    /* ============================================
     * UTILIDAD POR CATEGORY
     * ============================================ */

    $detalle_utilidad = DB::query("

        SELECT

            c.id,
            c.name,

            c.porcentaje_socio,
            c.porcentaje_propietario,

            IFNULL(ing.total_venta,0) AS total_venta,

            IFNULL(

                CASE 

                    WHEN c.participa_reparto = 1

                    THEN ing.total_venta
                         * (c.porcentaje_propietario / 100)

                    ELSE ing.total_venta

                END

            ,0) AS ingreso_empresa,

            IFNULL(

                CASE 

                    WHEN c.participa_reparto = 1

                    THEN ing.total_venta
                         * (c.porcentaje_socio / 100)

                    ELSE 0

                END

            ,0) AS ingreso_socio,

            IFNULL(gas.total_gasto,0) AS total_gasto,

            IFNULL(
                gas.total_gasto * (c.porcentaje_propietario / 100)
            ,0) AS gasto_empresa,

            IFNULL(
                gas.total_gasto * (c.porcentaje_socio / 100)
            ,0) AS gasto_socio,

            (

                IFNULL(

                    CASE 

                        WHEN c.participa_reparto = 1

                        THEN ing.total_venta
                             * (c.porcentaje_propietario / 100)

                        ELSE ing.total_venta

                    END

                ,0)

                -

                IFNULL(
                    gas.total_gasto
                    * (c.porcentaje_propietario / 100)
                ,0)

            ) AS utilidad_empresa,

            (

                IFNULL(

                    CASE 

                        WHEN c.participa_reparto = 1

                        THEN ing.total_venta
                             * (c.porcentaje_socio / 100)

                        ELSE 0

                    END

                ,0)

                -

                IFNULL(
                    gas.total_gasto
                    * (c.porcentaje_socio / 100)
                ,0)

            ) AS utilidad_socio

        FROM category c

        LEFT JOIN (

            SELECT

                pc.category_id,

                SUM(
                    d.amount * d.price_item
                ) AS total_venta

            FROM product_order_detail d

            INNER JOIN product_order o 
                ON o.product_order_id = d.order_id

            INNER JOIN product p
                ON p.product_id = d.product_id

            INNER JOIN product_category pc 
                ON pc.product_id = d.product_id

            WHERE o.fecha_creacion BETWEEN %s AND %s

            AND o.borrado_el IS NULL
            AND p.borrado_el IS NULL

            GROUP BY pc.category_id

        ) ing ON ing.category_id = c.id

        LEFT JOIN (

            SELECT

                rubro_category_id,

                SUM(monto) AS total_gasto

            FROM pos_gasto_rubro

            WHERE fecha BETWEEN %s AND %s

            GROUP BY rubro_category_id

        ) gas ON gas.rubro_category_id = c.id

        WHERE c.participa_reparto = 1

        AND c.borrado_el IS NULL

        ORDER BY c.name ASC

    ", $ini, $fin, $ini, $fin);


    /* ============================================
     * TOTALES
     * ============================================ */

    $total_ingresos = 
        $ventas
        + $caja_ing
        + $facturado_ing;


    $total_egresos  = 
        $caja_eg
        + $compras
        + $facturado_eg
        + $pago_deuda;


    $utilidad = 
        $total_ingresos
        - $total_egresos;


    /* ============================================
     * RESPUESTA FINAL
     * ============================================ */

    Flight::json([

        // RESUMEN
        'ventas'          => floatval($ventas),
        'caja_ing'        => floatval($caja_ing),
        'facturado_ing'   => floatval($facturado_ing),

        'caja_eg'         => floatval($caja_eg),
        'pago_deuda'      => floatval($pago_deuda),
        'compras'         => floatval($compras),
        'facturado_eg'    => floatval($facturado_eg),

        'total_ingresos'  => floatval($total_ingresos),
        'total_egresos'   => floatval($total_egresos),
        'utilidad'        => floatval($utilidad),

        // DETALLE
        'detalle_ingresos' => $detalle_ingresos,
        'detalle_egresos'  => $detalle_egresos,
        'detalle_utilidad' => $detalle_utilidad

    ]);

});