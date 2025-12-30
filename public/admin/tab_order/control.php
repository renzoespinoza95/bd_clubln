<?php
// este es mi backend usando php8.2, flightphp y meekrodb2
Flight::route('GET /order', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $path_public;                          // asegúrate de tener esta var en tu bootstrap
    include $path_public . '/admin/tab_order/inicio.php';
});

/* ======================================
   LISTAR ÓRDENES
====================================== */
Flight::route('GET /product_order/listar', function(){
    DB::query("SET NAMES 'utf8mb4'");
    $rows = DB::query("
        SELECT product_order_id, code, buyer, status, total_fees,
               FROM_UNIXTIME(created_at/1000,'%Y-%m-%d %H:%i:%s') AS fecha
        FROM product_order
        ORDER BY product_order_id DESC
    ");
    Flight::json($rows);
});

/* ======================================
   CREAR ORDEN
====================================== */
function generarCodigoOrden(){
  return strtoupper(bin2hex(random_bytes(4))); // ej: A9F2C1D3
}

Flight::route('POST /product_order/crear', function () {

    include DEFINITION;

    // ===============================
    // 1) Verificar sesión
    // ===============================
    if (!$sesion_admin_administrador_id) {
        Flight::json([
            'status' => 'error',
            'msg'    => 'Sesión no válida'
        ], 401);
        return;
    }

    // ===============================
    // 2) Obtener administrador_id
    // ===============================
    $valor_key = $nombre_app . vari("KEY");
    $administrador_id = (int) str_replace(
        "*",
        "",
        util::decrypt($sesion_admin_administrador_id, $valor_key)
    );

    if (!$administrador_id) {
        Flight::json([
            'status' => 'error',
            'msg'    => 'Administrador inválido'
        ], 401);
        return;
    }

    // ===============================
    // 3) Buscar caja ABIERTA del día
    // ===============================
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

    // ===============================
    // 4) Leer payload
    // ===============================
    $d = Flight::request()->data->getData();

    if (empty($d['items']) || !is_array($d['items'])) {
        Flight::json([
            'status' => 'error',
            'msg'    => 'No hay ítems en la orden'
        ], 400);
        return;
    }

    // ===============================
    // 5) Crear orden
    // ===============================
    DB::startTransaction();

    $now = time() * 1000;

    DB::insert('product_order', [
        'code'               => generarCodigoOrden(),
        'buyer'              => $d['buyer'] ?? 'CLIENTE',
        'address'            => $d['address'] ?? '',
        'administrador_id'   => $administrador_id,
        'caja_id'            => $caja['caja_id'],
        'tipo_pago_id'       => $d['tipo_pago_id'],
        'status'             => 'VENTA',
        'total_fees'         => $d['total_fees'],
        'created_at'         => $now,
        'last_update'        => $now,
        'fecha_creacion'     => date('Y-m-d H:i:s'),
        'fecha_modificacion' => date('Y-m-d H:i:s')
    ]);

    $order_id = DB::insertId();

    // ===============================
    // 6) Insertar detalles
    // ===============================
    foreach ($d['items'] as $i) {

        DB::insert('product_order_detail', [
            'order_id'    => $order_id,
            'product_id'  => $i['product_id'],
            'product_name'=> DB::queryFirstField(
                "SELECT name FROM product WHERE product_id = %i",
                $i['product_id']
            ),
            'amount'      => $i['amount'],
            'price_item'  => $i['price_item'],
            'created_at'  => $now,
            'last_update' => $now
        ]);

        // movimiento de inventario
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

    DB::commit();

    // ===============================
    // 7) Respuesta final
    // ===============================
    Flight::json([
        'status' => 'ok',
        'product_order_id' => $order_id,
        'caja_id' => $caja['caja_id']
    ]);
});



/* ======================================
   EDITAR ORDEN
====================================== */
Flight::route('POST /product_order/editar', function(){
    $d = Flight::request()->data->getData();
    $now = time()*1000;

    DB::update('product_order',[
        'buyer'=>$d['buyer'],
        'address'=>$d['address'],
        'status'=>$d['status'],
        'last_update'=>$now,
        'fecha_modificacion'=>date("Y-m-d H:i:s")
    ],"product_order_id=%i",$d['product_order_id']);

    Flight::json(['status'=>'ok']);
});

/* ======================================
   ELIMINAR ORDEN
====================================== */
Flight::route('POST /product_order/eliminar', function(){
    $d = Flight::request()->data->getData();
    DB::delete('product_order',"product_order_id=%i",$d['product_order_id']);
    Flight::json(['status'=>'ok']);
});

Flight::route('GET /product_order/detalle/@id', function($id){

    // Orden + tipo de pago
    $order = DB::queryFirstRow("
        SELECT o.*,
               tp.descripcion AS tipo_pago
        FROM product_order o
        LEFT JOIN tipo_pago tp 
               ON tp.tipo_pago_id = o.tipo_pago_id
        WHERE o.product_order_id = %i
    ", $id);

    // Detalles (items)
    $det = DB::query("
        SELECT d.*,
               p.name AS product_name
        FROM product_order_detail d
        LEFT JOIN product p 
               ON p.product_id = d.product_id
        WHERE d.order_id = %i
        ORDER BY d.product_order_detail_id ASC
    ", $id);

    Flight::json([
        'order'    => $order,
        'detalles' => $det
    ]);
});

Flight::route('POST /product_order_detail/crear', function(){

    $d = Flight::request()->data->getData();
    $now = time()*1000;

    DB::startTransaction();
    try {

        // insertar detalle
        DB::insert('product_order_detail',[
            'order_id' => $d['order_id'],
            'product_id' => $d['product_id'],
            'product_name' => DB::queryFirstField(
                "SELECT name FROM product WHERE product_id=%i",
                $d['product_id']
            ),
            'amount' => $d['amount'],
            'price_item' => $d['price_item'],
            'created_at' => $now,
            'last_update' => $now,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'fecha_modificacion' => date('Y-m-d H:i:s')
        ]);

        // 🔴 DESCONTAR INVENTARIO
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

        DB::commit();
        Flight::json(['status'=>'ok']);

    } catch(Exception $e){
        DB::rollback();
        Flight::json(['status'=>'error','msg'=>$e->getMessage()],500);
    }
});

Flight::route('POST /product_order_detail/eliminar', function(){

    $d = Flight::request()->data->getData();

    $item = DB::queryFirstRow(
        "SELECT * FROM product_order_detail WHERE product_order_detail_id=%i",
        $d['product_order_detail_id']
    );

    DB::startTransaction();
    try {

        // devolver stock
        registrar_movimiento_inventario(
            $item['product_id'],
            'ENTRADA',
            'DEVOLUCION_VENTA',
            $item['amount'],
            $item['price_item'],
            $item['order_id'],
            'product_order'
        );

        actualizar_estado_orden($d['order_id'], 'EDITADO');

        DB::delete(
            'product_order_detail',
            "product_order_detail_id=%i",
            $d['product_order_detail_id']
        );

        DB::commit();
        Flight::json(['status'=>'ok']);

    } catch(Exception $e){
        DB::rollback();
        Flight::json(['status'=>'error','msg'=>$e->getMessage()],500);
    }
});


Flight::route('POST /product_order_detail/editar', function () {

  $d = Flight::request()->data->getData();

  $old = DB::queryFirstRow(
    "SELECT * FROM product_order_detail WHERE product_order_detail_id=%i",
    $d['product_order_detail_id']
  );

  DB::startTransaction();
  try {

    // devolver stock viejo
    registrar_movimiento_inventario(
      $old['product_id'],
      'ENTRADA',
      'AJUSTE',
      $old['amount'],
      $old['price_item'],
      $old['order_id'],
      'product_order'
    );

    // aplicar nuevo
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

    DB::update('product_order_detail',[
      'amount'=>$d['amount'],
      'price_item'=>$d['price_item'],
      'last_update'=>time()*1000,
      'fecha_modificacion'=>date('Y-m-d H:i:s')
    ],"product_order_detail_id=%i",$d['product_order_detail_id']);

    DB::commit();
    Flight::json(['status'=>'ok']);

  } catch(Exception $e){
    DB::rollback();
    Flight::json(['status'=>'error','msg'=>$e->getMessage()],500);
  }
});


function actualizar_estado_orden($order_id, $estado){
    DB::update(
        'product_order',
        [
            'status' => $estado,
            'fecha_modificacion' => date('Y-m-d H:i:s'),
            'last_update' => time()*1000
        ],
        "product_order_id=%i",
        $order_id
    );
}

Flight::route('GET /cliente/listar', function(){
  Flight::json(
    DB::query("SELECT cliente_id,dni,nombre FROM cliente WHERE is_activo=1")
  );
});

Flight::route('POST /cliente/crear', function(){
  $d = Flight::request()->data->getData();
  DB::insert('cliente',[
    'dni'=>$d['dni'],
    'nombre'=>$d['nombre']
  ]);
  Flight::json(['ok'=>1]);
});


Flight::route('GET /auth/administrador-actual', function () {

    include DEFINITION;

    // 🔐 verificar sesión
    if (!$sesion_admin_administrador_id) {
        Flight::json([
            'status' => 'error',
            'msg'    => 'No autenticado'
        ], 401);
        return;
    }

    // 🔓 desencriptar administrador_id
    $valor_key = $nombre_app . vari("KEY");
    $administrador_id = str_replace(
        "*",
        "",
        util::decrypt($sesion_admin_administrador_id, $valor_key)
    );

    // 🧑‍💼 info del administrador
    $admin = login_admin::informacion_administrador_por_id($administrador_id);

    if (!$admin) {
        Flight::json([
            'status' => 'error',
            'msg'    => 'Administrador no encontrado'
        ], 404);
        return;
    }

    // 📅 buscar caja del día
    $hoy = date('Y-m-d');

    $caja = DB::queryFirstRow("
        SELECT *
        FROM caja
        WHERE administrador_id = %i
          AND DATE(fecha_apertura) = %s
        ORDER BY caja_id DESC
        LIMIT 1
    ", $administrador_id, $hoy);

    // 🟥 si no hay caja → CERRADA
    if (!$caja) {
        $caja = [
            'estado' => 'CERRADA'
        ];
    }

    // ✅ respuesta
    Flight::json([
        'status' => 'ok',
        'administrador' => [
            'administrador_id' => $admin['administrador_id'],
            'nombre'           => $admin['nombres_apellidos'] ?? '',
            'email'            => $admin['email'] ?? ''
        ],
        'caja' => $caja
    ]);
});

Flight::route('GET /tipo_pago/listar', function(){
    $rows = DB::query("
        SELECT tipo_pago_id, descripcion
        FROM tipo_pago
        ORDER BY orden ASC
    ");
    Flight::json($rows);
});
