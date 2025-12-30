<?php
declare(strict_types=1);

require_once "services/tools/rest.php";

/*
 * Handle all communication with Android Client
 */
class CLIENT extends REST {

    private ?mysqli $mysqli = null;
    private ?DB $db = null;

    private ?Product $product = null;
    private ?ProductCategory $product_category = null;
    private ?ProductOrder $product_order = null;
    private ?ProductOrderDetail $product_order_detail = null;
    private ?ProductImage $product_image = null;
    private ?Category $category = null;
    private ?User $user = null;
    private ?Fcm $fcm = null;
    private ?NewsInfo $news_info = null;
    private ?Currency $currency = null;
    private ?Config $config = null;
    private ?AppVersion $app_version = null;

    public ?CONF $conf = null;

    public function __construct($db) {

        parent::__construct();

        $this->db = $db;
        $this->mysqli = $db->mysqli;

        // Instancias
        $this->user                 = new User($this->db);
        $this->product              = new Product($this->db);
        $this->product_category     = new ProductCategory($this->db);
        $this->product_order        = new ProductOrder($this->db);
        $this->product_order_detail = new ProductOrderDetail($this->db);
        $this->product_image        = new ProductImage($this->db);
        $this->category             = new Category($this->db);
        $this->news_info            = new NewsInfo($this->db);
        $this->currency             = new Currency($this->db);
        $this->config               = new Config($this->db);
        $this->app_version          = new AppVersion($this->db);
        $this->conf                 = new CONF();
    }

    /* ============================================================
       INFO / VERSION CHECK
       ============================================================ */
    public function info() {

        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        if (!isset($this->_request['version'])) {
            $this->responseInvalidParam();
        }

        $version = (int)$this->_request['version'];

        $query = "SELECT COUNT(DISTINCT a.id)
                  FROM app_version a
                  WHERE version_code = $version AND active = 1";

        $resp_ver = $this->db->get_count($query);

        $config_arr = $this->config->findAllArr();

        $info = [
            "active"   => ($resp_ver > 0),
            "tax"      => $this->getValue($config_arr, 'TAX'),
            "currency" => $this->getValue($config_arr, 'CURRENCY'),
            "shipping" => json_decode($this->getValue($config_arr, 'SHIPPING') ?? "[]", true)
        ];

        $response = ["status" => "success", "info" => $info];
        $this->show_response($response);
    }

    /* ============================================================
       FEATURED NEWS
       ============================================================ */
    public function findAllFeaturedNewsInfo() {

        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        $featured_news = $this->news_info->findAllFeatured();

        $object_res = [];
        foreach ($featured_news as $r) {
            unset($r['full_content']);
            $object_res[] = $r;
        }

        $this->show_response([
            'status' => 'success',
            'news_infos' => $object_res
        ]);
    }

    /* ============================================================
       LIST NEWS WITH PAGINATION
       ============================================================ */
    public function findAllNewsInfo() {

        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        $limit = isset($this->_request['count']) ? (int)$this->_request['count'] : 10;
        $page  = isset($this->_request['page']) ? (int)$this->_request['page'] : 1;
        $q     = $this->_request['q'] ?? "";

        $offset = ($page * $limit) - $limit;

        $count_total = $this->news_info->allCountPlain($q, 1);
        $news_infos  = $this->news_info->findAllByPagePlain($limit, $offset, $q, 1);

        $object_res = [];
        foreach ($news_infos as $r) {
            unset($r['full_content']);
            $object_res[] = $r;
        }

        $this->show_response([
            'status'       => 'success',
            'count'        => count($news_infos),
            'count_total'  => $count_total,
            'pages'        => $page,
            'news_infos'   => $object_res
        ]);
    }

    /* ============================================================
       ALL PRODUCTS
       ============================================================ */
    public function findAllProduct() {

        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        $limit       = isset($this->_request['count']) ? (int)$this->_request['count'] : 10;
        $page        = isset($this->_request['page']) ? (int)$this->_request['page'] : 1;
        $q           = $this->_request['q'] ?? "";
        $category_id = isset($this->_request['category_id']) ? (int)$this->_request['category_id'] : -1;

        $offset = ($page * $limit) - $limit;

        $count_total = $this->product->allCountPlainForClient($q, $category_id);
        $products    = $this->product->findAllByPagePlainForClient($limit, $offset, $q, $category_id);

        $object_res = [];
        foreach ($products as $r) {
            unset($r['description']);
            $object_res[] = $r;
        }

        $this->show_response([
            'status'      => 'success',
            'count'       => count($products),
            'count_total' => $count_total,
            'pages'       => $page,
            'products'    => $object_res
        ]);
    }

