<?php
declare(strict_types=1);

require_once realpath(dirname(__FILE__) . "/../tools/rest.php");

class Config extends REST {

    private ?mysqli $mysqli = null;
    private $db = null;

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
        $query = "SELECT * FROM config cn";
        return $this->db->get_list($query);
    }

    public function findAllArr(): array {
        $query = "SELECT * FROM config cn";
        return $this->db->get_list($query);
    }

    /* ============================================================
       UPDATE ALL CONFIG
       ============================================================ */
    public function updateAll() {
        if ($this->get_request_method() !== "POST") {
            $this->response('', 406);
        }

        $config = json_decode(file_get_contents("php://input") ?: "[]", true);

        if (!$config) {
            $this->responseInvalidParam();
        }

        $column_names = ['code', 'value'];
        $table_name   = 'config';
        $pk           = 'code';

        $resp = $this->db->update_array_pk_str($pk, $config, $column_names, $table_name);

        $this->show_response($resp);
    }
}
?>
