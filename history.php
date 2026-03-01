<?php
require 'auth.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? 'list';
$username = $_SESSION['username'] ?? 'user';
$safeUser = preg_replace('/[^a-zA-Z0-9_-]/', '_', $username);
$dir = __DIR__ . '/storage';
$file = $dir . '/history_' . $safeUser . '.json';

if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

if ($action === 'clear') {
    file_put_contents($file, json_encode([]));
    echo json_encode(['success' => true]);
    exit;
}

if (!file_exists($file)) {
    echo json_encode(['history' => []]);
    exit;
}

$raw = file_get_contents($file);
$data = json_decode($raw, true);

if (!is_array($data)) {
    echo json_encode(['history' => []]);
    exit;
}

echo json_encode(['history' => $data]);
