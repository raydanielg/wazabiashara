<?php
/*
|--------------------------------------------------------------------------
| WAZABIASHARA — Newsletter AJAX Endpoint (api/subscribe.php)
|--------------------------------------------------------------------------
| Inapokea: POST JSON { "email": "..." }
| Inarudisha: JSON { success: bool, message: string }
|
| KUUNGANISHA DATABASE BAADAE:
|   1. Jaza $DB_* hapa chini.
|   2. Ondoa comment kwenye sehemu ya "DATABASE (PDO)".
|   3. Tengeneza table kwa SQL iliyoko mwishoni mwa faili hili.
| Kwa sasa: email zinahifadhiwa kwenye faili subscribers.json (fallback).
*/

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// ---- Ruhusu POST tu ----
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Njia ya ombi si sahihi.']);
    exit;
}

// ---- Soma JSON body ----
$raw   = file_get_contents('php://input');
$input = json_decode($raw, true);
$email = strtolower(trim($input['email'] ?? ($_POST['email'] ?? '')));

// ---- Validation ----
if ($email === '') {
    echo json_encode(['success' => false, 'message' => 'Tafadhali andika barua pepe yako.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Barua pepe uliyoandika si sahihi.']);
    exit;
}
if (strlen($email) > 190) {
    echo json_encode(['success' => false, 'message' => 'Barua pepe ni ndefu kupita kiasi.']);
    exit;
}

/*
|--------------------------------------------------------------------------
| DATABASE (PDO) — ondoa comment ukiwa tayari kuunganisha
|--------------------------------------------------------------------------
$DB_HOST = '127.0.0.1';
$DB_NAME = 'wazabiashara';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER, $DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Zuia kujiunga mara mbili
    $check = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE email = ? LIMIT 1");
    $check->execute([$email]);
    if ($check->fetch()) {
        echo json_encode(['success' => true, 'message' => 'Tayari umejiunga na jarida letu. Asante!']);
        exit;
    }

    $ins = $pdo->prepare(
        "INSERT INTO newsletter_subscribers (email, ip_address, created_at) VALUES (?, ?, NOW())"
    );
    $ins->execute([$email, $_SERVER['REMOTE_ADDR'] ?? null]);

    echo json_encode(['success' => true, 'message' => 'Umejiunga kikamilifu! Karibu Wazabiashara.']);
    exit;

} catch (PDOException $e) {
    // error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Tatizo la kiufundi limetokea. Jaribu tena baadaye.']);
    exit;
}
|--------------------------------------------------------------------------
*/

// ---- FALLBACK (bila database): hifadhi kwenye subscribers.json ----
$file = __DIR__ . '/subscribers.json';
$list = [];

if (is_file($file)) {
    $list = json_decode((string) file_get_contents($file), true) ?: [];
}

// Zuia kujiunga mara mbili
foreach ($list as $row) {
    if (($row['email'] ?? '') === $email) {
        echo json_encode(['success' => true, 'message' => 'Tayari umejiunga na jarida letu. Asante!']);
        exit;
    }
}

$list[] = [
    'email'      => $email,
    'ip'         => $_SERVER['REMOTE_ADDR'] ?? null,
    'created_at' => date('Y-m-d H:i:s'),
];

$saved = @file_put_contents($file, json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);

if ($saved === false) {
    echo json_encode(['success' => false, 'message' => 'Imeshindikana kuhifadhi. Jaribu tena baadaye.']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Umejiunga kikamilifu! Karibu Wazabiashara.']);
exit;

/*
|--------------------------------------------------------------------------
| SQL — tengeneza table hii ukiunganisha database:
|--------------------------------------------------------------------------
CREATE TABLE newsletter_subscribers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) NOT NULL UNIQUE,
    ip_address VARCHAR(45) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
*/
