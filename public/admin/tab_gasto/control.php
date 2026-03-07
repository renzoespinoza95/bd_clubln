<?php
// este es mi backend usando php8.1, flightphp y meekrodb2

/* =========================================================
 *  INICIO MODULO
 * ========================================================= */
Flight::route('GET /reg/pos_rubro/inicio', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $path_public;
    include $path_public . '/admin/tab_gasto/inicio.php';
});


/* =========================================================
 *  HELPERS
 * ========================================================= */
function reg_json_ok($data = [])
{
    Flight::json(array_merge(['status' => 'ok'], $data));
}

function reg_json_error($msg, $code = 400)
{
    Flight::json(['status' => 'error', 'msg' => $msg], $code);
}

function reg_administrador_actual_id()
{
    include DEFINITION;

    global $sesion_admin_administrador_id, $nombre_app;

    if (empty($sesion_admin_administrador_id)) {
        return 0;
    }

    $valor_key = $nombre_app . vari("KEY");

    $administrador_id = (int) str_replace(
        "*",
        "",
        util::decrypt($sesion_admin_administrador_id, $valor_key)
    );

    return $administrador_id;
}

function reg_now_ms()
{
    return time() * 1000;
}


/* =========================================================
 *  CATEGORY
 * ========================================================= */

/* GET /reg/category/listar */
Flight::route('GET /reg/category/listar', function () {
    DB::query("SET NAMES 'utf8mb4'");

    $rows = DB::query("
        SELECT
            id,
            name,
            icon,
            draft,
            brief,
            color,
            priority,
            created_at,
            last_update,
            participa_reparto,
            porcentaje_socio,
            porcentaje_propietario,
            is_activo
        FROM category
        ORDER BY id DESC
    ");

    Flight::json($rows);
});

/* GET /reg/category/activas */
Flight::route('GET /reg/category/activas', function () {
    DB::query("SET NAMES 'utf8mb4'");

    $rows = DB::query("
        SELECT
            id,
            name,
            color,
            priority,
            participa_reparto,
            porcentaje_socio,
            porcentaje_propietario
        FROM category
        WHERE is_activo = 1
        ORDER BY priority ASC, name ASC
    ");

    Flight::json($rows);
});

/* POST /reg/category/crear */
Flight::route('POST /reg/category/crear', function () {
    $data = Flight::request()->data->getData();

    $name = trim($data['name'] ?? '');
    $color = trim($data['color'] ?? '#000000');
    $priority = isset($data['priority']) ? (int)$data['priority'] : 0;
    $draft = isset($data['draft']) ? (int)$data['draft'] : 0;

    // nuevos campos de negocio
    $participa_reparto = !empty($data['participa_reparto']) ? 1 : 0;
    $porcentaje_socio = isset($data['porcentaje_socio']) ? (float)$data['porcentaje_socio'] : 0;
    $porcentaje_propietario = isset($data['porcentaje_propietario']) ? (float)$data['porcentaje_propietario'] : 0;
    $is_activo = isset($data['is_activo']) ? (int)$data['is_activo'] : 1;

    if ($name === '') {
        reg_json_error('Debe ingresar el nombre');
        return;
    }

    $existe = DB::queryFirstField(
        "SELECT COUNT(*) FROM category WHERE name=%s",
        $name
    );

    if ($existe > 0) {
        reg_json_error('Ya existe una categoría con ese nombre');
        return;
    }

    if (!$participa_reparto) {
        $porcentaje_socio = 0;
        $porcentaje_propietario = 0;
    }

    DB::insert('category', [
        'name' => $name,
        'icon' => trim($data['icon'] ?? ''),
        'draft' => $draft,
        'brief' => trim($data['brief'] ?? ''),
        'color' => $color,
        'priority' => $priority,
        'created_at' => reg_now_ms(),
        'last_update' => reg_now_ms(),
        'participa_reparto' => $participa_reparto,
        'porcentaje_socio' => $porcentaje_socio,
        'porcentaje_propietario' => $porcentaje_propietario,
        'is_activo' => $is_activo
    ]);

    reg_json_ok(['id' => DB::insertId()]);
});

/* POST /reg/category/editar */
Flight::route('POST /reg/category/editar', function () {
    $data = Flight::request()->data->getData();

    $id = (int)($data['id'] ?? 0);
    $name = trim($data['name'] ?? '');
    $color = trim($data['color'] ?? '#000000');
    $priority = isset($data['priority']) ? (int)$data['priority'] : 0;
    $draft = isset($data['draft']) ? (int)$data['draft'] : 0;

    $participa_reparto = !empty($data['participa_reparto']) ? 1 : 0;
    $porcentaje_socio = isset($data['porcentaje_socio']) ? (float)$data['porcentaje_socio'] : 0;
    $porcentaje_propietario = isset($data['porcentaje_propietario']) ? (float)$data['porcentaje_propietario'] : 0;
    $is_activo = isset($data['is_activo']) ? (int)$data['is_activo'] : 1;

    if ($id <= 0) {
        reg_json_error('Categoría inválida');
        return;
    }

    if ($name === '') {
        reg_json_error('Debe ingresar el nombre');
        return;
    }

    $existe = DB::queryFirstField(
        "SELECT COUNT(*) FROM category WHERE name=%s AND id<>%i",
        $name,
        $id
    );

    if ($existe > 0) {
        reg_json_error('Ya existe otra categoría con ese nombre');
        return;
    }

    if (!$participa_reparto) {
        $porcentaje_socio = 0;
        $porcentaje_propietario = 0;
    }

    DB::update('category', [
        'name' => $name,
        'icon' => trim($data['icon'] ?? ''),
        'draft' => $draft,
        'brief' => trim($data['brief'] ?? ''),
        'color' => $color,
        'priority' => $priority,
        'last_update' => reg_now_ms(),
        'participa_reparto' => $participa_reparto,
        'porcentaje_socio' => $porcentaje_socio,
        'porcentaje_propietario' => $porcentaje_propietario,
        'is_activo' => $is_activo
    ], "id=%i", $id);

    reg_json_ok();
});

/* POST /reg/category/eliminar */
Flight::route('POST /reg/category/eliminar', function () {
    $data = Flight::request()->data->getData();
    $id = (int)($data['id'] ?? 0);

    if ($id <= 0) {
        reg_json_error('Categoría inválida');
        return;
    }

    $usa_product_category = DB::queryFirstField(
        "SELECT COUNT(*) FROM product_category WHERE category_id=%i",
        $id
    );

    if ($usa_product_category > 0) {
        reg_json_error('No se puede eliminar porque está relacionada a productos');
        return;
    }

    $usa_gasto_rubro = DB::queryFirstField(
        "SELECT COUNT(*) FROM pos_gasto_rubro WHERE rubro_category_id=%i OR tipo_costo_category_id=%i",
        $id,
        $id
    );

    if ($usa_gasto_rubro > 0) {
        reg_json_error('No se puede eliminar porque está relacionada a gastos');
        return;
    }

    $usa_liquidacion = DB::queryFirstField(
        "SELECT COUNT(*) FROM pos_rubro_liquidacion WHERE rubro_category_id=%i",
        $id
    );

    if ($usa_liquidacion > 0) {
        reg_json_error('No se puede eliminar porque está relacionada a liquidaciones');
        return;
    }

    DB::delete('category', "id=%i", $id);

    reg_json_ok();
});


/* =========================================================
 *  GASTOS POR CATEGORIA
 * ========================================================= */

/* GET /reg/pos_gasto_rubro/listar */
Flight::route('GET /reg/pos_gasto_rubro/listar', function () {
    DB::query("SET NAMES 'utf8mb4'");

    $rows = DB::query("
        SELECT
            g.gasto_rubro_id,
            g.rubro_category_id,
            c1.name AS category_nombre,

            g.tipo_costo_category_id,
            c2.name AS tipo_costo_nombre,

            g.fecha,
            g.fecha_hora,
            g.concepto,
            g.descripcion,
            g.monto,
            g.administrador_id,
            g.compra_id,
            g.observaciones
        FROM pos_gasto_rubro g
        LEFT JOIN category c1 ON c1.id = g.rubro_category_id
        LEFT JOIN category c2 ON c2.id = g.tipo_costo_category_id
        ORDER BY g.gasto_rubro_id DESC
    ");

    Flight::json($rows);
});

/* POST /reg/pos_gasto_rubro/crear */
Flight::route('POST /reg/pos_gasto_rubro/crear', function () {
    $data = Flight::request()->data->getData();

    // compatibilidad: acepta category_id o rubro_category_id
    $rubro_category_id = (int)($data['rubro_category_id'] ?? ($data['category_id'] ?? 0));
    $tipo_costo_category_id = !empty($data['tipo_costo_category_id']) ? (int)$data['tipo_costo_category_id'] : null;

    $fecha = trim($data['fecha'] ?? '');
    $concepto = trim($data['concepto'] ?? '');
    $descripcion = trim($data['descripcion'] ?? '');
    $monto = isset($data['monto']) ? (float)$data['monto'] : 0;
    $compra_id = !empty($data['compra_id']) ? (int)$data['compra_id'] : null;
    $observaciones = trim($data['observaciones'] ?? '');

    $administrador_id = reg_administrador_actual_id();

    if ($rubro_category_id <= 0) {
        reg_json_error('Debe seleccionar una categoría');
        return;
    }

    if ($fecha === '') {
        reg_json_error('Debe seleccionar la fecha');
        return;
    }

    if ($concepto === '') {
        reg_json_error('Debe ingresar el concepto');
        return;
    }

    if ($monto <= 0) {
        reg_json_error('El monto debe ser mayor a 0');
        return;
    }

    DB::insert('pos_gasto_rubro', [
        'rubro_category_id' => $rubro_category_id,
        'tipo_costo_category_id' => $tipo_costo_category_id,
        'fecha' => $fecha,
        'fecha_hora' => date('Y-m-d H:i:s'),
        'concepto' => $concepto,
        'descripcion' => $descripcion,
        'monto' => $monto,
        'administrador_id' => $administrador_id ?: null,
        'compra_id' => $compra_id,
        'observaciones' => $observaciones
    ]);

    reg_json_ok(['gasto_rubro_id' => DB::insertId()]);
});

/* POST /reg/pos_gasto_rubro/editar */
Flight::route('POST /reg/pos_gasto_rubro/editar', function () {
    $data = Flight::request()->data->getData();

    $gasto_rubro_id = (int)($data['gasto_rubro_id'] ?? 0);
    $rubro_category_id = (int)($data['rubro_category_id'] ?? ($data['category_id'] ?? 0));
    $tipo_costo_category_id = !empty($data['tipo_costo_category_id']) ? (int)$data['tipo_costo_category_id'] : null;

    $fecha = trim($data['fecha'] ?? '');
    $concepto = trim($data['concepto'] ?? '');
    $descripcion = trim($data['descripcion'] ?? '');
    $monto = isset($data['monto']) ? (float)$data['monto'] : 0;
    $compra_id = !empty($data['compra_id']) ? (int)$data['compra_id'] : null;
    $observaciones = trim($data['observaciones'] ?? '');

    if ($gasto_rubro_id <= 0) {
        reg_json_error('Gasto inválido');
        return;
    }

    if ($rubro_category_id <= 0) {
        reg_json_error('Debe seleccionar una categoría');
        return;
    }

    if ($fecha === '') {
        reg_json_error('Debe seleccionar la fecha');
        return;
    }

    if ($concepto === '') {
        reg_json_error('Debe ingresar el concepto');
        return;
    }

    if ($monto <= 0) {
        reg_json_error('El monto debe ser mayor a 0');
        return;
    }

    DB::update('pos_gasto_rubro', [
        'rubro_category_id' => $rubro_category_id,
        'tipo_costo_category_id' => $tipo_costo_category_id,
        'fecha' => $fecha,
        'concepto' => $concepto,
        'descripcion' => $descripcion,
        'monto' => $monto,
        'compra_id' => $compra_id,
        'observaciones' => $observaciones
    ], "gasto_rubro_id=%i", $gasto_rubro_id);

    reg_json_ok();
});

/* POST /reg/pos_gasto_rubro/eliminar */
Flight::route('POST /reg/pos_gasto_rubro/eliminar', function () {
    $data = Flight::request()->data->getData();
    $gasto_rubro_id = (int)($data['gasto_rubro_id'] ?? 0);

    if ($gasto_rubro_id <= 0) {
        reg_json_error('Gasto inválido');
        return;
    }

    DB::delete('pos_gasto_rubro', "gasto_rubro_id=%i", $gasto_rubro_id);

    reg_json_ok();
});


/* =========================================================
 *  REPORTE PDF UTILIDAD
 * ========================================================= */

/* GET /reg/reportes/utilidad */
Flight::route('GET /reg/reportes/utilidad', function () {

    include DEFINITION;
    login_admin::autentificar_administrador();

    global $wkh_pdf, $varhost;

    $req = Flight::request();
    $ini = trim($req->query->ini ?? $_GET['ini'] ?? date('Y-m-d'));
    $fin = trim($req->query->fin ?? $_GET['fin'] ?? date('Y-m-d'));

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $ini) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fin)) {
        Flight::halt(400, 'Formato de fecha inválido');
    }

    DB::query("SET NAMES 'utf8mb4'");

    // INGRESOS POR CATEGORY
    $ingresos = DB::query("
        SELECT
            c.id,
            c.name,
            c.participa_reparto,
            c.porcentaje_socio,
            c.porcentaje_propietario,
            SUM(d.amount * d.price_item) AS subtotal
        FROM product_order_detail d
        INNER JOIN product_order o
            ON o.product_order_id = d.order_id
        INNER JOIN category c
            ON c.id = d.rubro_id
        WHERE DATE(o.fecha_creacion) BETWEEN %s AND %s
        GROUP BY c.id, c.name, c.participa_reparto, c.porcentaje_socio, c.porcentaje_propietario
        ORDER BY c.name ASC
    ", $ini, $fin);

    $total_general = 0;
    $filas_ingresos = '';

    $rubro_reparto = null;
    $ingreso_reparto = 0;

    foreach ($ingresos as $r) {
        $subtotal = (float)$r['subtotal'];
        $total_general += $subtotal;

        if ((int)$r['participa_reparto'] === 1 && $rubro_reparto === null) {
            $rubro_reparto = $r;
            $ingreso_reparto = $subtotal;
        }

        $filas_ingresos .= '
            <tr>
              <td style="padding:6px 10px;">'.htmlspecialchars($r['name']).'</td>
              <td style="padding:6px 10px; text-align:right;">S/ '.number_format($subtotal, 2).'</td>
            </tr>';
    }

    $costo_reparto = 0;
    $utilidad_neta = 0;
    $monto_socio = 0;
    $monto_propietario = 0;
    $porcentaje_socio = 0;
    $porcentaje_propietario = 0;
    $nombre_rubro_reparto = 'No configurado';

    if ($rubro_reparto) {
        $nombre_rubro_reparto = $rubro_reparto['name'];
        $porcentaje_socio = (float)$rubro_reparto['porcentaje_socio'];
        $porcentaje_propietario = (float)$rubro_reparto['porcentaje_propietario'];

        $costo_reparto = (float) DB::queryFirstField("
            SELECT IFNULL(SUM(monto), 0)
            FROM pos_gasto_rubro
            WHERE rubro_category_id = %i
              AND fecha BETWEEN %s AND %s
        ", $rubro_reparto['id'], $ini, $fin);

        $utilidad_neta = $ingreso_reparto - $costo_reparto;
        $monto_socio = $utilidad_neta * ($porcentaje_socio / 100);
        $monto_propietario = $utilidad_neta * ($porcentaje_propietario / 100);
    }

    $fini = date('d/m/Y', strtotime($ini));
    $ffin = date('d/m/Y', strtotime($fin));

    $html = '
    <html>
    <head>
      <meta charset="utf-8">
      <style>
        body{font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#222;}
        .box{border:1px solid #222;width:700px;margin:0 auto;}
        .head{padding:10px 15px;border-bottom:1px solid #222;}
        .section-title{font-weight:bold;border-top:1px solid #222;border-bottom:1px solid #222;padding:8px 15px;background:#f3f3f3;}
        table{width:100%;border-collapse:collapse;}
        .total-row td{font-weight:bold;border-top:1px solid #222;}
      </style>
    </head>
    <body>
      <div class="box">
        <div class="head">
          <div><strong>Cierre Diario</strong></div>
          <div>Fecha inicio: '.$fini.'</div>
          <div>Fecha final : '.$ffin.'</div>
        </div>

        <div class="section-title">INGRESOS POR CATEGORÍA</div>
        <table>
          '.$filas_ingresos.'
          <tr class="total-row">
            <td style="padding:6px 10px;">TOTAL GENERAL</td>
            <td style="padding:6px 10px; text-align:right;">S/ '.number_format($total_general, 2).'</td>
          </tr>
        </table>

        <div class="section-title">UTILIDAD '.htmlspecialchars(strtoupper($nombre_rubro_reparto)).'</div>
        <table>
          <tr>
            <td style="padding:6px 10px;">Ingresos</td>
            <td style="padding:6px 10px; text-align:right;">S/ '.number_format($ingreso_reparto, 2).'</td>
          </tr>
          <tr>
            <td style="padding:6px 10px;">Costos</td>
            <td style="padding:6px 10px; text-align:right;">S/ '.number_format($costo_reparto, 2).'</td>
          </tr>
          <tr class="total-row">
            <td style="padding:6px 10px;">UTILIDAD NETA</td>
            <td style="padding:6px 10px; text-align:right;">S/ '.number_format($utilidad_neta, 2).'</td>
          </tr>
          <tr>
            <td style="padding:6px 10px;">Socio '.number_format($porcentaje_socio,2).'%</td>
            <td style="padding:6px 10px; text-align:right;">S/ '.number_format($monto_socio, 2).'</td>
          </tr>
          <tr>
            <td style="padding:6px 10px;">Propietario '.number_format($porcentaje_propietario,2).'%</td>
            <td style="padding:6px 10px; text-align:right;">S/ '.number_format($monto_propietario, 2).'</td>
          </tr>
        </table>
      </div>
    </body>
    </html>';

    $pdf = VARPATH . '/public/reportes/archivos_temporales/utilidad_' . time() . '.pdf';

    $wkh_pdf->addPage($html);
    exec($wkh_pdf->getCommand($pdf));

    Flight::redirect(
        $varhost . '/public/reportes/archivos_temporales/' . basename($pdf)
    );
});

Flight::route('GET /reg/pos_tipo_costo_category/listar', function(){

    $rows = DB::query("
        SELECT
        *
        FROM pos_tipo_costo_category
        WHERE is_activo = 1
        ORDER BY orden
        ");

    Flight::json([
        'status' => 'ok',
        'data' => $rows
    ]);

});


Flight::route('POST /reg/pos_gasto_rubro/editar', function(){

    $p = Flight::request()->data;

    DB::update('pos_gasto_rubro',[

        'rubro_category_id'=>$p['rubro_category_id'],
        'tipo_costo_category_id'=>$p['tipo_costo_category_id'],
        'fecha'=>$p['fecha'],
        'concepto'=>$p['concepto'],
        'monto'=>$p['monto']

    ],'gasto_rubro_id=%i',$p['gasto_rubro_id']);

    Flight::json(['status'=>'ok']);

});