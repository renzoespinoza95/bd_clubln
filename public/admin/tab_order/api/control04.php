<?php
/* 1. Administradores */
Flight::route('GET /administrador/listar', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();

    DB::query("SET NAMES 'utf8mb4'");
    $rows = DB::query("
        SELECT administrador_id, nombres_apellidos
        FROM administradortbl
        WHERE is_activo = 1
        ORDER BY nombres_apellidos ASC
    ");
    Flight::json($rows);
});

/* 2. Liberar Mesa Ocupada Validando Pedidos Pendientes */
Flight::route('POST /ventas/liberarMesaOcupada', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();

    $d = Flight::request()->data;
    $mesa_id = (int)($d['mesa_id'] ?? 0);

    if (!$mesa_id) {
        Flight::json(['status' => 'error', 'msg' => 'Mesa inválida'], 400);
        return;
    }

    $pendientes = DB::queryFirstField("
        SELECT COUNT(*)
        FROM product_order
        WHERE mesa_id = %i
          AND modo_order_id = 2
          AND DATE(fecha_creacion) <= CURDATE()
    ", $mesa_id);

    if ($pendientes > 0) {
        Flight::json(['status' => 'error', 'msg' => 'La mesa tiene pedidos pendientes'], 409);
        return;
    }

    DB::startTransaction();
    try {
        DB::update('mesa', ['estado' => 'DISPONIBLE'], "mesa_id=%i", $mesa_id);
        DB::commit();
        Flight::json(['status' => 'ok']);
    } catch (Exception $e) {
        DB::rollback();
        Flight::json(['status' => 'error', 'msg' => $e->getMessage()], 500);
    }
});

