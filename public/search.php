<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/PatternRepository.php';

use App\Config\Database;
use App\Includes\PatternRepository;

$query = trim(mb_substr(filter_input(INPUT_GET, 'q') ?? '', 0, 160));
$live = filter_input(INPUT_GET, 'ajax') === 'true';
$error = false;
$results = [];
try {
    if (!$live || $query !== '') {
        $results = (new PatternRepository(Database::getInstance()))->search($query);
    }
} catch (Throwable $exception) {
    error_log('Pattern search failed: ' . $exception->getMessage());
    http_response_code(503);
    $error = true;
}

if ($live) {
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    echo json_encode($error ? ['error' => 'Search is temporarily unavailable.'] : $results, JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

$page_title = 'Browse privacy patterns';
require_once __DIR__ . '/../includes/header.php';
?>
<header class="page-intro">
    <h1>Browse privacy patterns</h1>
    <p>Search by name, strategy, GDPR article, or CWE identifier.</p>
    <form action="/search.php" method="get" class="search-form" role="search">
        <div><label for="searchInput">Search the catalog</label>
            <input type="search" id="searchInput" name="q" class="form-control" maxlength="160" value="<?= htmlspecialchars($query) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="/advanced-search.php">Advanced filters</a>
    </form>
</header>
<?php require __DIR__ . '/../includes/pattern-results.php'; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
