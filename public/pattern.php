<?php
require_once __DIR__ . '/../config/database.php';

use App\Config\Database;

$pattern = null;
$targetId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($targetId) {
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM patterns WHERE id = :id");
        $stmt->execute([':id' => $targetId]);
        $pattern = $stmt->fetch();
    } catch (\Exception $e) {
        error_log("Pattern detail lookup execution timeout: " . $e->getMessage());
    }
}

// Redirect client safely back to lookup directories if record node evaluates unpopulated
if (!$pattern) {
    header("Location: /search.php");
    exit();
}

$page_title = htmlspecialchars($pattern['name']) . " - Pattern Documentation";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-4xl mx-auto mb-6">
    <div class="card border-0 shadow-sm p-5 bg-white rounded mb-4">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small m-0">
                <li class="breadcrumb-item"><a href="/search.php" class="text-decoration-none">Search Index</a></li>
                <li class="breadcrumb-item active" aria-current="page">Node #<?= htmlspecialchars((string)$pattern['id']) ?></li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3 mt-2">
            <h2 class="fw-bold text-primary m-0"><?= htmlspecialchars($pattern['name']) ?></h2>
            <div class="d-flex gap-2">
                <?php if (!empty($pattern['strategies'])): ?>
                    <span class="badge bg-secondary fs-6 px-3 py-2"><?= htmlspecialchars($pattern['strategies']) ?></span>
                <?php endif; ?>
                <?php if (!empty($pattern['collocazione_mvc'])): ?>
                    <span class="badge bg-info text-dark fs-6 px-3 py-2"><?= htmlspecialchars($pattern['collocazione_mvc']) ?> Layer</span>
                <?php endif; ?>
            </div>
        </div>

        <hr class="text-muted my-3">

        <div class="row g-4 mt-1">
            <div class="col-12">
                <h5 class="fw-bold text-secondary mb-2"><i class="fas fa-book-open me-2"></i> Core Technical Description</h5>
                <p class="text-break lh-lg bg-light p-3 rounded border-start border-primary border-4 mb-0">
                    <?= nl2br(htmlspecialchars($pattern['description'] ?? 'No functional description specified.')) ?>
                </p>
            </div>

            <div class="col-md-6">
                <h6 class="fw-bold text-secondary mb-2"><i class="fas fa-bullseye me-2"></i> Applicability Context Contextual Basis</h6>
                <p class="small text-muted bg-light p-3 rounded h-100 mb-0">
                    <?= nl2br(htmlspecialchars($pattern['context'] ?? 'N/A')) ?>
                </p>
            </div>

            <div class="col-md-6">
                <h6 class="fw-bold text-secondary mb-2"><i class="fas fa-stream me-2"></i> ISO Design Lifecycle Target</h6>
                <p class="small text-muted bg-light p-3 rounded h-100 mb-0">
                    <?= htmlspecialchars($pattern['iso_phase'] ?? 'Unmapped lifecycle focus') ?>
                </p>
            </div>

            <div class="col-md-6">
                <h6 class="fw-bold text-secondary mb-2"><i class="fas fa-gavel me-2"></i> GDPR Mapped Constraints</h6>
                <p class="small fw-semibold text-dark bg-light p-3 rounded h-100 mb-0">
                    <?= htmlspecialchars($pattern['gdpr_article'] ?? 'Unmapped legal constraints') ?>
                </p>
            </div>

            <div class="col-md-6">
                <h6 class="fw-bold text-secondary mb-2"><i class="fas fa-shield-alt me-2"></i> Embedded Privacy Principles</h6>
                <p class="small text-muted bg-light p-3 rounded h-100 mb-0">
                    <?= htmlspecialchars($pattern['privacy_principles'] ?? 'N/A') ?>
                </p>
            </div>

            <div class="col-md-6">
                <h6 class="fw-bold text-secondary mb-2"><i class="fas fa-bug me-2"></i> OWASP Vulnerability Alignment</h6>
                <p class="small text-muted bg-light p-3 rounded h-100 mb-0 text-break">
                    <?= htmlspecialchars($pattern['owasp_categories'] ?? 'No distinct OWASP categories assigned') ?>
                </p>
            </div>

            <div class="col-md-6">
                <h6 class="fw-bold text-secondary mb-2"><i class="fas fa-code-branch me-2"></i> Associated CWE Weaknesses</h6>
                <p class="small text-muted bg-light p-3 rounded h-100 mb-0 text-break">
                    <?= htmlspecialchars($pattern['cwe_categories'] ?? 'No target CWE mappings established') ?>
                </p>
            </div>

            <div class="col-12">
                <h5 class="fw-bold text-secondary mb-2 mt-2"><i class="fas fa-laptop-code me-2"></i> Field Proof Implementation Examples</h5>
                <div class="bg-light p-3 rounded border">
                    <?php if (empty($pattern['examples'])): ?>
                        <span class="small text-muted">No external telemetry execution references mapped.</span>
                    <?php else: ?>
                        <p class="small text-break mb-0 lh-base">
                            <?= nl2br(htmlspecialchars($pattern['examples'])) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="/search.php" class="btn btn-outline-primary px-4 me-2"><i class="fas fa-search me-2"></i> New Search</a>
            <a href="/advanced-search.php" class="btn btn-outline-secondary px-4"><i class="fas fa-sliders-h me-2"></i> Advanced Filters</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>