/* 3. Reporte PDF Ventas por Fecha */
Flight::route('GET /imp_ventas_fecha', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();

    global $wkh_pdf, $varpath_tmp, $varhost_tmp, $varhost;

    $req = Flight::request();
    $ini = trim($req->query->ini ?? $_GET['ini'] ?? '');
    $fin = trim($req->query->fin ?? $_GET['fin'] ?? '');

    if ($ini === '' || $fin === '') {
        Flight::halt(400, 'Debe enviar las fechas ini y fin');
    }

    DB::query("SET NAMES 'utf8mb4'");

    $ventas = DB::query("
        SELECT 
            po.product_order_id,
            po.fecha_creacion,
            po.total_fees,
            cl.nombre AS cliente,
            a.nombres_apellidos AS administrador
        FROM product_order po
        LEFT JOIN cliente cl ON cl.cliente_id = po.cliente_id
        LEFT JOIN administradortbl a ON a.administrador_id = po.administrador_id
        WHERE po.borrado_el IS NULL
          AND po.fecha_creacion BETWEEN %s AND %s
        ORDER BY po.product_order_id ASC
    ", $ini, $fin);

    $listado = [];
    $total_general = 0;
    $i = 1;
    $total_costo_general = 0;

    foreach ($ventas as &$v) {
        $detalles = DB::query("
            SELECT 
                d.product_name AS producto,
                d.amount AS cantidad,
                d.price_item AS precio,
                (d.amount * d.price_item) AS subtotal,
                IFNULL(
                    (
                        SELECT im.precio_unitario
                        FROM inventario_movimiento im
                        WHERE im.product_id = d.product_id
                          AND im.tipo = 'ENTRADA'
                          AND im.origen = 'COMPRA'
                          AND im.fecha <= %s
                        ORDER BY im.inventario_movimiento_id DESC
                        LIMIT 1
                    ),
                    IFNULL(d.costo_unitario, 0.00)
                ) AS costo_unitario
            FROM product_order_detail d
            WHERE d.order_id = %i
              AND d.borrado_el IS NULL
        ", $v['fecha_creacion'], $v['product_order_id']);

        foreach ($detalles as &$d) {
            $d['costo_unitario'] = floatval($d['costo_unitario']);
            $d['precio']         = floatval($d['precio']);
            $d['cantidad']       = intval($d['cantidad']);
            $d['subtotal']       = $d['cantidad'] * $d['precio'];
            $d['total_costo']    = $d['cantidad'] * $d['costo_unitario'];
            $d['subtotal_costo'] = $d['total_costo'];
            $total_costo_general += $d['subtotal_costo'];
        }

        $v['indice']   = $i++;
        $v['detalles'] = $detalles;
        $total_general += $v['total_fees'];
        $listado[]     = $v;
    }

    $ini_fmt = date('d/m/Y', strtotime($ini));
    $fin_fmt = date('d/m/Y', strtotime($fin));

    $template_data = [
        'informacion' => [[
            'razon_social'        => 'CLUB SOCIAL LIMA NORTE S.A.C',
            'ruc'                 => vari('RUC'),
            'logo'                => $varhost . '/public/admin/login/images/logo_login.png',
            'titulo_reporte'      => "RESUMEN DE VENTAS DEL $ini_fmt AL $fin_fmt",
            'fecha'               => date('d/m/Y H:i'),
            'total_items'         => count($ventas),
            'total_costo_general' => number_format($total_costo_general, 2),
            'ganancia'            => number_format($total_general - $total_costo_general, 2),
            'total_general'       => number_format($total_general, 2)
        ]],
        'listado'     => $listado
    ];

    $html = (new Mustache)->render(
        file_get_contents(VARPATH . '/public/reportes/reporte_html/imp_ventas_fecha.html'),
        $template_data
    );

    $pdf = $varpath_tmp . 'ventas_' . time() . '.pdf';
    $wkh_pdf->addPage($html);
    exec($wkh_pdf->getCommand($pdf));

    Flight::redirect($varhost_tmp . basename($pdf));
});

/* 4. Reporte Excel Ventas por Fecha */
Flight::route('GET /imp_ventas_fecha_excel', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();

    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=ventas.xls");

    $ini = trim($_GET['ini'] ?? '');
    $fin = trim($_GET['fin'] ?? '');

    if ($ini === '' || $fin === '') {
        Flight::halt(400, 'Debe enviar las fechas ini y fin');
    }

    DB::query("SET NAMES 'utf8mb4'");

    $ventas = DB::query("
        SELECT 
            po.product_order_id,
            po.fecha_creacion,
            c.nombre AS cliente,
            a.nombres_apellidos AS administrador
        FROM product_order po
        LEFT JOIN cliente c ON c.cliente_id = po.cliente_id
        LEFT JOIN administradortbl a ON a.administrador_id = po.administrador_id
        WHERE po.fecha_creacion BETWEEN %s AND %s
        ORDER BY po.product_order_id
    ", $ini . ' 00:00:00', $fin . ' 23:59:59');

    $total_general = 0;

    echo "<table border='1'>";
    echo "<tr><th colspan='6'>REPORTE DE VENTAS DEL $ini AL $fin</th></tr>";
    echo "<tr><th>ID</th><th>Cliente</th><th>Administrador</th><th>Producto</th><th>Cantidad</th><th>Subtotal</th></tr>";

    foreach ($ventas as $v) {
        $detalles = DB::query("
            SELECT d.product_name, d.amount, d.price_item, (d.amount * d.price_item) AS subtotal
            FROM product_order_detail d
            WHERE d.order_id = %i
        ", $v['product_order_id']);

        foreach ($detalles as $d) {
            $subtotal = (float)$d['subtotal'];
            $total_general += $subtotal;
            echo "<tr>
                <td>{$v['product_order_id']}</td>
                <td>{$v['cliente']}</td>
                <td>{$v['administrador']}</td>
                <td>{$d['product_name']}</td>
                <td>{$d['amount']}</td>
                <td>" . number_format($subtotal, 2) . "</td>
            </tr>";
        }
    }

    echo "<tr>
            <td colspan='5'><strong>TOTAL GENERAL</strong></td>
            <td><strong>" . number_format($total_general, 2) . "</strong></td>
          </tr>";
    echo "</table>";
});

/* 5. Resumen Ventas Categoría PDF */
Flight::route('GET /imp_resumen_categoria', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();

    global $wkh_pdf, $varpath_tmp, $varhost_tmp, $varhost;

    $req = Flight::request();
    $ini = trim($req->query->ini ?? $_GET['ini'] ?? '');
    $fin = trim($req->query->fin ?? $_GET['fin'] ?? '');

    if ($ini === '' || $fin === '') {
        Flight::halt(400, 'Debe enviar las fechas ini y fin');
    }

    DB::query("SET NAMES 'utf8mb4'");

    $rows = DB::query("
        SELECT 
            DATE(po.fecha_creacion) AS fecha,
            p.name AS producto,
            SUM(d.amount) AS cantidad,
            d.costo_unitario,
            d.price_item
        FROM product_order_detail d
        INNER JOIN product_order po ON po.product_order_id = d.order_id
        INNER JOIN product p ON p.product_id = d.product_id
        WHERE po.fecha_creacion BETWEEN %s AND %s
        GROUP BY fecha, d.product_id, d.costo_unitario, d.price_item
        ORDER BY fecha ASC
    ", $ini . ' 00:00:00', $fin . ' 23:59:59');

    $dias = [];
    $total_compras_general = 0;
    $total_ventas_general = 0;

    foreach ($rows as $r) {
        $fecha = $r['fecha'];
        if (!isset($dias[$fecha])) {
            $dias[$fecha] = [
                'fecha'         => date('d/m/Y', strtotime($fecha)),
                'items'         => [],
                'total_compras' => 0,
                'total_ventas'  => 0
            ];
        }

        $cantidad       = (int)$r['cantidad'];
        $costo_unitario = (float)$r['costo_unitario'];
        $precio         = (float)$r['price_item'];

        $total_costo = $cantidad * $costo_unitario;
        $total_venta = $cantidad * $precio;

        $dias[$fecha]['items'][] = [
            'producto'       => $r['producto'],
            'cantidad'       => $cantidad,
            'costo_unitario' => number_format($costo_unitario, 2),
            'precio'         => number_format($precio, 2),
            'total_costo'    => number_format($total_costo, 2),
            'total_venta'    => number_format($total_venta, 2)
        ];

        $dias[$fecha]['total_compras'] += $total_costo;
        $dias[$fecha]['total_ventas']  += $total_venta;

        $total_compras_general += $total_costo;
        $total_ventas_general  += $total_venta;
    }

    $dias = array_map(function ($d) {
        $ganancia = $d['total_ventas'] - $d['total_compras'];
        return [
            'fecha'         => $d['fecha'],
            'items'         => $d['items'],
            'total_compras' => number_format($d['total_compras'], 2),
            'total_ventas'  => number_format($d['total_ventas'], 2),
            'ganancia'      => number_format($ganancia, 2)
        ];
    }, array_values($dias));

    $ini_fmt = date('d/m/Y', strtotime($ini));
    $fin_fmt = date('d/m/Y', strtotime($fin));

    $template_data = [
        'informacion' => [[
            'razon_social'   => 'CLUB SOCIAL LIMA NORTE S.A.C',
            'ruc'            => vari('RUC'),
            'logo'           => $varhost . '/public/admin/login/images/logo_login.png',
            'titulo_reporte' => "RESUMEN DE VENTAS DEL $ini_fmt AL $fin_fmt",
            'fecha'          => date('d/m/Y H:i'),
            'total_items'    => count($dias)
        ]],
        'dias'                  => $dias,
        'total_compras_general' => number_format($total_compras_general, 2),
        'total_ventas_general'  => number_format($total_ventas_general, 2),
        'total_ganancia_general'=> number_format($total_ventas_general - $total_compras_general, 2)
    ];

    $html = (new Mustache)->render(
        file_get_contents(VARPATH . '/public/reportes/reporte_html/imp_resumen_categoria.html'),
        $template_data
    );

    $pdf = $varpath_tmp . 'resumen_categoria_' . time() . '.pdf';
    $wkh_pdf->addPage($html);
    exec($wkh_pdf->getCommand($pdf));

    Flight::redirect($varhost_tmp . basename($pdf));
});