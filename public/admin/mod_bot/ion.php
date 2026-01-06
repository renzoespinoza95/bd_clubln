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

//ANTES: getListProduct)
Flight::route('GET /products', function () {
    /*
     * ENDPOINT ANTERIOR:
     * getListProduct
     */
    $page  = (int)(Flight::request()->query['page'] ?? 1);
    $limit = (int)(Flight::request()->query['limit'] ?? 20);
    $offset = ($page - 1) * $limit;

    $rows = DB::query("
        SELECT p.*, IFNULL(i.stock_actual,0) stock
        FROM product p
        LEFT JOIN inventario i ON i.product_id = p.product_id
        ORDER BY p.product_id DESC
        LIMIT %i OFFSET %i
    ", $limit, $offset);

    Flight::json([
        'status' => 'success',
        'data'   => $rows
    ]);
});


Flight::route('GET /products/@id', function ($id) {
    /*
     * ENDPOINT ANTERIOR:
     * getProductDetails
     */
    $row = DB::queryFirstRow("
        SELECT p.*, IFNULL(i.stock_actual,0) stock
        FROM product p
        LEFT JOIN inventario i ON i.product_id = p.product_id
        WHERE p.product_id = %i
    ", $id);

    Flight::json([
        'status' => $row ? 'success' : 'failed',
        'data'   => $row
    ]);
});


//(ANTES: getProductDetails)
Flight::route('GET /products/@id', function ($id) {
    /*
     * ENDPOINT ANTERIOR:
     * getProductDetails
     */
    $row = DB::queryFirstRow("
        SELECT p.*, IFNULL(i.stock_actual,0) stock
        FROM product p
        LEFT JOIN inventario i ON i.product_id = p.product_id
        WHERE p.product_id = %i
    ", $id);

    Flight::json([
        'status' => $row ? 'success' : 'failed',
        'data'   => $row
    ]);
});


//(ANTES: submitProductOrder)
// routes/order.php
Flight::route('POST /orders', function () {
    /*
     * ENDPOINT ANTERIOR:
     * submitProductOrder
     */
    $payload = json_decode(Flight::request()->getBody(), true);

    DB::startTransaction();

    try {
        $o = $payload['product_order'];

        DB::insert('product_order', [
            'buyer'            => $o['buyer'],
            'email'            => $o['email'],
            'phone'            => $o['phone'],
            'address'          => $o['address'],
            'shipping'         => $o['shipping'],
            'comment'          => $o['comment'],
            'status'           => $o['status'],
            'tax'              => $o['tax'],
            'total_fees'       => $o['total_fees'],
            'serial'           => $o['serial'],
            'administrador_id' => $o['administrador_id'],
            'caja_id'          => $o['caja_id'],
            'created_at'       => $o['created_at'],
            'last_update'      => $o['last_update'],
            'fecha_creacion'   => date('Y-m-d H:i:s')
        ]);

        $orderId = DB::insertId();

        foreach ($payload['product_order_detail'] as $d) {
            DB::insert('product_order_detail', [
                'order_id'    => $orderId,
                'product_id'  => $d['product_id'],
                'product_name'=> $d['product_name'],
                'amount'      => $d['amount'],
                'price_item'  => $d['price_item'],
                'created_at'  => $d['created_at'],
                'last_update' => $d['last_update'],
                'fecha_creacion' => date('Y-m-d H:i:s')
            ]);

            // ↓ descontar stock
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
                'id'   => $orderId,
                'code' => 'ORD-' . str_pad($orderId, 6, '0', STR_PAD_LEFT)
            ]
        ]);

    } catch (Exception $e) {
        DB::rollback();
        Flight::json([
            'status' => 'failed',
            'msg'    => $e->getMessage()
        ], 500);
    }
});


//(ANTES: getListCategory)
Flight::route('GET /categories', function () {
    /*
     * ENDPOINT ANTERIOR:
     * getListCategory
     */
    $rows = DB::query("SELECT * FROM category ORDER BY category_id DESC");
    Flight::json(['status'=>'success','data'=>$rows]);
});


//(ANTES: getFeaturedNews)
Flight::route('GET /news/featured', function () {
    /*
     * ENDPOINT ANTERIOR:
     * getFeaturedNews
     */
    $rows = DB::query("SELECT * FROM news WHERE featured=1 LIMIT 10");
    Flight::json(['status'=>'success','data'=>$rows]);
});



