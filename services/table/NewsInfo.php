<?php
declare(strict_types=1);

require_once realpath(dirname(__FILE__) . "/../tools/rest.php");

class NewsInfo extends REST {

    private ?mysqli $mysqli = null;
    private $db = null; 

    public function __construct($db) {
        parent::__construct();
        $this->db     = $db;
        $this->mysqli = $db->mysqli;
    }

    /* ============================================================
       GET ALL NEWS (ADMIN)
       ============================================================ */
    public function findAll() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }
        $this->show_response($this->findAllPlain());
    }

    public function findAllPlain(): array {
        $query = "SELECT * FROM news_info ni ORDER BY ni.id DESC";
        return $this->db->get_list($query);
    }

    /* ============================================================
       GET ONE
       ============================================================ */
    public function findOnePlain(int $id): array {
        $query = "SELECT DISTINCT * FROM news_info ni WHERE ni.id = $id";
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
       PAGINATION COUNT
       ============================================================ */
    public function allCountPlain(string $q, int $client): int {
        $query = "SELECT COUNT(DISTINCT ni.id) FROM news_info ni ";
        $keyword = "(ni.title REGEXP '$q' OR ni.brief_content REGEXP '$q' OR ni.full_content REGEXP '$q') ";

        if ($client !== 0) {
            $query .= "WHERE ni.draft <> 1 ";
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

    public function allCount() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        $q      = $this->_request['q'] ?? "";
        $client = isset($this->_request['client']) ? (int)$this->_request['client'] : 0;

        $this->show_response_plain($this->allCountPlain($q, $client));
    }

    /* ============================================================
       PAGINATION LIST
       ============================================================ */
    public function findAllByPagePlain(int $limit, int $offset, string $q, int $client): array {
        $query = "SELECT ni.* FROM news_info ni ";
        $keyword = "(ni.title REGEXP '$q' OR ni.brief_content REGEXP '$q' OR ni.full_content REGEXP '$q') ";

        if ($client !== 0) {
            $query .= "WHERE ni.draft <> 1 ";
            if ($q !== "") {
                $query .= "AND $keyword";
            }
        } else {
            if ($q !== "") {
                $query .= "WHERE $keyword";
            }
        }

        $query .= "ORDER BY ni.id DESC LIMIT $limit OFFSET $offset ";

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

        $this->show_response(
            $this->findAllByPagePlain($limit, $offset, $q, $client)
        );
    }

    /* ============================================================
       INSERT ONE NEWS
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
            'title','brief_content','full_content','image',
            'draft','status','created_at','last_update'
        ];

        $table_name = 'news_info';
        $pk         = 'id';

        // Validation: Featured limit
        if ($data['status'] === 'FEATURED' && $data['draft'] == 0 && $this->isFeaturedExceed() == 1) {
            $this->show_response([
                'status' => "failed",
                'msg'    => "Featured News exceed the maximum amount",
                'data'   => null
            ]);
            return;
        }

        $resp = $this->db->post_one($data, $pk, $column_names, $table_name);

        $this->show_response($resp);
    }

    /* ============================================================
       UPDATE ONE NEWS
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
        $table_name = 'news_info';
        $pk         = 'id';

        $column_names = [
            'title','brief_content','full_content','image',
            'draft','status','created_at','last_update'
        ];

        // Validation 1: If FEATURED, check max limit
        if (
            $data[$table_name]['status'] === 'FEATURED' &&
            $data[$table_name]['draft'] == 0 &&
            $this->isFeaturedExceed() == 1
        ) {
            $this->show_response([
                'status' => "failed",
                'msg'    => "Featured News exceed the maximum amount",
                'data'   => null
            ]);
            return;
        }

        // Validation 2: If NORMAL, ensure at least one FEATURED remains
        if (
            $data[$table_name]['status'] === 'NORMAL' &&
            $this->countFeaturedPlain() <= 1
        ) {
            $this->show_response([
                'status' => "failed",
                'msg'    => "Ops, At least there is one FEATURED news",
                'data'   => null
            ]);
            return;
        }

        $this->show_response(
            $this->db->post_update($id, $data, $pk, $column_names, $table_name)
        );
    }

    /* ============================================================
       DELETE NEWS
       ============================================================ */
    public function deleteOne() {
        if ($this->get_request_method() !== "GET") {
            $this->response('', 406);
        }

        if (!isset($this->_request['id'])) {
            $this->responseInvalidParam();
        }

        $id    = (int)$this->_request['id'];
        $data  = $this->findOnePlain($id);

        // Prevent deleting last FEATURED article
        if (
            ($data['status'] ?? '') === 'FEATURED' &&
            $this->countFeaturedPlain() <= 1
        ) {
            $this->show_response([
                'status' => "failed",
                'msg'    => "Ops, At least there is one FEATURED news",
                'data'   => null
            ]);
            return;
        }

        $table_name = 'news_info';
        $pk         = 'id';

        $this->show_response(
            $this->db->delete_one($id, $pk, $table_name)
        );
    }

    /* ============================================================
       FEATURED NEWS
       ============================================================ */
    public function findAllFeatured(): array {
        $query = "
            SELECT *
            FROM news_info ni 
            WHERE ni.status='FEATURED' 
              AND ni.draft = 0
            ORDER BY ni.id DESC
        ";

        return $this->db->get_list($query);
    }

    public function countByDraftPlain(int $i): int {
        $query = "SELECT COUNT(DISTINCT ni.id) FROM news_info ni WHERE ni.draft = $i";
        return (int)$this->db->get_count($query);
    }

    public function countFeaturedPlain(): int {
        $query = "
            SELECT COUNT(DISTINCT ni.id)
            FROM news_info ni 
            WHERE ni.status = 'FEATURED' 
              AND ni.draft = 0
        ";
        return (int)$this->db->get_count($query);
    }

    /* ============================================================
       FEATURED MAX LIMIT CHECK
       ============================================================ */
    public function isFeaturedExceed(): int {
        $key_code = 'FEATURED_NEWS';

        $query = "
            SELECT COUNT(counter) AS resp
            FROM (
                SELECT COUNT(id) AS counter
                FROM news_info
                WHERE status='FEATURED' AND draft=0
            ) AS N
            WHERE N.counter >= (
                SELECT value FROM config WHERE code='$key_code'
            )
        ";

        $row = $this->db->get_one($query);
        return (int)($row['resp'] ?? 0);
    }

    public function isFeaturedNewsExceed() {
        $this->show_response_plain($this->isFeaturedExceed());
    }
}
?>
