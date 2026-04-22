<?php
// Self-deploy script - deletes itself after running
// Access: https://drinksomt.ch/cms-deploy.php?token=somt-cms-2026
define('AUTH_TOKEN', 'somt-cms-2026');
define('TARGET_FILE', __DIR__ . '/cms-webhook.php');
define('CURRENT_FILE', __FILE__);

$token = $_GET['token'] ?? '';
if ($token !== AUTH_TOKEN) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid token']);
    exit;
}

$newContent = '<?php
// SOMT CMS Webhook v2 - Token-based auth (nginx strips Authorization header)
define(\'AUTH_TOKEN\', \'somt-cms-2026\');
define(\'INDEX_PATH\', __DIR__ . \'/index.html\');
define(\'BACKUP_PATH\', __DIR__ . \'/index.html.bak\');
header(\'Content-Type: application/json\');
header(\'Access-Control-Allow-Origin: https://drinksomt.ch\');
header(\'Access-Control-Allow-Methods: GET, POST, OPTIONS\');
header(\'Access-Control-Allow-Headers: Content-Type\');
if ($_SERVER[\'REQUEST_METHOD\'] === \'OPTIONS\') { http_response_code(200); exit; }
$token = $_GET[\'token\'] ?? \'\';
if ($token !== AUTH_TOKEN) { http_response_code(401); echo json_encode([\'error\' => \'Invalid or missing token\', \'hint\' => \'Use ?token=somt-cms-2026\']); exit; }
// GET mode: just check status
if ($_SERVER[\'REQUEST_METHOD\'] === \'GET\') { echo json_encode([\'status\' => \'ok\', \'mode\' => \'token-auth\', \'time\' => date(\'c\')]); exit; }
// POST mode: update content
if ($_SERVER[\'REQUEST_METHOD\'] !== \'POST\') { http_response_code(405); echo json_encode([\'error\' => \'POST required\']); exit; }
$input = file_get_contents(\'php://input\');
$data = json_decode($input, true);
if (!$data) { http_response_code(400); echo json_encode([\'error\' => \'Invalid JSON\']); exit; }
if (file_exists(INDEX_PATH)) { copy(INDEX_PATH, BACKUP_PATH); }
$html = file_get_contents(INDEX_PATH);
foreach ($data as $key => $value) { $html = str_replace(\'{{\' . $key . \'}}\', $value, $html); }
$result = file_put_contents(INDEX_PATH, $html);
if ($result === false) { http_response_code(500); echo json_encode([\'error\' => \'Write failed\']); exit; }
echo json_encode([\'success\' => true, \'bytes\' => $result, \'fields\' => count($data), \'time\' => date(\'c\')]);';

// Backup old file
if (file_exists(TARGET_FILE)) {
    copy(TARGET_FILE, TARGET_FILE . '.bak');
}

// Write new content
$result = file_put_contents(TARGET_FILE, $newContent);
if ($result === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to write cms-webhook.php']);
    exit;
}

// Delete this deploy script
@unlink(CURRENT_FILE);

echo json_encode([
    'success' => true,
    'message' => 'cms-webhook.php deployed',
    'bytes_written' => $result,
    'deploy_script_removed' => true,
    'time' => date('c')
]);
