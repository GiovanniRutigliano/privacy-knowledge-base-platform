<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

use App\Config\Database;
use function App\Includes\require_role;
use function App\Includes\get_csrf_token;

// Enforce baseline admin permission mapping validation boundary
require_role('admin');

$db = Database::getInstance();
$patterns = [];
$activePattern = null;
$mode = 'list';

try {
    $stmt = $db->query("SELECT * FROM patterns ORDER BY id DESC");
    $patterns = $stmt->fetchAll();
} catch (\Exception $e) {
    error_log("Admin records compilation interface read timeout: " . $e->getMessage());
}

// Check if interface state dictates editing a discrete target row
if (isset($_GET['action']) && $_GET['action'] === 'edit' && !empty($_GET['id'])) {
    $mode = 'edit';
    $targetId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($targetId) {
        $stmt = $db->prepare("SELECT * FROM patterns WHERE id = :id");
        $stmt->execute([':id' => $targetId]);
        $activePattern = $stmt->fetch();
        if (!$activePattern) $mode = 'list';
    }
} elseif (isset($_GET['action']) && $_GET['action'] === 'create') {
    $mode = 'create';
}

$page_title = "Administrator Knowledge Base Core Desk";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-5xl mx-auto mb-6">
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <h3 class="fw-bold m-0"><i class="fas fa-user-shield text-primary me-2"></i> Administration Management Desk</h3>
        <?php if ($mode === 'list'): ?>
            <a href="/admin.php?action=create" class="btn btn-primary"><i class="fas fa-plus me-2"></i> Instantiate Record</a>
        <?php else: ?>
            <a href="/admin.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i> Back to Overview</a>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success text-center small fw-semibold">Database transactions executed cleanly.</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger text-center small fw-semibold"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <?php if ($mode === 'create' || $mode === 'edit'): ?>
        <div class="card p-4 border-0 shadow-sm bg-light">
            <h5 class="fw-bold mb-4"><?= $mode === 'create' ? 'Initialize Design Pattern Record' : 'Modify Record Node #' . htmlspecialchars((string)$activePattern['id']) ?></h5>
            <form action="/admin_actions.php" method="POST" class="row g-3">
                <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
                <input type="hidden" name="action" value="<?= $mode ?>">
                <?php if ($mode === 'edit'): ?>
                    <input type="hidden" name="id" value="<?= htmlspecialchars((string)$activePattern['id']) ?>">
                <?php endif; ?>

                <div class="col-md-6">
                    <label for="name" class="form-label fw-semibold small">Pattern Classification String</label>
                    <input type="text" name="name" id="name" class="form-control shadow-none" value="<?= htmlspecialchars($activePattern['name'] ?? '') ?>" required>
                </div>
                
                <div class="col-md-6">
                    <label for="strategies" class="form-label fw-semibold small">Privacy Strategy Category</label>
                    <input type="text" name="strategies" id="strategies" class="form-control shadow-none" placeholder="e.g. Minimize, Hide, Separate" value="<?= htmlspecialchars($activePattern['strategies'] ?? '') ?>">
                </div>

                <div class="col-md-12">
                    <label for="description" class="form-label fw-semibold small">Core Technical Description</label>
                    <textarea name="description" id="description" rows="3" class="form-control shadow-none"><?= htmlspecialchars($activePattern['description'] ?? '') ?></textarea>
                </div>

                <div class="col-md-6">
                    <label for="context" class="form-label fw-semibold small">Applicability Context Contextual Basis</label>
                    <input type="text" name="context" id="context" class="form-control shadow-none" value="<?= htmlspecialchars($activePattern['context'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label for="iso_phase" class="form-label fw-semibold small">ISO Design Lifecycle Phase</label>
                    <input type="text" name="iso_phase" id="iso_phase" class="form-control shadow-none" value="<?= htmlspecialchars($activePattern['iso_phase'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label for="gdpr_article" class="form-label fw-semibold small">Mapped GDPR Article</label>
                    <input type="text" name="gdpr_article" id="gdpr_article" class="form-control shadow-none" value="<?= htmlspecialchars($activePattern['gdpr_article'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label for="privacy_principles" class="form-label fw-semibold small">Privacy Embedded Principles</label>
                    <input type="text" name="privacy_principles" id="privacy_principles" class="form-control shadow-none" value="<?= htmlspecialchars($activePattern['privacy_principles'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label for="owasp_categories" class="form-label fw-semibold small">OWASP Categorization Map</label>
                    <input type="text" name="owasp_categories" id="owasp_categories" class="form-control shadow-none" value="<?= htmlspecialchars($activePattern['owasp_categories'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label for="cwe_categories" class="form-label fw-semibold small">CWE Classification System</label>
                    <input type="text" name="cwe_categories" id="cwe_categories" class="form-control shadow-none" value="<?= htmlspecialchars($activePattern['cwe_categories'] ?? '') ?>">
                </div>

                <div class="col-md-12">
                    <label for="examples" class="form-label fw-semibold small">Field Proof Examples</label>
                    <textarea name="examples" id="examples" rows="2" class="form-control shadow-none"><?= htmlspecialchars($activePattern['examples'] ?? '') ?></textarea>
                </div>

                <div class="col-md-6">
                    <label for="collocazione_mvc" class="form-label fw-semibold small">MVC System Mapping Target</label>
                    <input type="text" name="collocazione_mvc" id="collocazione_mvc" class="form-control shadow-none" placeholder="e.g. Controller, Model, View" value="<?= htmlspecialchars($activePattern['collocazione_mvc'] ?? '') ?>">
                </div>

                <div class="col-12 text-end mt-4">
                    <button type="submit" class="btn btn-success px-4"><i class="fas fa-save me-2"></i> Commit Updates</button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm p-4">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle border text-center m-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Node ID</th>
                            <th>Classification</th>
                            <th>Strategy</th>
                            <th>ISO Focus</th>
                            <th>Mapped GDPR</th>
                            <th>Management Triggers</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($patterns)): ?>
                            <tr><td colspan="6" class="text-muted">Knowledge index currently unpopulated.</td></tr>
                        <?php else: ?>
                            <?php foreach ($patterns as $row): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars((string)$row['id']) ?></td>
                                    <td class="text-start fw-semibold text-primary"><?= htmlspecialchars($row['name']) ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($row['strategies']) ?></span></td>
                                    <td class="small text-truncate" style="max-width: 150px;">
                                        <?= htmlspecialchars($row['iso_phase']) ?>
                                    </td>
                                    <td class="small text-truncate" style="max-width: 150px;">
                                        <?= htmlspecialchars($row['gdpr_article']) ?>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="/admin.php?action=edit&id=<?= htmlspecialchars((string)$row['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit Node">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="/admin_actions.php" method="POST" onsubmit="return confirm('Evaluate explicit intent to purge records permanently?');" class="m-0">
                                                <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= htmlspecialchars((string)$row['id']) ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Purge Record">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>