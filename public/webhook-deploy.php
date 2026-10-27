<?php

// Kunci rahasia untuk otorisasi webhook
// Anda bisa mengubah kode ini sesuai keinginan Anda
$secret = 'hb_deploy_raport_2026';

// 1. Verifikasi Kunci Rahasia
$providedSecret = $_GET['secret'] ?? $_SERVER['HTTP_X_WEBHOOK_SECRET'] ?? '';
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

$isValid = false;
if (! empty($providedSecret) && hash_equals($secret, $providedSecret)) {
    $isValid = true;
} elseif (! empty($signature) && hash_equals('sha256='.hash_hmac('sha256', $payload, $secret), $signature)) {
    $isValid = true;
}

if (! $isValid) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Akses ditolak: Kode rahasia tidak valid.',
    ], JSON_PRETTY_PRINT);
    exit;
}

// 2. Eksekusi Git Pull dan cPanel Deployment
$repoDir = '/home/fivgoweb/repositories/Raport-Bayangan';
$output = [];

$output[] = '--- Menjalankan Git Pull ---';
exec("cd $repoDir && git pull origin master 2>&1", $gitOutput, $gitStatus);
$output = array_merge($output, $gitOutput ?? []);

$output[] = '--- Menjalankan cPanel Deployment ---';
exec("uapi VersionControlDeployment create repository_root=$repoDir 2>&1", $deployOutput, $deployStatus);
$output = array_merge($output, $deployOutput ?? []);

// 3. Tampilkan respon JSON
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'timestamp' => date('Y-m-d H:i:s'),
    'output' => $output,
], JSON_PRETTY_PRINT);
