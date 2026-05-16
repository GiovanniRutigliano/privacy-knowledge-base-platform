<?php
require_once __DIR__ . '/../config/database.php';

use App\Config\Database;

// Handle pure async live API searching payloads internally
// Note to self: PDO native prepared statements (ATTR_EMULATE_PREPARES = false) do not support reusing the same named placeholder multiple times. Mapping discrete placeholders (:q1 to :q5) ensures strict driver compatibility and optimal execution safety.
if (isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    header('Content-Type: application/json; charset=utf-8');
    $queryStr = trim($_GET['q'] ?? '');

    if ($queryStr === '') {
        echo json_encode([]);
        exit();
    }

    try {
        $db = Database::getInstance();
        $sql = "SELECT id, name, strategies, description, context, iso_phase, gdpr_article, collocazione_mvc 
                FROM patterns 
                WHERE name LIKE :q1 
                   OR strategies LIKE :q2 
                   OR description LIKE :q3 
                   OR context LIKE :q4
                   OR gdpr_article LIKE :q5 
                ORDER BY id ASC 
                LIMIT 25";
        
        $stmt = $db->prepare($sql);
        $param = "%{$queryStr}%";
        
        // Explicitly bind individual parameter tokens to satisfy native SQL statement compilation bounds
        $stmt->execute([
            ':q1' => $param,
            ':q2' => $param,
            ':q3' => $param,
            ':q4' => $param,
            ':q5' => $param,
        ]);
        $results = $stmt->fetchAll();

        // Ensure safe translation of complex multi-byte string boundaries mapping legacy dataset imports
        echo json_encode($results, JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE);
    } catch (\Exception $e) {
        http_response_code(500);
        // Note to self: Exposing raw exception strings locally inside client evaluation outputs to debug API transmission flows securely
        echo json_encode(['error' => 'Data index access unavailable: ' . $e->getMessage()]);
    }
    exit();
}

$page_title = "Search Patterns Index";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="card p-4 shadow-sm mx-auto border-0 bg-light" style="max-width: 800px;">
    <h3 class="text-center fw-bold mb-4">Search Privacy Knowledge Base</h3>
    <div class="input-group input-group-lg">
        <span class="input-group-text border-0 bg-white"><i class="fas fa-search text-muted"></i></span>
        <input type="text" id="searchInput" class="form-control border-0 shadow-none" placeholder="Enter keyword (e.g. Tracking, Metadata, Consent...)" autocomplete="off">
    </div>
    <div class="text-center mt-3">
        <a href="/advanced-search.php" class="text-decoration-none small text-muted"><i class="fas fa-filter"></i> Switch to granular Advanced Filtering</a>
    </div>
</div>

<div id="searchResults" class="mt-5">
    </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>