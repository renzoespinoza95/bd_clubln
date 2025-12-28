<?php
declare(strict_types=1);

require_once realpath(dirname(__FILE__) . "/../tools/rest.php");

class ProductCategory extends REST {

    private ?mysqli $mysqli = null;
    private $db = null;

    public function __construct($db) {
        parent::__construct();
        $this->db     = $db;
        $this->mysqli = $db->mysqli;
    }

    public function findAll() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        $query = "SELECT * FROM product_category pc";
        $this->show_response($this->db->get_list($query));
    }

    public function deleteInsertAll() {
        if ($this->get_request_method() !== "POST") {
            $this->response('', 406);
        }

        $product_category = json_decode(file_get_contents("php://input") ?: "[]", true);

        if (!$product_category || !isset($product_category[0]['product_id'])) {
            $this->responseInvalidParam();
        }

        $column_names = ['product_id', 'category_id'];
        $table_name   = 'product_category';

        // DELETE previous categories for this product
        try {
            $product_id = (int)$product_category[0]['product_id'];
            $query = "DELETE FROM {$table_name} WHERE product_id = $product_id";
            $this->mysqli->query($query);
        } catch (Exception $e) {
            // Silencioso a propósito, mismo comportamiento que tu código original
        }

        // INSERT new rows
        $resp = $this->db->post_array($product_category, $column_names, $table_name);

        $this->show_response($resp);
    }
}
?>
