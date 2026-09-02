<?php
declare(strict_types=1);

const HASA_API_VERSION = '1.2.0-alpha.1';

function hasaJson(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function hasaConfig(): array
{
    static $config = null;
    if (is_array($config)) {
        return $config;
    }

    $path = __DIR__ . '/config.php';
    if (!is_file($path)) {
        hasaJson([
            'ok' => false,
            'error' => 'configuration_missing',
            'message' => 'config.php wurde noch nicht eingerichtet.',
        ], 503);
    }

    $loaded = require $path;
    if (!is_array($loaded)) {
        hasaJson(['ok' => false, 'error' => 'configuration_invalid'], 503);
    }
    $config = $loaded;
    return $config;
}

function hasaPdo(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $db = hasaConfig()['database'] ?? [];
    foreach (['host', 'name', 'user', 'password'] as $key) {
        if (!isset($db[$key]) || $db[$key] === '') {
            throw new RuntimeException('Datenbankkonfiguration unvollständig.');
        }
    }

    $port = (int)($db['port'] ?? 3306);
    $charset = (string)($db['charset'] ?? 'utf8mb4');
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $db['host'],
        $port,
        $db['name'],
        $charset
    );

    $pdo = new PDO($dsn, (string)$db['user'], (string)$db['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function hasaHeader(string $name): string
{
    $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
    return trim((string)($_SERVER[$key] ?? ''));
}

function hasaRequireApiKey(): void
{
    $expected = (string)(hasaConfig()['api_key'] ?? '');
    $provided = hasaHeader('X-HASA-Key');
    if ($expected === '' || strlen($expected) < 32) {
        hasaJson(['ok' => false, 'error' => 'api_key_not_configured'], 503);
    }
    if ($provided === '' || !hash_equals($expected, $provided)) {
        hasaJson(['ok' => false, 'error' => 'unauthorized'], 401);
    }
}

function hasaReadJson(): array
{
    $config = hasaConfig();
    $max = (int)($config['max_request_bytes'] ?? 1048576);
    $length = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
    if ($length > $max) {
        hasaJson(['ok' => false, 'error' => 'request_too_large'], 413);
    }

    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        hasaJson(['ok' => false, 'error' => 'empty_request'], 400);
    }
    if (strlen($raw) > $max) {
        hasaJson(['ok' => false, 'error' => 'request_too_large'], 413);
    }

    try {
        $decoded = json_decode($raw, true, 64, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        hasaJson(['ok' => false, 'error' => 'invalid_json'], 400);
    }
    if (!is_array($decoded)) {
        hasaJson(['ok' => false, 'error' => 'json_object_required'], 400);
    }
    return $decoded;
}

function hasaText(mixed $value, int $maxLength): ?string
{
    if ($value === null || $value === '') {
        return null;
    }
    $text = trim((string)$value);
    if ($text === '') {
        return null;
    }
    return mb_substr($text, 0, $maxLength, 'UTF-8');
}

function hasaChoice(mixed $value, array $allowed, string $fallback): string
{
    $value = (string)$value;
    return in_array($value, $allowed, true) ? $value : $fallback;
}

function hasaDateTime(mixed $value): string
{
    if ($value === null || $value === '') {
        return gmdate('Y-m-d H:i:s');
    }
    try {
        return (new DateTimeImmutable((string)$value))
            ->setTimezone(new DateTimeZone('UTC'))
            ->format('Y-m-d H:i:s');
    } catch (Throwable) {
        hasaJson(['ok' => false, 'error' => 'invalid_observed_at'], 400);
    }
}

set_exception_handler(static function (Throwable $error): never {
    error_log('HASA API: ' . $error->getMessage());
    $environment = 'production';
    try {
        $environment = (string)(hasaConfig()['environment'] ?? 'production');
    } catch (Throwable) {
    }
    $result = ['ok' => false, 'error' => 'server_error'];
    if ($environment !== 'production') {
        $result['detail'] = $error->getMessage();
    }
    hasaJson($result, 500);
});
