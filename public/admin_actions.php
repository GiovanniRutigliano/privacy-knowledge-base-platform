<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

use App\Config\Database;
use function App\Includes\require_role;
use function App\Includes\verify_csrf_token;

// Block execution streams unless explicitly processed by administrator
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retain absolute runtime safety checking boundaries
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        die("Session verification validation parameter evaluation errors.");
    }

    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $db = Database::getInstance();

    try {
        if ($action === 'delete') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if ($id) {
                $stmt = $db->prepare("DELETE FROM patterns WHERE id = :id");
                $stmt->execute([':id' => $id]);
            }
        } elseif ($action === 'create' || $action === 'edit') {
            // Map parameters consistently across CRUD boundary states
            $params = [
                ':n'   => trim($_POST['name'] ?? ''),
                ':st'  => trim($_POST['strategies'] ?? ''),
                ':d'   => trim($_POST['description'] ?? ''),
                ':c'   => trim($_POST['context'] ?? ''),
                ':iso' => trim($_POST['iso_phase'] ?? ''),
                ':g'   => trim($_POST['gdpr_article'] ?? ''),
                ':pp'  => trim($_POST['privacy_principles'] ?? ''),
                ':ow'  => trim($_POST['owasp_categories'] ?? ''),
                ':cwe' => trim($_POST['cwe_categories'] ?? ''),
                ':ex'  => trim($_POST['examples'] ?? ''),
                ':mvc' => trim($_POST['collocazione_mvc'] ?? ''),
            ];

            if ($action === 'create') {
                $sql = "INSERT INTO patterns (name, strategies, description, context, iso_phase, gdpr_article, privacy_principles, owasp_categories, cwe_categories, examples, collocazione_mvc) 
                        VALUES (:n, :st, :d, :c, :iso, :g, :pp, :ow, :cwe, :ex, :mvc)";
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
            } else {
                $params[':id'] = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                $sql = "UPDATE patterns SET 
                            name = :n, strategies = :st, description = :d, context = :c, 
                            iso_phase = :iso, gdpr_article = :g, privacy_principles = :pp, 
                            owasp_categories = :ow, cwe_categories = :cwe, examples = :ex, 
                            collocazione_mvc = :mvc 
                        WHERE id = :id";
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
            }
        }
        
        // Retain PRG (Post-Redirect-Get) architecture logic to prevent duplicate transaction transmissions
        header("Location: /admin.php?success=1");
        exit();
    } catch (\Exception $e) {
        error_log("Backend execution handler fault: " . $e->getMessage());
        header("Location: /admin.php?error=" . urlencode("Database runtime transaction error."));
        exit();
    }
} else {
    header("Location: /admin.php");
    exit();
}