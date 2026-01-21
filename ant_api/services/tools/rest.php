<?php
declare(strict_types=1);

class REST {

    public array $_allow = [];
    public string $_content_type = "application/json";
    public array $_request = [];
    public array $_header = [];

    public string $_method = "";
    public int $_code = 200;

    public function __construct() {
        $this->inputs();
    }

    public function get_referer(): ?string {
        return $_SERVER['HTTP_REFERER'] ?? null;
    }

    public function response(string $data = "", int $status = 200): void {
        $this->_code = $status ?: 200;
        $this->set_headers();
        echo $data;
        exit;
    }

    private function get_status_message(): string {
        $status = [
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            404 => 'Not Found',
            406 => 'Not Acceptable',
            401 => 'Unauthorized'
        ];

        return $status[$this->_code] ?? $status[500] ?? "Internal Server Error";
    }

    public function get_request_method(): string {
        return $_SERVER['REQUEST_METHOD'] ?? "GET";
    }

    public function inputs(): void {
        $this->_header = $this->get_request_header();

        switch ($this->get_request_method()) {
            case "POST":
                $this->_request = $this->cleanInputs($_POST);
                break;

            case "GET":
            case "DELETE":
                $this->_request = $this->cleanInputs($_GET);
                break;

            case "PUT":
                parse_str(file_get_contents("php://input") ?: "", $this->_request);
                $this->_request = $this->cleanInputs($this->_request);
                break;

            default:
                $this->response('', 406);
                break;
        }
    }

    public function cleanInputs($data) {
        if (is_array($data)) {
            $clean = [];
            foreach ($data as $k => $v) {
                $clean[$k] = $this->cleanInputs($v);
            }
            return $clean;
        }

        // MAGIC QUOTES ya no existe en PHP 8 → eliminado.
        $data = strip_tags((string)$data);
        return trim($data);
    }

    public function get_request_header(): array {
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $formatted = ucwords(strtolower(str_replace('_', ' ', substr($key, 5))));
                $formatted = str_replace(' ', '', $formatted);
                $headers[$formatted] = $value;
            }
        }

        return $headers;
    }

    public function set_headers(): void {
        header("HTTP/1.1 {$this->_code} " . $this->get_status_message());
        header("Content-Type: {$this->_content_type}");
    }

    public function show_response($data): void {
        $this->response($this->json($data), 200);
    }

    public function show_response_plain(string $data): void {
        $this->response($data, 200);
    }

    public function json($data): string {
        return json_encode($data, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE);
    }

    public function responseInvalidParam(): void {
        $resp = [
            "status" => "Failed",
            "msg"    => "Invalid Parameter"
        ];
        $this->response($this->json($resp), 200);
    }
}
?>
