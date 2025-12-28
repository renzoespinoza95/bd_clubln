<?php
declare(strict_types=1);

require_once realpath(dirname(__FILE__) . "/../tools/rest.php");

class ProductOrderDetail extends REST {

    private ?mysqli $mysqli = null;
    private $db = null;
    private $mail_handler = null;

    public function __construct($db) {
        parent::__construct();
        $this->db           = $db;
        $this->mysqli       = $db->mysqli;
    }

    public function findAll() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        $query = "SELECT * FROM product_order_detail pod";
        $this->show_response($this->db->get_list($query));
    }

    public function insertAllPlain(int $order_id, array $data) {
        foreach ($data as $i => $d) {
            $data[$i]['order_id'] = $order_id;
        }

        $column_names = [
            'order_id', 'product_id', 'product_name',
            'amount', 'price_item', 'created_at', 'last_update'
        ];
        $table_name = 'product_order_detail';

        return $this->db->post_array($data, $column_names, $table_name);
    }

    public function deleteInsertAll() {
        if ($this->get_request_method() !== "POST") {
            $this->response('', 406);
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data)) {
            $this->responseInvalidParam();
        }

        // Default is new order
        $is_new = isset($this->_request['is_new'])
                    ? (int)$this->_request['is_new']
                    : 1;

        $column_names = [
            'order_id', 'product_id', 'product_name',
            'amount', 'price_item', 'created_at', 'last_update'
        ];
        $table_name = 'product_order_detail';

        try {
            if (count($data) > 0) {
                $order_id = (int)$data[0]['order_id'];
                $query = "DELETE FROM $table_name WHERE order_id = $order_id";
                $this->mysqli->query($query);
            }
        } catch (Exception $e) {}

        // Insert records
        $resp = $this->db->post_array($data, $column_names, $table_name);

        // ❌ FIX BUG ORIGINAL
        // Tenías:
        // if ($resp['status'] = 'success') { ... }
        // Eso es una asignación, no comparación → SIEMPRE ERA TRUE
        //
        // Ahora:
        if ($resp['status'] === 'success' && $is_new === 1) {
            $this->mail_handler->curlEmailOrder($data[0]['order_id']);
        }

        $this->show_response($resp);
    }

    public function findAllByOrderIdPlain(int $order_id) {
        $query = "SELECT DISTINCT * FROM product_order_detail pod WHERE pod.order_id=$order_id";
        return $this->db->get_list($query);
    }

    public function findAllByOrderId() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        if (!isset($this->_request['order_id'])) {
            $this->responseInvalidParam();
        }

        $order_id = (int)$this->_request['order_id'];
        $this->show_response($this->findAllByOrderIdPlain($order_id));
    }

    public function checkAvailableProductOrderDetail(array $order_detail) {
        $resp = ['status' => 'success', 'data' => []];

        $status_list = [];

        foreach ($order_detail as $od) {

            $item = [
                'product_id'   => $od['product_id'],
                'stock'        => 0,
                'amount'       => $od['amount'],
                'product_name' => $od['product_name'],
                'msg'          => 'OK'
            ];

            $product_id = (int)$od['product_id'];
            $query      = "SELECT * FROM product p WHERE p.id=$product_id LIMIT 1";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {

                $result            = $r->fetch_assoc();
                $item['stock']     = (int)$result['stock'];
                $requested_amount  = (int)$od['amount'];

                if ($result['stock'] < $requested_amount) {
                    $item['msg'] = 'Stock Not Enough';
                    $resp['status'] = 'failed';
                }

            } else {
                $item['msg'] = 'Product Not Exist';
                $resp['status'] = 'failed';
            }

            $status_list[] = $item;
        }

        $resp['data'] = $status_list;
        return $resp;
    }

}
?>
