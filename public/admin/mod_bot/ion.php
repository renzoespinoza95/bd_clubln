<?php
Flight::route('POST /app/login', function () {

    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        Flight::json(['success' => false]);
        return;
    }

    // 🔐 Validar administrador
    $admin = DB::queryFirstRow("
        SELECT administrador_id, nombres_apellidos
        FROM administradortbl
        WHERE email = %s
          AND clavel = %s
          AND is_activo = 1
        LIMIT 1
    ", $email, $password);

    if (!$admin) {
        Flight::json(['success' => false]);
        return;
    }

    // 📅 Fecha de hoy
    $hoy = date('Y-m-d');

    // 💰 Buscar caja del día
    $caja = DB::queryFirstRow("
        SELECT caja_id, estado
        FROM caja
        WHERE administrador_id = %i
          AND DATE(fecha_apertura) = %s
        ORDER BY caja_id DESC
        LIMIT 1
    ", $admin['administrador_id'], $hoy);

    if ($caja) {
        $estado_caja = $caja['estado']; // ABIERTA o CERRADA
        $caja_id     = $caja['caja_id'];
    } else {
        $estado_caja = 'CERRADA';
        $caja_id     = null;
    }

    // 🕒 Actualizar último acceso
    DB::update('administradortbl', [
        'fecha_ultimo_acceso' => date('Y-m-d H:i:s')
    ], 'administrador_id=%i', $admin['administrador_id']);

    // 📤 Respuesta final
    Flight::json([
        'success'             => true,
        'administrador_id'    => $admin['administrador_id'],
        'nombres_apellidos'   => $admin['nombres_apellidos'],
        'estado_caja'         => $estado_caja,
        'caja_id'             => $caja_id
    ]);
});


Flight::route('GET /api/info', function () {

    $version = Flight::request()->query['version'] ?? 0;

    Flight::json([
        'status' => 'success',
        'info' => [
            'active'   => true,
            'tax'      => 18,
            'currency' => 'PEN',
            'shipping' => ['JIMM', 'NENE', 'PLUSS'],
            'version'  => (int)$version
        ]
    ]);
});

// anterior: getListCategory
Flight::route('GET /api/category/list', function () {

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
            last_update
        FROM category
        WHERE draft = 0
        ORDER BY priority ASC, name
    ");

    Flight::json([
        'status'     => 'success',
        'categories' => $rows
    ]);
});

// antes: getListProduct
Flight::route('GET /api/product/list', function () {

    // ============================
    // 📄 Paginación
    // ============================
    $page  = max(1, (int)(Flight::request()->query['page'] ?? 1));
    $count = (int)(Flight::request()->query['count'] ?? 10);
    $offset = ($page - 1) * $count;

    // ============================
    // 🔎 Filtros
    // ============================
    $q           = Flight::request()->query['q'] ?? '';
    $category_id = Flight::request()->query['category_id'] ?? null;

    // ============================
    // 🧠 WHERE dinámico (IGUAL AL ANTIGUO)
    // ============================
    $where  = "p.draft = 0";
    $params = [];

    if ($q !== '') {
        $where .= " AND p.name LIKE %s";
        $params[] = '%' . $q . '%';
    }

    if ($category_id !== null && $category_id !== '') {
        $where .= " AND pc.category_id = %i";
        $params[] = (int)$category_id;
    }

    // ============================
    // 🔢 TOTAL
    // ============================
    $count_total = DB::queryFirstField("
        SELECT COUNT(DISTINCT p.product_id)
        FROM product p
        LEFT JOIN product_category pc ON pc.product_id = p.product_id
        WHERE $where
    ", ...$params);

    // ============================
    // 📦 LISTADO
    // ============================
    $params[] = $count;
    $params[] = $offset;

    $products = DB::query("
        SELECT DISTINCT
            p.product_id,
            p.name,
            p.image,
            p.price,
            p.price_discount,
            p.draft,
            p.status,
            p.created_at,
            p.last_update,
            p.fecha_creacion,
            p.fecha_modificacion
        FROM product p
        LEFT JOIN product_category pc ON pc.product_id = p.product_id
        WHERE $where
        ORDER BY p.product_id DESC
        LIMIT %i OFFSET %i
    ", ...$params);

    // ============================
    // 📤 RESPONSE (CLON DEL ANTIGUO)
    // ============================
    Flight::json([
        'status'       => 'success',
        'count'        => $count,
        'count_total'  => (int)$count_total,
        'pages'        => (int)ceil($count_total / $count),
        'products'     => $products
    ]);
});

// antes: getProductDetails
Flight::route('GET /api/product/detail/@id', function ($id) {

    // ============================
    // 📦 PRODUCTO + STOCK
    // ============================
    $product = DB::queryFirstRow("
        SELECT 
            p.*,
            IFNULL(i.stock_actual, 0) AS stock
        FROM product p
        LEFT JOIN inventario i 
            ON i.product_id = p.product_id
        WHERE p.product_id = %i
        LIMIT 1
    ", $id);

    if (!$product) {
        Flight::json([
            'status' => 'failed',
            'msg' => 'Product not found'
        ]);
        return;
    }

    // ============================
    // 🖼️ IMÁGENES (MISMO NOMBRE)
    // ============================
    $product_images = DB::query("
        SELECT product_id, name
        FROM product_image
        WHERE product_id = %i
    ", $id);

    // ============================
    // 🏷️ CATEGORÍAS (COMPLETAS)
    // ============================
    $categories = DB::query("
        SELECT 
            c.id,
            c.name,
            c.icon,
            c.draft,
            c.brief,
            c.color,
            c.priority,
            c.created_at,
            c.last_update
        FROM category c
        INNER JOIN product_category pc 
            ON pc.category_id = c.id
        WHERE pc.product_id = %i
        ORDER BY c.priority ASC
    ", $id);

    // ============================
    // 📤 RESPONSE (CLON DEL ANTIGUO)
    // ============================
    Flight::json([
        'status' => 'success',
        'product' => [
            'product_id'        => (int)$product['product_id'],
            'name'              => $product['name'],
            'image'             => $product['image'],
            'price'             => (float)$product['price'],
            'price_discount'    => (float)$product['price_discount'],
            'draft'             => (int)$product['draft'],
            'description'       => $product['description'],
            'status'            => $product['status'],
            'created_at'        => (int)$product['created_at'],
            'last_update'       => (int)$product['last_update'],
            'fecha_creacion'    => $product['fecha_creacion'],
            'fecha_modificacion'=> $product['fecha_modificacion'],
            'stock'             => (int)$product['stock'],
            'categories'        => $categories,
            'product_images'    => $product_images
        ]
    ]);
});

/*
Flight::route('POST /api/order/submit', function () {

    $payload = json_decode(file_get_contents('php://input'), true);

    if (
        !$payload ||
        empty($payload['product_order']) ||
        empty($payload['product_order_detail'])
    ) {
        Flight::json([
            'status' => 'failed',
            'msg' => 'Invalid payload'
        ]);
        return;
    }

    // ⏱️ Timestamp en milisegundos (Android compatible)
    $now = (int) (microtime(true) * 1000);

    DB::startTransaction();

    try {

        // INSERTAR ORDEN
        $o = $payload['product_order'];

        $mesa_id = (int) ($o['mesa_id'] ?? 0);
        $modo    = ($mesa_id > 0) ? 'MESA' : 'DIRECTA';
        $status  = ($modo === 'MESA') ? 'ABIERTA' : 'PAGADO';

        $year   = date('Y'); // 2026
        $serial = 'ORD-' . $year . '-' . strtoupper(bin2hex(random_bytes(3)));

        DB::insert('product_order', [
            'administrador_id' => $o['administrador_id'] ?? null,
            'cliente_id'       => $o['cliente_id'] ?? null,
            'tipo_pago_id'     => $o['tipo_pago_id'] ?? null,
            'mesa_id'          => $mesa_id > 0 ? $mesa_id : null,
            'modo'             => $modo,

            'status'           => $o['status'] ?? $status,
            'total_fees'       => $o['total_fees'],
            'tax'              => $o['tax'] ?? 0,
            'serial'           => $serial,

            'created_at'       => $o['created_at'] ?? $now,
            'last_update'      => $o['last_update'] ?? $now,
            'caja_id'          => $o['caja_id'] ?? null
        ]);


        $order_id = DB::insertId();

        // INSERTAR STOCK
        foreach ($payload['product_order_detail'] as $d) {

            DB::insert('product_order_detail', [
                'order_id'    => $order_id,
                'product_id'  => $d['product_id'],
                'product_name'=> $d['product_name'],
                'amount'      => $d['amount'],
                'price_item'  => $d['price_item'],
                'created_at'  => $d['created_at']  ?? $now,
                'last_update' => $d['last_update'] ?? $now
            ]);

            // 🔻 Descontar stock
            DB::query("
                UPDATE inventario
                SET stock_actual = stock_actual - %i
                WHERE product_id = %i
            ", $d['amount'], $d['product_id']);
        }

        DB::commit();

        // RESPONSE
        Flight::json([
            'status' => 'success',
            'data' => [
                'id' => $order_id,
                'code' => $serial
            ]
        ]);

    } catch (Exception $e) {

        DB::rollback();

        Flight::json([
            'status' => 'failed',
            'msg' => 'Failed when submit order'
        ]);
    }
});
*/

Flight::route('GET /api/tipo-pago/list', function () {

    $rows = DB::query("
        SELECT 
            tipo_pago_id,
            descripcion,
            orden
        FROM tipo_pago
        ORDER BY orden ASC, descripcion ASC
    ");

    Flight::json([
        'status' => 'success',
        'data'   => $rows
    ]);
});

Flight::route('GET /api/cliente/list', function () {

    $rows = DB::query("
        SELECT 
            *
        FROM cliente
        ORDER BY cliente_id ASC
    ");

    Flight::json([
        'status' => 'success',
        'data'   => $rows
    ]);
});



/**
VERSION CON  LOS CAMPOS ELIMINADOS

{
  "product_order": {
    "cliente_id": 42,
    "administrador_id": 3,
    "caja_id": 8,
    "status": "POS",
    "total_fees": 84.9,
    "tax": 0
  },
  "product_order_detail": [
    {
      "product_id": 25,
      "product_name": "Snacks Papas Lays",
      "amount": 2,
      "price_item": 3.5
    }
  ]
}


Flight::route('POST /api/order/submit', function () {

    $payload = json_decode(file_get_contents('php://input'), true);

    if (
        !$payload ||
        empty($payload['product_order']) ||
        empty($payload['product_order_detail'])
    ) {
        Flight::json([
            'status' => 'failed',
            'msg' => 'Invalid payload'
        ]);
        return;
    }

    $o = $payload['product_order'];

    // ⏱️ timestamp ms (compat Android)
    $now = (int)(microtime(true) * 1000);

    DB::startTransaction();

    try {

        // =============================
        // INSERT ORDER (NUEVO MODELO)
        // =============================
        DB::insert('product_order', [
            'cliente_id'       => $o['cliente_id'],
            'administrador_id' => $o['administrador_id'] ?? null,
            'caja_id'          => $o['caja_id'] ?? null,
            'status'           => $o['status'],
            'total_fees'       => $o['total_fees'],
            'tax'              => $o['tax'],
            'created_at'       => $o['created_at']  ?? $now,
            'last_update'      => $o['last_update'] ?? $now,
            'modo'             => $o['modo'] ?? 'DIRECTA'
        ]);

        $order_id = DB::insertId();

        // ===============================
        //   INSERT DETAILS + STOCK
        // ===============================
        foreach ($payload['product_order_detail'] as $d) {

            DB::insert('product_order_detail', [
                'order_id'    => $order_id,
                'product_id'  => $d['product_id'],
                'product_name'=> $d['product_name'],
                'amount'      => $d['amount'],
                'price_item'  => $d['price_item'],
                'created_at'  => $d['created_at']  ?? $now,
                'last_update' => $d['last_update'] ?? $now
            ]);

            // 🔻 Descontar stock
            DB::query("
                UPDATE inventario
                SET stock_actual = stock_actual - %i
                WHERE product_id = %i
            ", $d['amount'], $d['product_id']);
        }

        DB::commit();

        // =============================
        //   RESPONSE OK
        // =============================
        Flight::json([
            'status' => 'success',
            'data' => [
                'order_id' => $order_id
            ]
        ]);

    } catch (Exception $e) {

        DB::rollback();

        Flight::json([
            'status' => 'failed',
            'msg' => 'Failed when submit order'
        ]);
    }
});


**/

Flight::route('GET /ion/slider', function () {
    include DEFINITION;
    // Traer sliders visibles
    $rows = DB::query("
        SELECT
            slider_id,
            img,
            descripcion,
            fecha_creacion,
            fecha_fin
        FROM slider
        WHERE is_visible = 1
        ORDER BY orden ASC
    ");

    $news_infos = [];

    foreach ($rows as $r) {
        $news_infos[] = [
            'id'            => (int)$r['slider_id'],
            'title'         => $r['descripcion'],
            'brief_content' => $r['descripcion'],
            'image'         => BUNNY_CDN_BASE . "/" . SLIDER_DIR . "/" . $r['img'],
            'draft'         => 0,
            'status'        => 'FEATURED',
            // Android espera timestamps en milisegundos
            'created_at'    => strtotime($r['fecha_creacion']) * 1000,
            'last_update'   => strtotime($r['fecha_fin']) * 1000,
        ];
    }

    Flight::json([
        'status'     => 'success',
        'news_infos' => $news_infos
    ]);
});


Flight::route(
    'GET /ion/reportepos/@administrador_id/@fecha',
    function ($administrador_id, $fecha) {

        // 🗓️ Si viene "hoy", usamos fecha actual
        if ($fecha === 'hoy') {
            $fecha = date('Y-m-d');
        }

        // 🧪 Validación básica
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            Flight::json([
                'status' => 'failed',
                'msg' => 'Formato de fecha inválido (YYYY-MM-DD)'
            ]);
            return;
        }

        // 📦 Ventas del día
        $ventas = DB::query("
            SELECT
                product_order_id,
                administrador_id,
                cliente_id,
                tipo_pago_id,
                mesa_id,
                modo,
                status,
                total_fees,
                tax,
                fecha_creacion
            FROM product_order
            WHERE administrador_id = %i
              AND DATE(fecha_creacion) = %s
            ORDER BY fecha_creacion ASC
        ", $administrador_id, $fecha);

        // 🔢 Totales
        $resumen = DB::queryFirstRow("
            SELECT
                COUNT(*) AS total_ventas,
                IFNULL(SUM(total_fees), 0) AS total_dia
            FROM product_order
            WHERE administrador_id = %i
              AND DATE(fecha_creacion) = %s
        ", $administrador_id, $fecha);

        Flight::json([
            'status' => 'success',
            'fecha' => $fecha,
            'administrador_id' => (int)$administrador_id,
            'resumen' => [
                'total_ventas' => (int)$resumen['total_ventas'],
                'total_dia' => (float)$resumen['total_dia']
            ],
            'data' => $ventas
        ]);
    }
);

Flight::route('GET /api/mesa/pedido-activo/@mesa_id', function ($mesa_id) {

    $todayStart = strtotime('today') * 1000;
    $todayEnd   = strtotime('tomorrow') * 1000;

    $order = DB::queryFirstRow("
        SELECT *
        FROM product_order
        WHERE mesa_id = %i
          AND modo = 'MESA'
          AND status = 'ABIERTA'
          AND created_at BETWEEN %i AND %i
        ORDER BY product_order_id DESC
        LIMIT 1
    ", $mesa_id, $todayStart, $todayEnd);

    if (!$order) {
        Flight::json([
            'status' => 'empty'
        ]);
        return;
    }

    $details = DB::query("
        SELECT *
        FROM product_order_detail
        WHERE order_id = %i
    ", $order['product_order_id']);

    Flight::json([
        'status' => 'success',
        'order'  => $order,
        'items'  => $details
    ]);
});


Flight::route('POST /api/mesa/agregar-productos', function () {

    $payload = json_decode(file_get_contents('php://input'), true);

    DB::startTransaction();

    try {

        foreach ($payload['items'] as $d) {

            DB::insert('product_order_detail', [
                'order_id'    => $payload['order_id'],
                'product_id'  => $d['product_id'],
                'product_name'=> $d['product_name'],
                'amount'      => $d['amount'],
                'price_item'  => $d['price_item'],
                'created_at'  => $d['created_at'],
                'last_update' => $d['last_update']
            ]);

            DB::query("
                UPDATE inventario
                SET stock_actual = stock_actual - %i
                WHERE product_id = %i
            ", $d['amount'], $d['product_id']);
        }

        DB::commit();

        Flight::json(['status' => 'success']);

    } catch (Exception $e) {
        DB::rollback();
        Flight::json(['status' => 'failed']);
    }
});

Flight::route('POST /api/mesa/crear-pedido', function () {

    $payload = json_decode(file_get_contents('php://input'), true);

    if (!$payload || empty($payload['mesa_id'])) {
        Flight::json([
            'status' => 'failed',
            'msg'    => 'mesa_id requerido'
        ]);
        return;
    }

    $mesa_id = (int)$payload['mesa_id'];
    $modo    = $payload['modo'] ?? 'MESA';

    // ⏱️ Fecha y hora actual
    $fecha_inicio = date('Y-m-d H:i:s');

    // ⏱️ Timestamp en ms (para Android)
    $now_ms = (int)(microtime(true) * 1000);

    DB::startTransaction();

    try {

        DB::insert('product_order', [
            'mesa_id'        => $mesa_id,
            'modo'           => $modo,
            'status'         => 'ABIERTA',
            'fecha_inicio'   => $fecha_inicio,   // 👈 AQUÍ
            'created_at'     => $now_ms,
            'last_update'    => $now_ms,
            'total_fees'     => 0,
            'tax'            => 0
        ]);

        $order_id = DB::insertId();

        DB::commit();

        Flight::json([
            'status' => 'success',
            'order'  => [
                'product_order_id' => $order_id,
                'mesa_id'          => $mesa_id,
                'fecha_inicio'     => $fecha_inicio
            ]
        ]);

    } catch (Exception $e) {

        DB::rollback();

        Flight::json([
            'status' => 'failed',
            'msg'    => 'No se pudo crear el pedido'
        ]);
    }
});


Flight::route('POST /api/order/submit', function () {

    $payload = json_decode(file_get_contents('php://input'), true);

    if (
        !$payload ||
        empty($payload['product_order']) ||
        empty($payload['product_order_detail'])
    ) {
        Flight::json([
            'status' => 'failed',
            'msg' => 'Invalid payload'
        ]);
        return;
    }

    $o = $payload['product_order'];

    $mesa_id = (int)($o['mesa_id'] ?? 0);
    $nowMs   = (int)(microtime(true) * 1000);
    $nowSql  = date('Y-m-d H:i:s');

    DB::startTransaction();

    try {

        // =====================================================
        // 🪑 CASO MESA: BUSCAR PEDIDO ABIERTO
        // =====================================================
        $order_id = null;

        if ($mesa_id > 0) {

            $order = DB::queryFirstRow("
                SELECT product_order_id
                FROM product_order
                WHERE mesa_id = %i
                  AND status = 'ABIERTA'
                  AND fecha_fin IS NULL
                ORDER BY product_order_id DESC
                LIMIT 1
            ", $mesa_id);

            if ($order) {
                // ✅ YA EXISTE PEDIDO ABIERTO
                $order_id = (int)$order['product_order_id'];
            }
        }

        // =====================================================
        // ➕ SI NO EXISTE, CREAR PRODUCT_ORDER
        // =====================================================
        if (!$order_id) {

            $modo   = ($mesa_id > 0) ? 'MESA' : 'DIRECTA';
            $status = ($mesa_id > 0) ? 'ABIERTA' : 'PAGADO';

            $serial = 'ORD-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));

            DB::insert('product_order', [
                'cliente_id'       => $o['cliente_id'] ?? null,
                'administrador_id' => $o['administrador_id'] ?? null,
                'caja_id'          => $o['caja_id'] ?? null,
                'mesa_id'          => $mesa_id ?: null,
                'modo'             => $modo,
                'status'           => $status,
                'total_fees'       => $o['total_fees'],
                'tax'              => $o['tax'] ?? 0,
                'serial'           => $serial,
                'fecha_inicio'     => $nowSql,
                'created_at'       => $nowMs,
                'last_update'      => $nowMs
            ]);

            $order_id = DB::insertId();
        }

        // =====================================================
        // 📦 INSERTAR DETALLES + DESCONTAR STOCK
        // =====================================================
        foreach ($payload['product_order_detail'] as $d) {

            DB::insert('product_order_detail', [
                'order_id'     => $order_id,
                'product_id'   => $d['product_id'],
                'product_name' => $d['product_name'],
                'amount'       => $d['amount'],
                'price_item'   => $d['price_item'],
                'created_at'   => $d['created_at']  ?? $nowMs,
                'last_update'  => $d['last_update'] ?? $nowMs
            ]);

            DB::query("
                UPDATE inventario
                SET stock_actual = stock_actual - %i
                WHERE product_id = %i
            ", $d['amount'], $d['product_id']);
        }

        DB::commit();

        Flight::json([
            'status' => 'success',
            'data' => [
                'order_id' => $order_id
            ]
        ]);

    } catch (Exception $e) {

        DB::rollback();

        Flight::json([
            'status' => 'failed',
            'msg' => 'Failed when submit order'
        ]);
    }
});


Flight::route('GET /api/mesa/pedido-abierto/@mesa_id', function ($mesa_id) {

    $order = DB::queryFirstRow("
        SELECT *
        FROM product_order
        WHERE mesa_id = %i
          AND status = 'ABIERTA'
          AND fecha_fin IS NULL
        ORDER BY product_order_id DESC
        LIMIT 1
    ", $mesa_id);

    if (!$order) {
        Flight::json([
            'status' => 'empty'
        ]);
        return;
    }

    $items = DB::query("
        SELECT 
            product_id,
            product_name,
            amount,
            price_item,
            (amount * price_item) AS total
        FROM product_order_detail
        WHERE order_id = %i
    ", $order['product_order_id']);

    $subtotal = 0;
    foreach ($items as $i) {
        $subtotal += $i['total'];
    }

    $tax   = $order['tax'] ?? 0;
    $total = $subtotal + $tax;

    Flight::json([
        'status' => 'success',
        'data' => [
            'order_id' => $order['product_order_id'],
            'subtotal' => $subtotal,
            'tax'      => $tax,
            'total'    => $total,
            'items'    => $items
        ]
    ]);
});


Flight::route('GET /api/order/detail/@id', function ($id) {

    $orderId = (int)$id;

    if ($orderId <= 0) {
        Flight::json([
            'status' => 'failed',
            'msg' => 'ID de orden inválido'
        ]);
        return;
    }

    // 📦 Detalle de productos
    $items = DB::query("
        SELECT
            d.product_id,
            d.product_name,
            d.amount,
            d.price_item,
            o.total_fees
        FROM product_order_detail d
        INNER JOIN product_order o 
            ON o.product_order_id = d.order_id
        WHERE d.order_id = %i
        ORDER BY d.product_order_detail_id ASC
    ", $orderId);

    if (!$items) {
        Flight::json([
            'status' => 'failed',
            'msg' => 'Orden sin detalle'
        ]);
        return;
    }

    Flight::json([
        'status'   => 'success',
        'order_id' => $orderId,
        'total'    => (float)$items[0]['total_fees'],
        'data'     => $items
    ]);
});
