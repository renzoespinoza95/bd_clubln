<?php
// ===========================================================
// Rutas MSG (FlightPHP + MeekroDB2) — santo_id como ENUM string
// ===========================================================

// (Opcional) vista de inicio
Flight::route('GET /msg/inicio', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $path_public;
    include $path_public . '/admin/tab_msg/inicio.php';
});

// Lista blanca del ENUM (valores EXACTOS en BD)
function _santo_allowed(): array {
    return ['santa rosa','sr de los milagros','san martin de porres'];
}
// Etiqueta bonita para mostrar junto a la tabla
function _santo_label(?string $v): ?string {
    if ($v === null) return null;
    $map = [
        'santa rosa'           => 'Santa Rosa',
        'sr de los milagros'   => 'Sr de los Milagros',
        'san martin de porres' => 'San Martín de Porres',
    ];
    return $map[$v] ?? $v;
}

// GET /msg/listar
Flight::route('GET /msg/listar', function() {
    DB::query("SET NAMES 'utf8'");
    $rows = DB::query("SELECT * FROM msg ORDER BY msg_id DESC");
    foreach ($rows as &$r) {
        $r['santo_nombre'] = _santo_label(isset($r['santo_id']) ? $r['santo_id'] : null);
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
});

// POST /msg/crear
// Recibe: usu_nom, contenido_rem (HTML summernote), is_valido (0/1), santo_id (enum string), me_gusta (int)
Flight::route('POST /msg/crear', function() {
    $p = Flight::request()->data->getData();

    $usu_nom       = isset($p['usu_nom'])       ? trim($p['usu_nom']) : '';
    $contenido_rem = isset($p['contenido_rem']) ? (string)$p['contenido_rem'] : '';
    $is_valido     = isset($p['is_valido'])     ? intval($p['is_valido']) : 0;
    $santo_id      = isset($p['santo_id'])      ? trim((string)$p['santo_id']) : null;
    $me_gusta      = isset($p['me_gusta'])      ? intval($p['me_gusta']) : 0;

    if ($usu_nom === '' || trim(strip_tags($contenido_rem)) === '') {
        Flight::json(['status'=>'error','msg'=>'Faltan datos obligatorios'], 400);
        return;
    }
    if ($santo_id !== null && !in_array($santo_id, _santo_allowed(), true)) {
        Flight::json(['status'=>'error','msg'=>'santo_id inválido'], 400);
        return;
    }

    DB::insert('msg', [
        'usu_nom'        => $usu_nom,
        'contenido_rem'  => $contenido_rem,
        'fecha_creacion' => date('Y-m-d H:i:s'),
        'santo_id'       => $santo_id,   // ENUM string o NULL
        'me_gusta'       => $me_gusta,
        'is_valido'      => $is_valido
    ]);

    Flight::json(['status'=>'ok','id'=>DB::insertId()]);
});

// POST /msg/editar
Flight::route('POST /msg/editar', function() {
    $p = Flight::request()->data->getData();
    $id = isset($p['msg_id']) ? intval($p['msg_id']) : 0;
    if ($id<=0) { Flight::json(['status'=>'error','msg'=>'ID inválido'], 400); return; }

    $row = DB::queryFirstRow("SELECT * FROM msg WHERE msg_id=%i", $id);
    if (!$row) { Flight::json(['status'=>'error','msg'=>'No existe'], 404); return; }

    $usu_nom       = isset($p['usu_nom'])       ? trim($p['usu_nom']) : $row['usu_nom'];
    $contenido_rem = array_key_exists('contenido_rem',$p) ? (string)$p['contenido_rem'] : $row['contenido_rem'];
    $is_valido     = isset($p['is_valido'])     ? intval($p['is_valido']) : $row['is_valido'];
    $santo_id      = array_key_exists('santo_id',$p) ? (strlen(trim($p['santo_id']))? trim((string)$p['santo_id']) : null) : $row['santo_id'];
    $me_gusta      = isset($p['me_gusta'])      ? intval($p['me_gusta']) : $row['me_gusta'];

    if ($usu_nom === '' || trim(strip_tags($contenido_rem)) === '') {
        Flight::json(['status'=>'error','msg'=>'Datos inválidos'], 400);
        return;
    }
    if ($santo_id !== null && !in_array($santo_id, _santo_allowed(), true)) {
        Flight::json(['status'=>'error','msg'=>'santo_id inválido'], 400);
        return;
    }

    DB::update('msg', [
        'usu_nom'       => $usu_nom,
        'contenido_rem' => $contenido_rem,
        'is_valido'     => $is_valido,
        'santo_id'      => $santo_id,   // ENUM string o NULL
        'me_gusta'      => $me_gusta
    ], "msg_id=%i", $id);

    Flight::json(['status'=>'ok']);
});

// POST /msg/eliminar
Flight::route('POST /msg/eliminar', function() {
    $p = Flight::request()->data->getData();
    $id = isset($p['msg_id']) ? intval($p['msg_id']) : 0;
    if ($id<=0) { Flight::json(['status'=>'error','msg'=>'ID inválido'], 400); return; }

    DB::delete('msg', "msg_id=%i", $id);
    Flight::json(['status'=>'ok']);
});



// ===== Opciones (puedes moverlo a config) =====
const USE_OPENAI_MODERATION = false;         // true para evaluar casos dudosos con un servicio externo
const OPENAI_API_KEY        = '';            // si lo usas, coloca tu API key
const MAX_LEN               = 1000;          // longitud máx de comentario
const REACTION_MAX_LEN      = 50;            // longitud máx para tratar como reacción
const RATE_LIMIT_MAX_5MIN   = 10;            // máx 10 intentos cada 5 min por IP

// ===== Helpers de moderación =====

function normalize_text(string $t): string {
  $t = trim($t);
  $t = strip_tags($t);
  $t = preg_replace('/\s+/u', ' ', $t);
  return $t;
}

function is_valid_santo(string $santo): bool {
  return in_array($santo, ['santa_rosa','senor_milagros','san_martin'], true);
}

function is_amen_reaction(string $t): bool {
  if (mb_strlen($t) > REACTION_MAX_LEN) return false;
  // “amen” / “amén” + opcional emojis o signos suaves
  return (bool)preg_match('/^\s*a(m|é)ne?s?(\s*[!,.]?\s*)?([🙏🕊️✝️💜❤️✨⭐️]+)?\s*$/iu', $t);
}

function has_blocked_entities(string $t): bool {
  // links / teléfonos / correos / spam evidente
  if (preg_match('/https?:\/\/|www\./i', $t)) return true;
  if (preg_match('/\b[\w\.-]+@[\w\.-]+\.\w{2,}\b/u', $t)) return true;      // emails
  if (preg_match('/(\+?\d[\d\-\s]{7,})/u', $t)) return true;               // teléfonos
  // lista negra (pon tus términos reales)
  $blocked = ['insulto1','insulto2','amenaza1','odio1'];
  foreach ($blocked as $w) {
    if ($w && mb_stripos($t, $w) !== false) return true;
  }
  return false;
}

function looks_borderline(string $t): bool {
  // gritos, exceso signos, mayúsculas largas, palabrotas suaves
  if (preg_match('/[A-ZÁÉÍÓÚÑ]{6,}/u', $t)) return true;
  if (preg_match('/!{3,}|\?{3,}/u', $t)) return true;
  $mild = ['tonto','estupido','idiota']; // ejemplo leve
  foreach ($mild as $w) {
    if ($w && mb_stripos($t, $w) !== false) return true;
  }
  return false;
}

function classify_tipo(string $t): string {
  return (mb_strlen($t) <= 140) ? 'peticion_breve' : 'peticion_larga';
}

function moderation_score_external(string $t): float {
  if (!USE_OPENAI_MODERATION) return 0.0;
  // Aquí podrías llamar a OpenAI/Google Perspective/etc.
  // Devuelve 0..1 (más alto = más riesgoso).
  // Placeholder seguro:
  return 0.0;
}

function rate_limit_check(string $ip): bool {
  // máx RATE_LIMIT_MAX_5MIN comentarios intentados en últimos 5 minutos
  $desde = date('Y-m-d H:i:s', time() - 300);
  $cnt = DB::queryFirstField(
    "SELECT COUNT(*) FROM comentarios_log WHERE ip=%s AND created_at >= %s",
    $ip, $desde
  );
  return ($cnt < RATE_LIMIT_MAX_5MIN);
}

function rate_limit_touch(string $ip): void {
  DB::insert('comentarios_log', ['ip' => $ip]);
}

// ===== Endpoint: crear comentario / reacción =====
Flight::route('POST /api/comentarios', function () {
  try {
    DB::query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
    $req  = Flight::request();
    $ip   = $req->ip;
    $ua   = substr($req->user_agent ?? '', 0, 255);

    if (!rate_limit_check($ip)) {
      Flight::json(['ok'=>false, 'reason'=>'Demasiadas solicitudes. Inténtalo luego.'], 429);
      return;
    }
    rate_limit_touch($ip);

    $body  = json_decode($req->getBody(), true) ?: [];
    $texto = normalize_text((string)($body['texto'] ?? ''));
    $santo = (string)($body['santo'] ?? '');

    if (!is_valid_santo($santo)) {
      Flight::json(['ok'=>false, 'reason'=>'Santo no permitido.'], 422);
      return;
    }
    if ($texto === '' || mb_strlen($texto) > MAX_LEN) {
      Flight::json(['ok'=>false, 'reason'=>'Longitud inválida.'], 422);
      return;
    }
    if (has_blocked_entities($texto)) {
      // oculto directo por spam/privacidad
      DB::insert('comentarios', [
        'santo'      => $santo,
        'texto'      => $texto,
        'tipo'       => 'peticion_breve',
        'estado'     => 'oculto',
        'riesgo'     => 1.00,
        'motivo'     => 'spam_privacidad',
        'ip'         => $ip,
        'user_agent' => $ua
      ]);
      Flight::json(['ok'=>false, 'review'=>'oculto_spam'], 202);
      return;
    }

    // reacción “amén”
    if (is_amen_reaction($texto)) {
      DB::insert('comentarios', [
        'santo'      => $santo,
        'texto'      => $texto,
        'tipo'       => 'reaccion',
        'estado'     => 'aprobado',
        'riesgo'     => 0.00,
        'motivo'     => 'amen'
      ]);
      // opcional: devolver conteo actualizado
      $count = DB::queryFirstField("SELECT COUNT(*) FROM comentarios WHERE santo=%s AND tipo='reaccion' AND estado='aprobado'", $santo);
      Flight::json(['ok'=>true, 'as'=>'reaccion', 'amen_count'=>$count], 200);
      return;
    }

    // clasificación simple por longitud
    $tipo = classify_tipo($texto);

    // casos dudosos
    if (looks_borderline($texto)) {
      $score = moderation_score_external($texto); // 0..1
      if ($score >= 0.85) {
        DB::insert('comentarios', [
          'santo'  => $santo,
          'texto'  => $texto,
          'tipo'   => $tipo,
          'estado' => 'oculto',
          'riesgo' => $score,
          'motivo' => 'alto_riesgo',
          'ip'     => $ip,
          'user_agent' => $ua
        ]);
        Flight::json(['ok'=>false, 'review'=>'alto_riesgo'], 202);
        return;
      }
      if ($score >= 0.60) {
        DB::insert('comentarios', [
          'santo'  => $santo,
          'texto'  => $texto,
          'tipo'   => $tipo,
          'estado' => 'en_revision',
          'riesgo' => $score,
          'motivo' => 'revisar',
          'ip'     => $ip,
          'user_agent' => $ua
        ]);
        Flight::json(['ok'=>true, 'flag'=>'en_revision', 'tipo'=>$tipo], 201);
        return;
      }
    }

    // publicar normal (aprobado)
    DB::insert('comentarios', [
      'santo'      => $santo,
      'texto'      => $texto,
      'tipo'       => $tipo,
      'estado'     => 'aprobado',
      'riesgo'     => 0.00,
      'motivo'     => null,
      'ip'         => $ip,
      'user_agent' => $ua
    ]);

    Flight::json(['ok'=>true, 'tipo'=>$tipo, 'estado'=>'aprobado'], 201);

  } catch (\Throwable $e) {
    Flight::json(['ok'=>false, 'error'=>$e->getMessage()], 500);
  }
});

// ===== Endpoint: conteo de “amén” por santo =====
Flight::route('GET /api/comentarios/:santo/amen', function($santo) {
  if (!is_valid_santo($santo)) {
    Flight::json(['ok'=>false,'reason'=>'Santo no permitido.'],422); return;
  }
  $count = DB::queryFirstField("SELECT COUNT(*) FROM comentarios WHERE santo=%s AND tipo='reaccion' AND estado='aprobado'", $santo);
  Flight::json(['ok'=>true,'santo'=>$santo,'amen_count'=>$count]);
});

// ===== Endpoint: listar pendientes de revisión =====
Flight::route('GET /api/moderacion/pendientes', function(){
  $rows = DB::query("SELECT id, santo, LEFT(texto, 300) AS texto, tipo, riesgo, motivo, created_at
                     FROM comentarios WHERE estado='en_revision' ORDER BY created_at DESC LIMIT 100");
  Flight::json(['ok'=>true, 'items'=>$rows]);
});

// ===== Endpoint: aprobar/ocultar después de revisión humana =====
Flight::route('POST /api/moderacion/:id/(aprobar|ocultar)', function($id, $accion){
  $id = (int)$id;
  $estado = ($accion === 'aprobar') ? 'aprobado' : 'oculto';
  DB::update('comentarios', ['estado'=>$estado], 'id=%i', $id);
  Flight::json(['ok'=>true,'id'=>$id,'nuevo_estado'=>$estado]);
});

// ===== Endpoint opcional: reescritura respetuosa (sin IA) =====
Flight::route('POST /api/comentarios/reescribir', function(){
  $req  = Flight::request();
  $body = json_decode($req->getBody(), true) ?: [];
  $t    = normalize_text((string)($body['texto'] ?? ''));

  if ($t === '') { Flight::json(['ok'=>false,'reason'=>'Texto vacío'],422); return; }

  // reescritura muy simple y segura (no IA)
  $t = mb_convert_case($t, MB_CASE_LOWER, "UTF-8");
  $t = ucfirst($t);
  if (!str_ends_with($t, '.')) $t .= '.';
  if (!preg_match('/por favor|te pido|te ruego|gracias/i', $t)) {
    $t = "Por favor, " . $t . " Gracias.";
  }
  Flight::json(['ok'=>true, 'sugerencia'=>$t]);
});

// GET /api/comentarios/list?santo=santa_rosa&estado=aprobado&limit=50
Flight::route('GET /api/comentarios/list', function () {
  DB::query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
  $santo  = $_GET['santo']  ?? '';
  $estado = $_GET['estado'] ?? 'aprobado';
  $limit  = max(1, min((int)($_GET['limit'] ?? 50), 200));

  if (!in_array($santo, ['santa_rosa','senor_milagros','san_martin'], true)) {
    Flight::json(['ok'=>false,'reason'=>'Santo no permitido.'],422); return;
  }
  if (!in_array($estado, ['aprobado','en_revision','oculto'], true)) {
    $estado = 'aprobado';
  }

  $rows = DB::query("
    SELECT id, santo, texto, tipo, estado, created_at
    FROM comentarios
    WHERE santo=%s AND estado=%s
    ORDER BY created_at DESC
    LIMIT %i
  ", $santo, $estado, $limit);

  Flight::json(['ok'=>true,'items'=>$rows]);
});
