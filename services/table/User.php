<?php
declare(strict_types=1);

require_once realpath(dirname(__FILE__) . "/../tools/rest.php");
require_once realpath(dirname(__FILE__) . "/../conf.php");

class User extends REST {

    private ?mysqli $mysqli = null;
    private $db = null;
    private $conf;

    public function __construct($db) {
        parent::__construct();
        $this->db   = $db;
        $this->mysqli = $db->mysqli;
        $this->conf = new CONF();
    }

    // ============================
    // 🔐 CHECK AUTHORIZATION
    // ============================
    public function checkAuthorization() {
        $resp = ["status" => 'Failed', "msg" => 'Unauthorized'];

        if (isset($this->_header['Token']) && !empty($this->_header['Token'])) {

            $token = $this->_header['Token'];
            $token = $this->mysqli->real_escape_string($token);

            $query = "SELECT id FROM user WHERE password='$token'";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows < 1) {
                $this->show_response($resp);
            }

        } else {
            $this->show_response($resp);
        }
    }

    // ============================
    // 🔐 LOGIN
    // ============================
    public function processLogin() {
        if ($this->get_request_method() !== "POST") {
            $this->response('', 406);
        }

        $customer = json_decode(file_get_contents("php://input"), true);

        if (!isset($customer['username'], $customer['password'])) {
            $this->show_response([
                'status' => "failed",
                'msg' => "Invalid username or password"
            ]);
        }

        $username = $this->mysqli->real_escape_string($customer['username']);
        $password = md5($customer['password']);

        $query = "
            SELECT id, name, username, email, password 
            FROM user 
            WHERE password = '$password' 
            AND username = '$username'
            LIMIT 1
        ";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = $r->fetch_assoc();
            $resp = ['status' => "success", "user" => $result];
            $this->show_response($resp);
        }

        $error = ['status' => "failed", "msg" => "Username or Password not found"];
        $this->show_response($error);
    }

    // ============================
    // 🔍 FIND ONE USER
    // ============================
    public function findOne() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        $id = (int) ($this->_request['id'] ?? 0);
        $query = "SELECT id, name, username, email FROM user WHERE id=$id";

        $this->show_response($this->db->get_one($query));
    }

    // ============================
    // 🔑 GET FIRST TOKEN
    // ============================
    public function findOneToken() {
        $query = "SELECT password FROM user LIMIT 1";
        return $this->db->get_one($query)['password'];
    }

    // ============================
    // ✏️ UPDATE USER
    // ============================
    public function updateOne() {
        if ($this->get_request_method() !== "POST") {
            $this->response('', 406);
        }

        // Demo protection
        if ($this->conf->DEMO_VERSION) {
            $m = [
                'status' => "failed",
                'msg' => "Ops, this is demo version",
                'data' => null
            ];
            $this->show_response($m);
        }

        $user = json_decode(file_get_contents("php://input"), true);

        if (!isset($user['id'])) {
            $this->responseInvalidParam();
        }

        $id = (int) $user['id'];
        $password = $user['user']['password'] ?? '';

        // If password is "*****", don't change it
        if ($password === '*****') {
            $column_names = ['id', 'name', 'username', 'email'];
        } else {
            $user['user']['password'] = md5($password);
            $column_names = ['id', 'name', 'username', 'email', 'password'];
        }

        $table_name = 'user';
        $pk = 'id';

        $resp = $this->db->post_update($id, $user, $pk, $column_names, $table_name);
        $this->show_response($resp);
    }

    // ============================
    // ➕ INSERT USER
    // ============================
    public function insertOne() {
        if ($this->get_request_method() !== "POST") {
            $this->response('', 406);
        }

        if ($this->conf->DEMO_VERSION) {
            $m = [
                'status' => "failed",
                'msg' => "Ops, this is demo version",
                'data' => null
            ];
            $this->show_response($m);
        }

        $user = json_decode(file_get_contents("php://input"), true);

        if (!isset($user['password'])) {
            $this->responseInvalidParam();
        }

        $user['password'] = md5($user['password']);

        $column_names = ['name', 'username', 'email', 'password'];
        $table_name = 'user';
        $pk = 'id';

        $resp = $this->db->post_one($user, $pk, $column_names, $table_name);
        $this->show_response($resp);
    }

}
?>
