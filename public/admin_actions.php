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
                ':n'   => trim(filter_input(INPUT_POST, 'name') ?? ''),
                ':st'  => trim(filter_input(INPUT_POST, 'strategies') ?? ''),
                ':d'   => trim(filter_input(INPUT_POST, 'description') ?? ''),
                ':c'   => trim(filter_input(INPUT_POST, 'context') ?? ''),
                ':iso' => trim(filter_input(INPUT_POST, 'iso_phase') ?? ''),
                ':g'   => trim(filter_input(INPUT_POST, 'gdpr_article') ?? ''),
                ':pp'  => trim(filter_input(INPUT_POST, 'privacy_principles') ?? ''),
                ':ow'  => trim(filter_input(INPUT_POST, 'owasp_categories') ?? ''),
                ':cwe' => trim(filter_input(INPUT_POST, 'cwe_categories') ?? ''),
                ':ex'  => trim(filter_input(INPUT_POST, 'examples') ?? ''),
                ':mvc' => trim(filter_input(INPUT_POST, 'collocazione_mvc') ?? ''),
            ];

            if ($params[':n'] === '' || mb_strlen($params[':n']) > 255) {
                header('Location: /admin.php?error=' . urlencode('Enter a name between 1 and 255 characters.'), true, 303);
                exit;
            }

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