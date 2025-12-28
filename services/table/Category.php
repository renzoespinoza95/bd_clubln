<?php
declare(strict_types=1);

require_once realpath(dirname(__FILE__) . "/../tools/rest.php");

class Category extends REST {

    private ?mysqli $mysqli = null;
    private $db = null;

    public function __construct($db) {
        parent::__construct();
        $this->db     = $db;
        $this->mysqli = $db->mysqli;
    }

    /* ============================================================
       GET ALL (ADMIN)
       ============================================================ */
    public function findAll() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        $query = "SELECT * FROM category c ORDER BY c.priority ASC";
        $this->show_response($this->db->get_list($query));
    }

    /* ============================================================
       GET ALL (CLIENT)
       ============================================================ */
    public function findAllForClient(): array {
        $query = "SELECT * FROM category c WHERE c.draft = 0 ORDER BY c.priority ASC";
        return $this->db->get_list($query);
    }

    /* ============================================================
       GET ONE
       ============================================================ */
    public function findOne() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        if (!isset($this->_request['id'])) {
            $this->responseInvalidParam();
        }

        $id = (int)$this->_request['id'];
        $query = "SELECT DISTINCT * FROM category c WHERE c.id = $id";

        $this->show_response($this->db->get_one($query));
    }

    /* ============================================================
       COUNT
       ============================================================ */
    public function allCountPlain(string $q, int $client): int {
        $query = "SELECT COUNT(DISTINCT c.id) FROM category c ";
        $keyword = "(c.name REGEXP '$q' OR c.brief REGEXP '$q') ";

        if ($client !== 0) {
            $query .= "WHERE c.draft <> 1 ";
            if ($q !== "") $query .= "AND $keyword";
        } else {
            if ($q !== "") $query .= "WHERE $keyword";
        }

        return (int) $this->db->get_count($query);
    }

    public function allCount() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        $q      = $this->_request['q'] ?? "";
        $client = isset($this->_request['client']) ? (int)$this->_request['client'] : 0;

        $this->show_response_plain($this->allCountPlain($q, $client));
    }

    /* ============================================================
       PAGINATION
       ============================================================ */
    public function findAllByPagePlain(int $limit, int $offset, string $q, int $client): array {
        $query = "SELECT c.* FROM category c ";
        $keyword = "(c.name REGEXP '$q' OR c.brief REGEXP '$q') ";

        if ($client !== 0) {
            $query .= "WHERE c.draft <> 1 ";
            if ($q !== "") $query .= "AND $keyword";
        } else {
            if ($q !== "") $query .= "WHERE $keyword";
        }

        $query .= "ORDER BY c.id DESC LIMIT $limit OFFSET $offset";

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

        $q      = $this->_request['q'] ?? "";
        $client = isset($this->_request['client']) ? (int)$this->_request['client'] : 0;

        $this->show_response($this->findAllByPagePlain($limit, $offset, $q, $client));
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
            'name','icon','draft','brief','color',
            'priority','created_at','last_update'
        ];

        $table_name = 'category';
        $pk = 'id';

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

        $id = (int)$data['id'];

        $column_names = [
            'name','icon','draft','brief','color',
            'priority','created_at','last_update'
        ];

        $table_name = 'category';
        $pk = 'id';

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

        $table_name = 'category';
        $pk = 'id';

        $this->show_response($this->db->delete_one($id, $pk, $table_name));
    }

    /* ============================================================
       CATEGORY BY PRODUCT
       ============================================================ */
    public function getAllByProductIdPlain(int $product_id): array {
        $query = "
            SELECT DISTINCT c.* 
            FROM category c 
            WHERE c.id IN (
                SELECT pc.category_id 
                FROM product_category pc 
                WHERE pc.product_id = $product_id
            )
        ";
        return $this->db->get_list($query);
    }

    public function getAllByProductId() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        if (!isset($this->_request['product_id'])) {
            $this->responseInvalidParam();
        }

        $product_id = (int)$this->_request['product_id'];

        $this->show_response($this->getAllByProductIdPlain($product_id));
    }

    /* ============================================================
       COUNT BY DRAFT
       ============================================================ */
    public function countByDraftPlain(int $i): int {
        $query = "SELECT COUNT(DISTINCT c.id) FROM category c WHERE c.draft = $i";
        return (int)$this->db->get_count($query);
    }
}
?>
