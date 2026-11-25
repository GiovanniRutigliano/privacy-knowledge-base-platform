<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/PatternRepository.php';

use App\Config\Database;
use App\Includes\PatternRepository;

$filters = [];
$fields = PatternRepository::FACETS + ['name' => 'Pattern name', 'gdpr_article' => 'GDPR article'];
foreach ($fields as $key => $label) {
    $filters[$key] = trim(mb_substr(filter_input(INPUT_GET, $key) ?? '', 0, 160));
}
$error = false;
$results = [];
$options = [];
try {
    $repository = new PatternRepository(Database::getInstance());
    $options = $repository->facets();
    $results = $repository->search('', $filters);
} catch (Throwable $exception) {
    error_log('Advanced search failed: ' . $exception->getMessage());
    http_response_code(503);
    $error = true;
}
$page_title = 'Advanced pattern filters';
require_once __DIR__ . '/../includes/header.php';
?>
<header class="page-intro">
    <h1>Advanced filters</h1>
    <p>Combine a search by name or GDPR article with categories from the catalog.</p>
</header>
<form action="/advanced-search.php" method="get" class="facet-form">
    <input type="hidden" name="search_action" value="1">
    <?php foreach ($fields as $key => $label): ?>
        <div>
            <label for="<?= $key ?>"><?= htmlspecialchars($label) ?></label>
            <?php if (isset($options[$key])): ?>
                <select class="form-select" id="<?= $key ?>" name="<?= $key ?>">
                    <option value="">All</option>
                    <?php foreach ($options[$key] as $option): ?>
                        <option value="<?= htmlspecialchars($option) ?>" <?= $filters[$key] === $option ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input class="form-control" id="<?= $key ?>" name="<?= $key ?>" value="<?= htmlspecialchars($filters[$key]) ?>" maxlength="160">
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    <div class="facet-actions"><button class="btn btn-primary" type="submit">Apply filters</button> <a href="/advanced-search.php">Clear</a></div>
</form>
<?php require __DIR__ . '/../includes/pattern-results.php'; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
