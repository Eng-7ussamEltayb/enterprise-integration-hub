<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
if ($apiKey !== 'demo-key-123') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$queueFile = __DIR__ . '/../storage/queue.json';
$auditFile = __DIR__ . '/../storage/audit.json';
foreach ([$queueFile, $auditFile] as $f) {
    if (!file_exists($f)) {
        if (!is_dir(dirname($f))) mkdir(dirname($f), 0777, true);
        file_put_contents($f, '[]');
    }
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

function read_json(string $file): array {
    return json_decode((string)file_get_contents($file), true) ?: [];
}
function write_json(string $file, array $data): void {
    file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}
function audit(string $file, string $event, array $meta = []): void {
    $rows = read_json($file);
    $rows[] = ['event' => $event, 'meta' => $meta, 'at' => date('c')];
    write_json($file, $rows);
}

if ($method === 'POST' && $path === '/webhooks/inbound') {
    $payload = json_decode((string)file_get_contents('php://input'), true) ?: [];
    $queue = read_json($queueFile);
    $job = ['id' => count($queue) + 1, 'type' => $payload['type'] ?? 'unknown', 'payload' => $payload, 'status' => 'queued'];
    $queue[] = $job;
    write_json($queueFile, $queue);
    audit($auditFile, 'webhook.received', ['job_id' => $job['id']]);
    http_response_code(202);
    echo json_encode(['accepted' => true, 'job' => $job], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($method === 'GET' && $path === '/jobs') {
    echo json_encode(['data' => read_json($queueFile)], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($method === 'GET' && $path === '/audit') {
    echo json_encode(['data' => read_json($auditFile)], JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Not found']);
