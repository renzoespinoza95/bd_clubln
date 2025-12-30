<?php
declare(strict_types=1);

require_once realpath(dirname(__FILE__) . "/../../conf.php");

class DB {

    public ?mysqli $mysqli = null;
    public ?CONF $conf = null;

    public function __construct() {
        $this->conf = new CONF(); 
        $this->dbConnect(); 
    }

    /* ======================== Conexion ======================== */

    private function dbConnect(): void {
        $this->mysqli = @new mysqli(
            $this->conf->DB_SERVER,
            $this->conf->DB_USER,
            $this->conf->DB_PASSWORD,
            $this->conf->DB_NAME
        );

        if ($this->mysqli->connect_errno) {
            throw new Exception("Error de conexión: " . $this->mysqli->connect_error);
        }

        $this->mysqli->set_charset("utf8mb4");
    }

    public function reConnect(): self {
        $this->dbConnect();
        return $this;
    }

    /* ======================== Checker ======================== */

    public function checkResponse_Impl(): void {
        echo mysqli_ping($this->mysqli)
            ? "Database Connection : Success"
            : "Database Connection : Error";
    }

    public function real_escape(string $s): string {
        return $this->mysqli->real_escape_string($s);
    }

    /* ======================== Utils ======================== */

    public function get_list(string $query): array {
        $result = [];
        $r = $this->mysqli->query($query);

        if (!$r) {
            throw new Exception($this->mysqli->error);
        }

        while ($row = $r->fetch_assoc()) {
            $result[] = $row;
        }
        return $result;
    }

    public function get_one(string $query): array {
        $r = $this->mysqli->query($query);

        if (!$r) {
            throw new Exception($this->mysqli->error);
        }

        return ($r->num_rows > 0) ? $r->fetch_assoc() : [];
    }

    public function get_count(string $query): int {
        $r = $this->mysqli->query($query);

        if (!$r) {
            throw new Exception($this->mysqli->error);
        }

        if ($r->num_rows > 0) {
            [$count] = $r->fetch_row();
            return (int)$count;
        }

        return 0;
    }

    /* ======================== INSERT ONE ======================== */

    public function post_one(array $obj, string $pk, array $column_names, string $table_name): array {
        if (empty($obj)) {
            return ['status' => 'failed', 'msg' => 'No data', 'data' => []];
        }

        $columns = [];
        $values = [];

        foreach ($column_names as $col) {
            $val = $obj[$col] ?? '';
            $columns[] = $col;
            $values[] = "'" . $this->real_escape((string)$val) . "'";
        }

        $query = "INSERT INTO $table_name (" . implode(",", $columns) . ") VALUES (" . implode(",", $values) . ")";

        if ($this->mysqli->query($query)) {
            $last_id = $this->mysqli->insert_id;

            $get_query = "SELECT * FROM $table_name WHERE $pk = $last_id";
            $r = $this->mysqli->query($get_query);

            if ($r && $r->num_rows > 0) {
                $obj = $r->fetch_assoc();
            }

            return [
                'status' => 'success',
                'msg' => "$table_name created successfully",
                'data' => $obj
            ];
        }

        return [
            'status' => 'failed',
            'msg' => $this->mysqli->error,
            'data' => $obj
        ];
    }

    /* ======================== INSERT MULTIPLE ======================== */

    public function post_array(array $obj_array, array $column_names, string $table_name): array {
        if (empty($obj_array)) {
            return ['status' => 'failed', 'msg' => 'Empty array', 'data' => []];
        }

        $query = "";

        foreach ($obj_array as $obj) {
            $cols = [];
            $vals = [];

            foreach ($column_names as $col) {
                $val = $obj[$col] ?? '';
                $cols[] = $col;
                $vals[] = "'" . $this->real_escape((string)$val) . "'";
            }

            $query .= "INSERT INTO $table_name (" . implode(",", $cols) . ") VALUES (" . implode(",", $vals) . ");";
        }

        if ($this->mysqli->multi_query($query)) {
            return [
                'status' => 'success',
                'msg' => "$table_name created successfully",
                'data' => $obj_array
            ];
        }

        return [
            'status' => 'failed',
            'msg' => $this->mysqli->error,
            'data' => $obj_array
        ];
    }

    /* ======================== UPDATE MULTIPLE ======================== */

    public function update_array(string $pk, array $obj_array, array $column_names, string $table_name): array {
        if (empty($obj_array)) {
            return ['status' => 'failed', 'msg' => 'Empty array', 'data' => []];
        }

        $query = "";

        foreach ($obj_array as $obj) {
            $pk_value = $obj[$pk];
            $sets = [];

            foreach ($column_names as $col) {
                $val = $obj[$col] ?? '';
                $sets[] = "$col='" . $this->real_escape((string)$val) . "'";
            }

            $query .= "UPDATE $table_name SET " . implode(",", $sets) . " WHERE $pk=$pk_value;";
        }

        if ($this->mysqli->multi_query($query)) {
            return [
                'status' => 'success',
                'msg' => "$table_name update successfully",
                'data' => $obj_array
            ];
        }

        return [
            'status' => 'failed',
            'msg' => $this->mysqli->error,
            'data' => $obj_array
        ];
    }

    public function update_array_pk_str(string $pk, array $obj_array, array $column_names, string $table_name): array {
        if (empty($obj_array)) {
            return ['status' => 'failed', 'msg' => 'Empty array', 'data' => []];
        }

        $query = "";

        foreach ($obj_array as $obj) {
            $pk_value = $obj[$pk];
            $sets = [];

            foreach ($column_names as $col) {
                $val = $obj[$col] ?? '';
                $sets[] = "$col='" . $this->real_escape((string)$val) . "'";
            }

            $query .= "UPDATE $table_name SET " . implode(",", $sets) . " WHERE $pk='$pk_value';";
        }

        if ($this->mysqli->multi_query($query)) {
            return [
                'status' => 'success',
                'msg' => "$table_name update successfully",
                'data' => $obj_array
            ];
        }

        return [
            'status' => 'failed',
            'msg' => $this->mysqli->error,
            'data' => $obj_array
        ];
    }

    /* ======================== UPDATE ONE ======================== */

    public function post_update(int $id, array $obj, string $pk, array $column_names, string $table_name): array {
        $data = $obj[$table_name] ?? [];
        $sets = [];

        foreach ($column_names as $col) {
            $val = $data[$col] ?? '';
            $sets[] = "$col='" . $this->real_escape((string)$val) . "'";
        }

        $query = "UPDATE $table_name SET " . implode(",", $sets) . " WHERE $pk=$id";

        if ($this->mysqli->query($query)) {
            return [
                'status' => 'success',
                'msg' => "$table_name update successfully",
                'data' => $obj
            ];
        }

        return [
            'status' => 'failed',
            'msg' => $this->mysqli->error,
            'data' => $obj
        ];
    }

    /* ======================== DELETE ======================== */

    public function delete_one(int $id, string $pk, string $table_name): array {
        $query = "DELETE FROM $table_name WHERE $pk = $id";

        if ($this->mysqli->query($query)) {
            return ['status' => 'success', 'msg' => "Record deleted"];
        }

        return ['status' => 'failed', 'msg' => $this->mysqli->error];
    }

    public function delete_one_str(string $pkval, string $pk, string $table_name): array {
        $query = "DELETE FROM $table_name WHERE $pk = '$pkval'";

        if ($this->mysqli->query($query)) {
            return ['status' => 'success', 'msg' => "Record deleted"];
        }

        return ['status' => 'failed', 'msg' => $this->mysqli->error];
    }
}
?>
