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

function usage(): void
{
    fwrite(STDERR, "Usage: php tools/import_sql_dump.php /abs/path/to/dump.sql [--fresh]\n");
}

function ensureMysqlClientExists(): string
{
    $path = trim((string)shell_exec('command -v mysql 2>/dev/null'));
    if ($path === '') {
        fwrite(STDERR, "mysql client not found. Install MySQL client and re-run.\n");
        exit(1);
    }
    return $path;
}

function pdoFromEnv(array $env): PDO
{
    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $port = (string)($env['DB_PORT'] ?? '3306');
    $db = $env['DB_DATABASE'] ?? '';
    $user = $env['DB_USERNAME'] ?? '';
    $pass = $env['DB_PASSWORD'] ?? '';

    if ($db === '' || $user === '') {
        fwrite(STDERR, "Missing DB_DATABASE or DB_USERNAME in .env.\n");
        exit(1);
    }

    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
    return new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function dropAllTables(PDO $pdo, string $database): void
{
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

    $stmt = $pdo->prepare('SELECT table_name FROM information_schema.tables WHERE table_schema = :db');
    $stmt->execute(['db' => $database]);
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        $pdo->exec('DROP TABLE IF EXISTS `' . str_replace('`', '``', (string)$table) . '`');
    }

    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
}

function writeTempDefaultsFile(array $env): string
{
    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $port = (string)($env['DB_PORT'] ?? '3306');
    $user = $env['DB_USERNAME'] ?? '';
    $pass = $env['DB_PASSWORD'] ?? '';

    $tmp = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'froglake_mysql_' . bin2hex(random_bytes(8)) . '.cnf';
    $contents = "[client]\n";
    $contents .= "host={$host}\n";
    $contents .= "port={$port}\n";
    $contents .= "user={$user}\n";
    $contents .= "password={$pass}\n";
    $contents .= "default-character-set=utf8mb4\n";

    if (file_put_contents($tmp, $contents) === false) {
        fwrite(STDERR, "Failed to write temp mysql defaults file.\n");
        exit(1);
    }

    chmod($tmp, 0600);
    return $tmp;
}

function importSql(string $mysqlBin, string $defaultsFile, string $database, string $sqlPath): void
{
    $cmd = implode(' ', [
        escapeshellarg($mysqlBin),
        '--defaults-extra-file=' . escapeshellarg($defaultsFile),
        '--database=' . escapeshellarg($database),
        '--default-character-set=utf8mb4',
        '--binary-mode=1',
    ]);

    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = proc_open($cmd, $descriptors, $pipes);
    if (!is_resource($process)) {
        fwrite(STDERR, "Failed to start mysql import process.\n");
        exit(1);
    }

    $in = fopen($sqlPath, 'rb');
    if ($in === false) {
        fwrite(STDERR, "Unable to open SQL file: {$sqlPath}\n");
        proc_terminate($process);
        exit(1);
    }

    stream_copy_to_stream($in, $pipes[0]);
    fclose($in);
    fclose($pipes[0]);

    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);

    $exitCode = proc_close($process);

    if ($stdout !== false && trim($stdout) !== '') {
        fwrite(STDOUT, $stdout);
    }

    if ($exitCode !== 0) {
        fwrite(STDERR, $stderr !== false ? $stderr : "mysql import failed with exit code {$exitCode}\n");
        exit($exitCode);
    }
}

$args = $argv;
array_shift($args);

if (count($args) < 1) {
    usage();
    exit(1);
}

$sqlPath = $args[0];
$fresh = in_array('--fresh', $args, true);

if (!str_starts_with($sqlPath, DIRECTORY_SEPARATOR)) {
    fwrite(STDERR, "Please provide an absolute path to the SQL file.\n");
    exit(1);
}

if (!is_file($sqlPath)) {
    fwrite(STDERR, "SQL file not found: {$sqlPath}\n");
    exit(1);
}

$projectRoot = dirname(__DIR__);
$env = parseEnvFile($projectRoot . '/.env');

$database = $env['DB_DATABASE'] ?? '';
if ($database === '') {
    fwrite(STDERR, "DB_DATABASE is missing in .env\n");
    exit(1);
}

$mysqlBin = ensureMysqlClientExists();

$pdo = pdoFromEnv($env);

if ($fresh) {
    fwrite(STDOUT, "Dropping existing tables in {$database}...\n");
    dropAllTables($pdo, $database);
}

$defaultsFile = writeTempDefaultsFile($env);

try {
    fwrite(STDOUT, "Importing {$sqlPath} into {$database}...\n");
    importSql($mysqlBin, $defaultsFile, $database, $sqlPath);
    fwrite(STDOUT, "Import complete.\n");
} finally {
    if (is_file($defaultsFile)) {
        @unlink($defaultsFile);
    }
}

