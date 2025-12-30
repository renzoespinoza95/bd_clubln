<?php
declare(strict_types=1);

require_once realpath(dirname(__FILE__) . "/../tools/rest.php");

class ProductOrder extends REST {

    private ?mysqli $mysqli = null;
    private $db = null;
    private $product_order_detail = null;
    private $fcm = null;
    private $mail_handler = null;

    public function __construct($db) {
        parent::__construct();
        $this->db                   = $db;
        $this->mysqli               = $db->mysqli;
        $this->product_order_detail = new ProductOrderDetail($this->db);
    }

    public function findAll() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        $query = "SELECT * FROM product_order po ORDER BY po.product_order_id DESC";
        $this->show_response($this->db->get_list($query));
    }

    public function findOne() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        if (!isset($this->_request['id'])) {
            $this->responseInvalidParam();
        }

        $id    = (int)$this->_request['id'];
        $query = "SELECT DISTINCT * FROM product_order po WHERE po.product_order_id=$id";

        $this->show_response($this->db->get_one($query));
    }

    public function findOnePlain(int $id) {
        $query = "SELECT * FROM product_order po WHERE po.product_order_id=$id";
        return $this->db->get_one($query);
    }

    public function findAllByPage() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        if (!isset($this->_request['limit']) || !isset($this->_request['page'])) {
            $this->responseInvalidParam();
        }

        $limit  = (int)$this->_request['limit'];
        $offset = ((int)$this->_request['page']) - 1;
        $q      = $this->_request['q'] ?? "";

        if ($q !== "") {
            $query =
                "SELECT DISTINCT * FROM product_order po 
                 WHERE buyer REGEXP '$q' 
                    OR code REGEXP '$q' 
                    OR address REGEXP '$q' 
                    OR email REGEXP '$q' 
                    OR phone REGEXP '$q' 
                    OR comment REGEXP '$q' 
                    OR shipping REGEXP '$q' 
                 ORDER BY po.product_order_id DESC 
                 LIMIT $limit OFFSET $offset";
        } else {
            $query =
                "SELECT DISTINCT * FROM product_order po 
                 ORDER BY po.product_order_id DESC 
                 LIMIT $limit OFFSET $offset";
        }

        $this->show_response($this->db->get_list($query));
    }

    public function allCount() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        $query = "SELECT COUNT(DISTINCT po.product_order_id) FROM product_order po";
        $count = $this->db->get_count($query);
        $this->show_response_plain(json_encode($count));
    }

    public function insertOne() {
        if ($this->get_request_method() !== "POST") {
            $this->response('', 406);
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data)) {
            $this->responseInvalidParam();
        }

        $resp = $this->insertOnePlain($data);
        $this->show_response($resp);
    }

    public function insertOnePlain(array $data) {

        $column_names = [
            'code', 'buyer', 'address', 'email',
            'shipping', 'date_ship', 'phone', 'comment',
            'status', 'total_fees', 'tax', 'serial',
            'created_at', 'last_update',
            'caja_id', 'administrador_id'
        ];

        $table_name = 'product_order';
        $pk         = 'product_order_id';

        // Generar código único
        $data['code'] = $this->getRandomCode();

        // Insertar
        $resp = $this->db->post_one($data, $pk, $column_names, $table_name);

        // DEVOLVER SOLO EL ID
        return (int)$resp['data'][$pk];
    }



    public function updateOne() {
        if ($this->get_request_method() !== "POST") {
            $this->response('', 406);
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id'])) {
            $this->responseInvalidParam();
        }

        $id = (int)$data['id'];

        $column_names = [
            'buyer', 'address', 'email', 'shipping',
            'date_ship', 'phone', 'comment', 'status',
            'total_fees', 'tax', 'serial', 'created_at', 'last_update'
        ];

        $table_name = 'product_order';
        $pk         = 'product_order_id';

        $this->show_response($this->db->post_update($id, $data, $pk, $column_names, $table_name));
    }

    public function deleteOne() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        if (!isset($this->_request['id'])) {
            $this->responseInvalidParam();
        }

        $id = (int)$this->_request['id'];
        $this->show_response($this->deleteOnePlain($id));
    }

    public function deleteOnePlain(int $id) {
        $table_name = 'product_order';
        $pk         = 'product_order_id';
        return $this->db->delete_one($id, $pk, $table_name);
    }

    public function countByStatusPlain(string $status) {
        $query = "SELECT COUNT(DISTINCT po.product_order_id) FROM product_order po WHERE po.status='$status'";
        return $this->db->get_count($query);
    }

    public function processOrder() {
        if ($this->get_request_method() !== "POST") {
            $this->response('', 406);
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id'], $data['product_order'], $data['product_order_detail'])) {
            $this->responseInvalidParam();
        }

        $order_id      = (int)$data['id'];
        $order         = $data['product_order'];
        $order_details = $data['product_order_detail'];

        // Verificar stock
        $resp_od = $this->product_order_detail->checkAvailableProductOrderDetail($order_details);

        if ($resp_od['status'] === 'success') {

            // Actualizar stock
            foreach ($resp_od['data'] as $od) {
                $new_stock  = (int)$od['stock'] - (int)$od['amount'];
                $product_id = (int)$od['product_id'];

                $query = "UPDATE product SET stock=$new_stock WHERE id=$product_id";
                $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
            }

            // Actualizar estado de orden
            $new_status = 'PROCESSED';
            $query2 = "UPDATE product_order SET status='$new_status' WHERE id=$order_id";
            $this->mysqli->query($query2) or die($this->mysqli->error . __LINE__);

            // Notificación
            $order['id']     = $order_id;
            $order['status'] = $new_status;
            $this->sendNotifProductOrder($order);

            // Email
            $this->mail_handler->curlEmailOrderProcess($order_id);
        }

        $this->show_response($resp_od);
    }

    private function sendNotifProductOrder(array $order) {
        if (!empty($order['serial'])) {

            $regid = $this->fcm->findBySerial($order['serial']);

            if ($regid && isset($regid['regid'])) {
                $registration_ids = [$regid['regid']];

                $data = [
                    'title'   => 'Order Status Changed',
                    'content' => 'Your order ' . $order['code'] . ' status has been change to ' . $order['status'],
                    'type'    => 'PROCESS_ORDER',
                    'code'    => $order['code'],
                    'status'  => $order['status']
                ];

                $this->fcm->sendPushNotification($registration_ids, $data);
            }
        }
    }

    private function getRandomCode(): string {
        $size = 10;
        $keysAlpha = range('A', 'Z');
        $keysNum   = range(0, 9);

        $alpha1 = $keysAlpha[array_rand($keysAlpha)] . $keysAlpha[array_rand($keysAlpha)];
        $alpha2 = $keysAlpha[array_rand($keysAlpha)] . $keysAlpha[array_rand($keysAlpha)];

        $middle = "";
        for ($i = 0; $i < ($size - 5); $i++) {
            $middle .= $keysNum[array_rand($keysNum)];
        }

        $final_key = $alpha1 . $middle . $alpha2;

        // Validar unicidad
        $query = "SELECT COUNT(DISTINCT po.product_order_id) FROM product_order po WHERE po.code='$final_key'";
        $exists = $this->db->get_count($query);

        return $exists > 0 ? $this->getRandomCode() : $final_key;
    }

    public function updateStatusPlain(int $order_id, string $status): void {

        $status = $this->db->real_escape($status);

        $sql = "
            UPDATE product_order
            SET status = '{$status}'
            WHERE product_order_id = {$order_id}
        ";

        $this->mysqli->query($sql);
    }

}
?>
