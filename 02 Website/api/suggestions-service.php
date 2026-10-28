<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';

const HASA_SUGGESTION_CATEGORIES = ['idee', 'verbesserung', 'fehler', 'sonstiges'];
const HASA_SUGGESTION_RETENTION_DAYS = 180;

function hasaSuggestionInstall(PDO $pdo): void
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS hasa_suggestions (
        id CHAR(32) PRIMARY KEY, category VARCHAR(20) NOT NULL, subject VARCHAR(160) NOT NULL,
        message TEXT NOT NULL, display_name VARCHAR(120) NOT NULL, contact VARCHAR(254) NOT NULL,
        created_at BIGINT NOT NULL, expires_at BIGINT NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $pdo->exec("CREATE TABLE IF NOT EXISTS hasa_suggestion_limits (
        bucket CHAR(64) PRIMARY KEY, hits INT NOT NULL, expires_at BIGINT NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}
function hasaSuggestionReady(PDO $pdo): bool
{
    try {
        $pdo->query('SELECT id FROM hasa_suggestions LIMIT 0');
        $pdo->query('SELECT bucket FROM hasa_suggestion_limits LIMIT 0');
        return true;
    } catch (PDOException $e) { return false; }
}
function hasaSuggestionAdmin(PDO $pdo, array $user): void
{
    $q = $pdo->prepare('SELECT active, role, must_change_password, can_manage_user_admins FROM hasa_users WHERE id=?');
    $q->execute([(int)$user['id']]);
    $actor = $q->fetch(PDO::FETCH_ASSOC);
    if (!$actor || !(int)$actor['active'] || (int)$actor['must_change_password']
        || ($actor['role'] !== 'root' && !(int)$actor['can_manage_user_admins'])) {
        throw new DomainException('forbidden');
    }
}
function hasaSuggestionText(array $input, string $key, int $min, int $max): string
{
    $value = $input[$key] ?? '';
    if (!is_string($value) || !mb_check_encoding($value, 'UTF-8')) throw new InvalidArgumentException($key);
    $value = trim(str_replace(["\r\n", "\r"], "\n", $value));
    $length = mb_strlen($value, 'UTF-8');
    if ($length < $min || $length > $max || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $value)) throw new InvalidArgumentException($key);
    return $value;
}
function hasaSuggestionValidate(array $input): array
{
    foreach (array_keys($input) as $key) {
        if (!in_array($key, ['category','subject','message','name','contact','consent','website','csrf'], true)) throw new InvalidArgumentException('fields');
    }
    if (($input['consent'] ?? false) !== true) throw new InvalidArgumentException('consent');
    if (!isset($input['category']) || !in_array($input['category'], HASA_SUGGESTION_CATEGORIES, true)) throw new InvalidArgumentException('category');
    $data = ['category' => $input['category'],
        'subject' => hasaSuggestionText($input, 'subject', 3, 160),
        'message' => hasaSuggestionText($input, 'message', 10, 10000),
        'name' => hasaSuggestionText($input, 'name', 0, 120),
        'contact' => hasaSuggestionText($input, 'contact', 0, 254)];
    if ($data['contact'] !== '' && !filter_var($data['contact'], FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('contact');
    return $data;
}
function hasaSuggestionOriginAllowed(string $origin, array $config): bool
{
    if ($origin === 'https://hasa.li') return true;
    return ($config['environment'] ?? 'production') !== 'production'
        && (bool)preg_match('~^http://(?:127\.0\.0\.1|localhost)(?::[0-9]+)?$~D', $origin);
}
function hasaSuggestionCleanup(PDO $pdo, int $now): void
{
    $pdo->prepare('DELETE FROM hasa_suggestions WHERE expires_at <= ?')->execute([$now]);
    $pdo->prepare('DELETE FROM hasa_suggestion_limits WHERE expires_at <= ?')->execute([$now]);
}
function hasaSuggestionSave(PDO $pdo, array $data, string $address, string $secret, int $now): ?string
{
    if (strlen($secret) < 24) throw new RuntimeException('configuration_missing');
    hasaSuggestionCleanup($pdo, $now);
    $ip = hash_hmac('sha256', gmdate('Y-m-d', $now) . '|' . $address, $secret);
    // Insert and lock the global bucket first: concurrent requests share one lock order.
    $rules = [
        ['global-hour:' . intdiv($now, 3600), 300, (intdiv($now, 3600)+1)*3600],
        ['ip-day:' . $ip, 20, (intdiv($now, 86400)+1)*86400],
        ['ip-hour:' . $ip . ':' . intdiv($now, 3600), 5, (intdiv($now, 3600)+1)*3600]
    ];
    $pdo->beginTransaction();
    try {
        $insert = $pdo->prepare('INSERT INTO hasa_suggestion_limits(bucket,hits,expires_at) VALUES(?,0,?) ON DUPLICATE KEY UPDATE bucket=VALUES(bucket)');
        $read = $pdo->prepare('SELECT hits FROM hasa_suggestion_limits WHERE bucket=? FOR UPDATE');
        $increase = $pdo->prepare('UPDATE hasa_suggestion_limits SET hits=hits+1 WHERE bucket=?');
        foreach ($rules as [$key,$limit,$expires]) {
            $key = hash('sha256', $key);
            $insert->execute([$key,$expires]); $read->execute([$key]);
            if ((int)$read->fetchColumn() >= $limit) { $pdo->rollBack(); return null; }
            $increase->execute([$key]);
        }
        $id = bin2hex(random_bytes(16));
        $q = $pdo->prepare('INSERT INTO hasa_suggestions(id,category,subject,message,display_name,contact,created_at,expires_at) VALUES(?,?,?,?,?,?,?,?)');
        $q->execute([$id,$data['category'],$data['subject'],$data['message'],$data['name'],$data['contact'],$now,$now+HASA_SUGGESTION_RETENTION_DAYS*86400]);
        $pdo->commit(); return $id;
    } catch (Throwable $e) { if ($pdo->inTransaction()) $pdo->rollBack(); throw $e; }
}
