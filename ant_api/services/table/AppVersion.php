<?php
declare(strict_types=1);

require_once realpath(dirname(__FILE__) . "/../tools/rest.php");

class AppVersion extends REST {

    private ?mysqli $mysqli = null;
    private ?DB $db = null;

    public function __construct($db) {
        parent::__construct();
        $this->db     = $db;
        $this->mysqli = $db->mysqli;
    }

    /* ============================================================
       GET ALL
       ============================================================ */
    public function findAll() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }
        $this->show_response($this->findAllPlain());
    }

    public function findAllPlain(): array {
        $query = "SELECT * FROM app_version a ORDER BY a.id DESC";
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

        $id   = (int)$this->_request['id'];
        $resp = $this->findOnePlain($id);

        $this->show_response($resp);
    }

    public function findOnePlain(int $id): array {
        $query = "SELECT * FROM app_version a WHERE a.id = $id LIMIT 1";
        return $this->db->get_one($query);
    }

    /* ============================================================
       COUNT
       ============================================================ */
    public function allCount() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        $q = $this->_request['q'] ?? "";

        if ($q !== "") {
            $query =
                "SELECT COUNT(DISTINCT a.id) 
                 FROM app_version a 
                 WHERE version_code REGEXP '$q'
                    OR version_name REGEXP '$q'";
        } else {
            $query = "SELECT COUNT(DISTINCT a.id) FROM app_version a";
        }

        $this->show_response_plain($this->db->get_count($query));
    }

    /* ============================================================
       PAGINATION
       ============================================================ */
    public function findAllByPage() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        if (!isset($this->_request['limit']) || !isset($this->_request['page'])) {
            $this->responseInvalidParam();
        }

        $limit  = (int)$this->_request['limit'];
        $page   = (int)$this->_request['page'];
        $offset = ($page - 1);

        $q = $this->_request['q'] ?? "";

        if ($q !== "") {
            $query =
                "SELECT a.* 
                 FROM app_version a 
                 WHERE version_code REGEXP '$q'
                    OR version_name REGEXP '$q'
                 ORDER BY a.id DESC 
                 LIMIT $limit OFFSET $offset";
        } else {
            $query =
                "SELECT a.* 
                 FROM app_version a 
                 ORDER BY a.id DESC 
                 LIMIT $limit OFFSET $offset";
        }

        $this->show_response($this->db->get_list($query));
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

        $column_names = ['version_code', 'version_name', 'active', 'created_at', 'last_update'];
        $table_name   = 'app_version';
        $pk           = 'id';

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

        $column_names = ['version_code', 'version_name', 'active', 'created_at', 'last_update'];
        $table_name   = 'app_version';
        $pk           = 'id';

        // Prevent disabling last active version
        $active = $data[$table_name]['active'] ?? null;

        if ($active === 0 && $this->countActiveVersion() <= 1) {
            $this->show_response([
                'status' => "failed",
                "msg"    => "Ops, at least one active app version must remain",
                "data"   => null
            ]);
            return;
        }

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

        $id         = (int)$this->_request['id'];
        $table_name = 'app_version';
        $pk         = 'id';

        $data = $this->findOnePlain($id);

        if (($data['active'] ?? 0) == 1 && $this->countActiveVersion() <= 1) {

            $this->show_response([
                'status' => "failed",
                "msg"    => "Ops, at least one active app version must remain",
                "data"   => null
            ]);
            return;
        }

        $this->show_response(
            $this->db->delete_one($id, $pk, $table_name)
        );
    }

    /* ============================================================
       HELPERS
       ============================================================ */
    public function countActiveVersion(): int {
        $query = "SELECT COUNT(DISTINCT a.id) FROM app_version a WHERE a.active = 1";
        return (int)$this->db->get_count($query);
    }

    public function countInactiveVersion(): int {
        $query = "SELECT COUNT(DISTINCT a.id) FROM app_version a WHERE a.active = 0";
        return (int)$this->db->get_count($query);
    }
}
?>