    /* ============================================================
       PRODUCT DETAILS
       ============================================================ */
    public function findProductDetails() {

        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        if (!isset($this->_request['id'])) {
            $this->responseInvalidParam();
        }

        $id = (int)$this->_request['id'];

        // =========================
        // PRODUCTO
        // =========================
        $product = $this->product->findOnePlain($id);

        if ($product) {

            // =========================
            // CATEGORÍAS
            // =========================
            $categories = $this->category->getAllByProductIdPlain($id);

            // =========================
            // IMÁGENES
            // =========================
            $product_images = $this->product_image->findAllByProductIdPlain($id);

            // 👉 CLAVE: definir imagen principal
            if (!empty($product_images)) {
                // Android necesita product.image
                $product['image'] = $product_images[0]['name'];
            } else {
                $product['image'] = '';
            }

            // =========================
            // ARMAR RESPUESTA
            // =========================
            $product['categories']     = $categories;
            $product['product_images'] = $product_images;

            $response = [
                'status'  => 'success',
                'product' => $product
            ];

        } else {

            $response = [
                'status'  => 'failed',
                'product' => null
            ];
        }

        $this->show_response($response);
    }


    /* ============================================================
       NEWS DETAILS
       ============================================================ */
    public function findNewsDetails() {

        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        if (!isset($this->_request['id'])) {
            $this->responseInvalidParam();
        }

        $id = (int)$this->_request['id'];

        $news_info = $this->news_info->findOnePlain($id);

        $response = [
            'status'    => 'success',
            'news_info' => $news_info
        ];

        $this->show_response($response);
    }

    /* ============================================================
       CATEGORY LIST
       ============================================================ */
    public function findAllCategory() {

        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        $categories = $this->category->findAllForClient();

        $this->show_response([
            'status'     => 'success',
            'categories' => $categories
        ]);
    }


    /* ============================================================
       SUBMIT PRODUCT ORDER — ESTABLE / SIN DELETE
       ============================================================ */
    public function submitProductOrder() {

        if ($this->get_request_method() !== "POST") {
            $this->response('', 406);
        }

        // =========================
        // 1) Seguridad
        // =========================
        $security = $this->_header['Security'] ?? '';
        if ($security !== $this->conf->SECURITY_CODE) {
            $this->show_response([
                'status' => 'failed',
                'msg'    => 'Invalid security code',
                'data'   => null
            ]);
            return;
        }

        // =========================
        // 2) Leer JSON
        // =========================
        $payload = json_decode(file_get_contents("php://input") ?: "[]", true);

        if (
            !isset($payload['product_order']) ||
            !isset($payload['product_order_detail'])
        ) {
            $this->responseInvalidParam();
        }

        $order  = $payload['product_order'];
        $detail = $payload['product_order_detail'];

        // =========================
        // 3) Generar código pedido
        // =========================
        $code = 'POS-' . date('Ymd') . '-' . str_pad((string)rand(1, 999999), 6, '0', STR_PAD_LEFT);

        // =========================
        // 4) Insertar ORDER
        // =========================
        $sql_order = "
            INSERT INTO product_order (
                code, buyer, email, phone, address,
                shipping, date_ship, comment, status,
                tax, total_fees, serial,
                administrador_id, caja_id,
                created_at, last_update
            ) VALUES (
                '$code',
                '{$this->db->real_escape($order['buyer'])}',
                '{$this->db->real_escape($order['email'])}',
                '{$this->db->real_escape($order['phone'])}',
                '{$this->db->real_escape($order['address'])}',
                '{$this->db->real_escape($order['shipping'])}',
                {$order['date_ship']},
                '{$this->db->real_escape($order['comment'])}',
                '{$this->db->real_escape($order['status'])}',
                {$order['tax']},
                {$order['total_fees']},
                '{$this->db->real_escape($order['serial'])}',
                {$order['administrador_id']},
                {$order['caja_id']},
                {$order['created_at']},
                {$order['last_update']}
            )
        ";

        if (!$this->db->mysqli->query($sql_order)) {
            $this->show_response([
                'status' => 'failed',
                'msg'    => $this->db->mysqli->error,
                'data'   => null
            ]);
            return;
        }

        $order_id = $this->db->mysqli->insert_id;

        // =========================
        // 5) Insertar DETALLES
        // =========================
        foreach ($detail as $d) {

            $sql_detail = "
                INSERT INTO product_order_detail (
                    product_order_id,
                    product_id,
                    product_name,
                    amount,
                    price_item,
                    created_at,
                    last_update
                ) VALUES (
                    $order_id,
                    {$d['product_id']},
                    '{$this->db->real_escape($d['product_name'])}',
                    {$d['amount']},
                    {$d['price_item']},
                    {$d['created_at']},
                    {$d['last_update']}
                )
            ";

            if (!$this->db->mysqli->query($sql_detail)) {

                // marcar pedido como fallido
                $this->db->mysqli->query("
                    UPDATE product_order
                    SET status = 'FAILED'
                    WHERE product_order_id = $order_id
                ");

                $this->show_response([
                    'status' => 'failed',
                    'msg'    => 'Failed inserting order detail',
                    'data'   => null
                ]);
                return;
            }
        }

        // =========================
        // 6) RESPUESTA PARA ANDROID
        // =========================
        $this->show_response([
            'status' => 'success',
            'msg'    => 'Success submit product order',
            'data'   => [
                'id'   => $order_id,
                'code' => $code
            ]
        ]);
    }

    

    /* ============================================================
       Helper
       ============================================================ */
    private function getValue(array $data, string $code) {

        foreach ($data as $d) {
            if (($d['code'] ?? null) === $code) {
                return $d['value'] ?? null;
            }
        }

        return null;
    }
}
?>
