<?php

declare(strict_types=1);

function parseEnvFile(string $path): array
{
    if (!is_file($path)) {
        return [];
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        return [];
    }

    $env = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }

        $env[$key] = $value;
    }

    return $env;
}

function tryConnect(array $attempt): ?PDO
{
    try {
        $pdo = new PDO(
            $attempt['dsn'],
            $attempt['user'],
            $attempt['pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        return $pdo;
    } catch (Throwable) {
        return null;
    }
}

$projectRoot = dirname(__DIR__);
$env = parseEnvFile($projectRoot . '/.env');

$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = (string)($env['DB_PORT'] ?? '3306');
$adminUser = $env['DB_USERNAME'] ?? 'root';
$adminPass = $env['DB_PASSWORD'] ?? '';

$database = 'froglake_app';
$username = 'froglake_App';
$password = bin2hex(random_bytes(12));

$attempts = [
    [
        'label' => 'env',
        'dsn' => "mysql:host={$host};port={$port};charset=utf8mb4",
        'user' => $adminUser,
        'pass' => $adminPass,
    ],
    [
        'label' => 'root_socket',
        'dsn' => "mysql:host=localhost;port={$port};charset=utf8mb4",
        'user' => 'root',
        'pass' => '',
    ],
    [
        'label' => 'root_tcp',
        'dsn' => "mysql:host={$host};port={$port};charset=utf8mb4",
        'user' => 'root',
        'pass' => '',
    ],
];

$pdo = null;
$usedLabel = null;
$lastError = null;

foreach ($attempts as $attempt) {
    $pdo = tryConnect($attempt);
    if (!$pdo instanceof PDO) {
        $lastError = 'MySQL connection failed for attempt: ' . $attempt['label'];
        continue;
    }

    try {
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        $hosts = ['localhost', '127.0.0.1'];
        foreach ($hosts as $userHost) {
            try {
                $pdo->exec("CREATE USER IF NOT EXISTS '{$username}'@'{$userHost}' IDENTIFIED BY '{$password}'");
            } catch (Throwable) {
                $pdo->exec("ALTER USER '{$username}'@'{$userHost}' IDENTIFIED BY '{$password}'");
            }

            $pdo->exec("GRANT ALL PRIVILEGES ON `{$database}`.* TO '{$username}'@'{$userHost}'");
        }

        $pdo->exec('FLUSH PRIVILEGES');
        $usedLabel = $attempt['label'];
        $lastError = null;
        break;
    } catch (Throwable $e) {
        $pdo = null;
        $lastError = $attempt['label'] . ': ' . $e->getMessage();
        continue;
    }
}

if (!$usedLabel) {
    fwrite(STDERR, "Unable to create DB/user with available MySQL credentials.\n");
    fwrite(STDERR, $lastError ? ("Last error: {$lastError}\n") : '');
    exit(1);
}

echo json_encode(
    [
        'DB_CONNECTION' => 'mysql',
        'DB_HOST' => $host,
        'DB_PORT' => (int)$port,
        'DB_DATABASE' => $database,
        'DB_USERNAME' => $username,
        'DB_PASSWORD' => $password,
        'admin_connection' => $usedLabel,
    ],
    JSON_PRETTY_PRINT
) . "\n";
