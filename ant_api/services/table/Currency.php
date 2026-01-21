<?php
declare(strict_types=1);

require_once realpath(dirname(__FILE__) . "/../tools/rest.php");

class Currency extends REST {

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

        $query = "SELECT * FROM currency cu";
        $this->show_response($this->db->get_list($query));
    }
}
?>
