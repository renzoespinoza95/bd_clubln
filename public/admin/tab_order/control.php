<?php
/* -------------------------------
 * Vista /tab3
 * ------------------------------- */
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
        SELECT id, code, buyer, status, total_fees,
               FROM_UNIXTIME(created_at/1000,'%Y-%m-%d %H:%i:%s') AS fecha
        FROM product_order
        ORDER BY id DESC
    ");
    Flight::json($rows);
});

/* ======================================
   CREAR ORDEN
====================================== */
Flight::route('POST /product_order/crear', function(){
    $d = Flight::request()->data->getData();
    $now = time()*1000;

    DB::insert('product_order',[
        'code'        => $d['code'],
        'buyer'       => $d['buyer'],
        'address'     => $d['address'],
        'shipping'    => '',
        'date_ship'   => $now,
        'email'       => '',
        'phone'       => '',
        'comment'     => '',
        'status'      => $d['status'],
        'total_fees'  => $d['total_fees'],
        'tax'         => 0,
        'created_at'  => $now,
        'last_update' => $now,
        'fecha_creacion' => date("Y-m-d H:i:s"),
        'fecha_modificacion' => date("Y-m-d H:i:s")
    ]);

    Flight::json(['status'=>'ok']);
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
    ],"id=%i",$d['id']);

    Flight::json(['status'=>'ok']);
});

/* ======================================
   ELIMINAR ORDEN
====================================== */
Flight::route('POST /product_order/eliminar', function(){
    $d = Flight::request()->data->getData();
    DB::delete('product_order',"id=%i",$d['id']);
    Flight::json(['status'=>'ok']);
});


Flight::route('GET /product_order/detalle/@id', function($id){
    $order = DB::queryFirstRow("SELECT * FROM product_order WHERE id=%i",$id);

    $det = DB::query("
        SELECT d.*, p.name AS product_name
        FROM product_order_detail d
        LEFT JOIN product p ON p.id = d.product_id
        WHERE d.order_id=%i
        ORDER BY d.id ASC
    ",$id);

    Flight::json([
        'order'=>$order,
        'detalles'=>$det
    ]);
});
