<?php
declare(strict_types=1);

require_once realpath(dirname(__FILE__) . "/../tools/rest.php");

class Product extends REST {

    private ?mysqli $mysqli = null;
    private $db = null;

    public function __construct($db) {
        parent::__construct();
        $this->db     = $db;
        $this->mysqli = $db->mysqli;
    }

    /* ============================================================
       FIND ALL
       ============================================================ */
    public function findAll() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        $query = "SELECT * FROM product p ORDER BY p.product_id DESC";
        $this->show_response($this->db->get_list($query));
    }

    /* ============================================================
       FIND ONE
       ============================================================ */
    public function findOnePlain(int $id): array {

        $query = "
            SELECT 
                p.*,
                IFNULL(i.stock_actual, 0) AS stock
            FROM product p
            LEFT JOIN inventario i 
                ON i.product_id = p.product_id
            WHERE p.product_id = $id
            LIMIT 1
        ";

        return $this->db->get_one($query);
    }


    public function findOne() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        if (!isset($this->_request['id'])) {
            $this->responseInvalidParam();
        }

        $id = (int)$this->_request['id'];
        $this->show_response($this->findOnePlain($id));
    }

    /* ============================================================
       COUNT (ADMIN)
       ============================================================ */
    public function allCountPlain(string $q, int $category_id): int {
        $query = "SELECT COUNT(DISTINCT p.id) FROM product p ";
        $keyword = "(p.name REGEXP '$q' OR p.status REGEXP '$q' OR p.description REGEXP '$q') ";

        if ($category_id !== -1) {
            $query .= ", product_category pc WHERE pc.product_id=p.id AND pc.category_id=$category_id ";
            if ($q !== "") {
                $query .= "AND $keyword";
            }
        } else {
            if ($q !== "") {
                $query .= "WHERE $keyword";
            }
        }

        return (int)$this->db->get_count($query);
    }

    /* ============================================================
       COUNT (CLIENT)
       ============================================================ */
    public function allCountPlainForClient(string $q, int $category_id): int {
        $query = "SELECT COUNT(DISTINCT p.product_id) FROM product p ";
        $keyword = "(p.name REGEXP '$q' OR p.status REGEXP '$q' OR p.description REGEXP '$q') ";

        if ($category_id !== -1) {
            $query .= ", product_category pc WHERE p.draft=0 AND pc.product_id=p.product_id AND pc.category_id=$category_id ";
            if ($q !== "") {
                $query .= "AND $keyword";
            }
        } else {
            if ($q !== "") {
                $query .= "WHERE p.draft=0 AND $keyword";
            }
        }

        return (int)$this->db->get_count($query);
    }

    public function allCount() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        $q = $this->_request['q'] ?? "";
        $category_id = isset($this->_request['category_id']) ? (int)$this->_request['category_id'] : -1;

        $this->show_response_plain(
            $this->allCountPlain($q, $category_id)
        );
    }

    /* ============================================================
       PAGINATION LIST
       ============================================================ */
    public function findAllByPagePlain(int $limit, int $offset, string $q, int $category_id): array {
        $query = "SELECT DISTINCT p.* FROM product p ";
        $keyword = "(p.name REGEXP '$q' OR p.status REGEXP '$q' OR p.description REGEXP '$q') ";

        if ($category_id !== -1) {
            $query .= ", product_category pc WHERE pc.product_id=p.product_id AND pc.category_id=$category_id ";
            if ($q !== "") {
                $query .= "AND $keyword";
            }
        } else {
            if ($q !== "") {
                $query .= "WHERE $keyword";
            }
        }

        $query .= "ORDER BY p.product_id DESC LIMIT $limit OFFSET $offset ";

        return $this->db->get_list($query);
    }

    

    /* ============================================================
       PAGINATION LIST (CLIENT)  ✅ AQUÍ ESTÁ LA SOLUCIÓN
       ============================================================ */
    public function findAllByPagePlainForClient(
        int $limit,
        int $offset,
        string $q,
        int $category_id
    ): array {

        $query = "
            SELECT DISTINCT 
                p.*,
                IFNULL(pi.name, '') AS image
            FROM product p
            LEFT JOIN product_image pi 
                ON pi.product_id = p.product_id
        ";

        $keyword = "(p.name REGEXP '$q' OR p.status REGEXP '$q' OR p.description REGEXP '$q') ";

        if ($category_id !== -1) {
            $query .= "
                INNER JOIN product_category pc 
                    ON pc.product_id = p.product_id
                WHERE p.draft = 0
                  AND pc.category_id = $category_id
            ";
            if ($q !== "") {
                $query .= " AND $keyword ";
            }
        } else {
            $query .= " WHERE p.draft = 0 ";
            if ($q !== "") {
                $query .= " AND $keyword ";
            }
        }

        $query .= " ORDER BY p.product_id DESC LIMIT $limit OFFSET $offset ";

        return $this->db->get_list($query);
    }


    public function findAllByPage() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        if (!isset($this->_request['limit']) || !isset($this->_request['page'])) {
            $this->responseInvalidParam();
        }

        $limit  = (int)$this->_request['limit'];
        $page   = (int)$this->_request['page'];
        $offset = $page - 1;

        $q = $this->_request['q'] ?? "";
        $category_id = isset($this->_request['category_id']) ? (int)$this->_request['category_id'] : -1;

        $this->show_response(
            $this->findAllByPagePlain($limit, $offset, $q, $category_id)
        );
    }

    /* ============================================================
       INSERT
       ============================================================ */
    public function insertOne() {
        if ($this->get_request_method() !== "POST") {
            $this->response('', 406);
        }

        $data = json_decode(file_get_contents("php://input") ?: "[]", true);
        if (!$data) {
            $this->responseInvalidParam();
        }

        $column_names = [
            'name','image','price','price_discount','stock',
            'draft','description','status','created_at','last_update'
        ];

        $table_name = 'product';
        $pk         = 'product_id';

        $resp = $this->db->post_one($data, $pk, $column_names, $table_name);

        $this->show_response($resp);
    }

    /* ============================================================
       UPDATE
       ============================================================ */
    public function updateOne() {
        if ($this->get_request_method() !== "POST") {
            $this->response('', 406);
        }

        $data = json_decode(file_get_contents("php://input") ?: "[]", true);

        if (!isset($data['id'])) {
            $this->responseInvalidParam();
        }

        $id  = (int)$data['id'];

        $column_names = [
            'name','image','price','price_discount','stock',
            'draft','description','status','created_at','last_update'
        ];

        $table_name = 'product';
        $pk         = 'product_id';

        $this->show_response(
            $this->db->post_update($id, $data, $pk, $column_names, $table_name)
        );
    }

    /* ============================================================
       DELETE
       ============================================================ */
    public function deleteOne() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        if (!isset($this->_request['id'])) {
            $this->responseInvalidParam();
        }

        $id = (int)$this->_request['id'];

        $table_name = 'product';
        $pk         = 'product_id';

        $this->show_response(
            $this->db->delete_one($id, $pk, $table_name)
        );
    }

    /* ============================================================
       COUNTS
       ============================================================ */
    public function countByDraftPlain(int $i): int {
        $query = "SELECT COUNT(DISTINCT p.product_id) FROM product p WHERE p.draft=$i";
        return (int)$this->db->get_count($query);
    }

    public function countByStatusPlain(string $status): int {
        $query = "SELECT COUNT(DISTINCT p.product_id) FROM product p WHERE p.status='$status'";
        return (int)$this->db->get_count($query);
    }
}
?>
