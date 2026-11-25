<?php

declare(strict_types=1);

use App\Config\Database;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../config/database.php';

$mode = $argv[1] ?? '';
if ($mode === '--hash-existing') {
    $db = Database::getInstance();
    $db->beginTransaction();
    try {
        $accounts = ['personal_informations' => 'id'];
        foreach ($accounts as $table => $key) {
            $rows = $db->query("SELECT $key, password FROM $table FOR UPDATE")->fetchAll();
            $update = $db->prepare("UPDATE $table SET password = ? WHERE $key = ?");
            foreach ($rows as $row) {
                if (password_get_info($row['password'])['algo'] === null) {
                    $update->execute([password_hash($row['password'], PASSWORD_DEFAULT), $row[$key]]);
                }
            }
        }
        $db->commit();
        exit(0);
    } catch (Throwable $error) {
        $db->rollBack();
        fwrite(STDERR, "Password migration failed. No rows were committed.\n");
        exit(1);
    }
}

$username = $argv[2] ?? '';
$password = getenv('DEMO_ACCOUNT_PASSWORD');
if (!in_array($mode, ['admin', 'security-manager'], true)
    || !preg_match('/^[A-Za-z0-9_-]{3,50}$/D', $username)
    || !is_string($password) || strlen($password) < 12 || strlen($password) > 72) {
    fwrite(STDERR, "Provide a role, a username, and DEMO_ACCOUNT_PASSWORD (12–72 bytes), or use --hash-existing.\n");
    exit(2);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
try {
    $db = Database::getInstance();
    $role = $mode === 'security-manager' ? 'security manager' : 'admin';
    $statement = $db->prepare('INSERT INTO personal_informations (username, role, password) VALUES (?, ?, ?)');
    $statement->execute([$username, $role, $hash]);
} catch (Throwable $error) {
    fwrite(STDERR, "Account creation failed. Check the database and whether the account already exists.\n");
    exit(1);
}
