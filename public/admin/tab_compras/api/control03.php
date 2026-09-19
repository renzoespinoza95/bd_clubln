<?php
/* 1. GET /inventario/movimientos/@producto_id */
Flight::route('GET /inventario/movimientos/@producto_id', function ($producto_id) {
    $rows = DB::query("
        SELECT * 
        FROM inventario_movimiento
        WHERE product_id=%i
        ORDER BY fecha DESC
    ", $producto_id);

    Flight::json($rows);
});

/* 2. GET /imp_compras_fecha (PDF) */
Flight::route('GET /imp_compras_fecha', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();

    global $wkh_pdf, $varpath_tmp, $varhost_tmp, $varhost;

    $request = Flight::request();
    $ini = trim($request->query->ini ?? $_GET['ini'] ?? '');
    $fin = trim($request->query->fin ?? $_GET['fin'] ?? '');

    if ($ini === '' || $fin === '') {
        Flight::halt(400, 'Debe enviar las fechas ini y fin');
    }

    $fini = util::fecha_barra($ini);
    $ffin = util::fecha_barra($fin);

    $rows = DB::query("
        SELECT 
            c.compra_id,
            p.nombre AS proveedor,
            CONCAT(
                LPAD(DAY(c.fecha_creacion), 2, '0'), '/',
                LPAD(MONTH(c.fecha_creacion), 2, '0'), '/',
                YEAR(c.fecha_creacion), ' ',
                LPAD(HOUR(c.fecha_creacion), 2, '0'), ':',
                LPAD(MINUTE(c.fecha_creacion), 2, '0')
            ) AS fecha_creacion,
            c.total_compra
        FROM compra c
        LEFT JOIN proveedor p ON p.proveedor_id = c.proveedor_id
        WHERE c.borrado_el IS NULL
        AND c.fecha_creacion BETWEEN %s AND %s
        ORDER BY c.fecha_creacion
    ", $ini . ' 00:00:00', $fin . ' 23:59:59');

    $template_data['informacion'] = [[
        'razon_social'   => 'CLUB SOCIAL LIMA NORTE S.A.C',
        'ruc'            => vari('RUC'),
        'titulo_reporte' => 'REPORTE DE COMPRAS DEL ' . $fini . ' AL ' . $ffin,
        'fecha'          => date('d/m/Y H:i'),
        'logo'           => $varhost . '/public/admin/login/images/logo_login.png',
        'total_items'    => count($rows)
    ]];

    $i = 1;
    foreach ($rows as $k => $r) {
        $rows[$k]['indice'] = $i++;
    }

    $template_data['listado'] = $rows;

    $total_general = 0;
    foreach ($rows as $r) {
        $total_general += $r['total_compra'];
    }

    $template_data['total_general'] = number_format($total_general, 2);

    $html = (new Mustache)->render(
        file_get_contents(VARPATH . '/public/reportes/reporte_html/imp_compras_fecha.html'),
        $template_data
    );

    $pdf = $varpath_tmp . 'compras_' . time() . '.pdf';
    $wkh_pdf->addPage($html);
    exec($wkh_pdf->getCommand($pdf));

    Flight::redirect($varhost_tmp . basename($pdf));
});

/* 3. GET /imp_compras_fecha_excel */
Flight::route('GET /imp_compras_fecha_excel', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=compras.xls");

    $request = Flight::request();
    $ini = trim($request->query->ini ?? $_GET['ini'] ?? '');
    $fin = trim($request->query->fin ?? $_GET['fin'] ?? '');

    if ($ini === '' || $fin === '') {
        Flight::halt(400, 'Debe enviar las fechas ini y fin');
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $ini) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fin)) {
        Flight::halt(400, 'Formato de fecha inválido');
    }

    $rows = DB::query("
        SELECT 
            c.compra_id,
            p.nombre,
            c.fecha_creacion,
            c.total_compra
        FROM compra c
        LEFT JOIN proveedor p ON p.proveedor_id = c.proveedor_id
        WHERE c.fecha_creacion BETWEEN %s AND %s
        ORDER BY c.fecha_creacion ASC
    ", $ini . ' 00:00:00', $fin . ' 23:59:59');

    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Proveedor</th><th>Fecha</th><th>Total</th></tr>";
    foreach ($rows as $r) {
        echo "<tr>
            <td>{$r['compra_id']}</td>
            <td>{$r['nombre']}</td>
            <td>{$r['fecha_creacion']}</td>
            <td>{$r['total_compra']}</td>
        </tr>";
    }
    echo "</table>";
});