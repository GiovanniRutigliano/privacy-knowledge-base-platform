<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

use App\Config\Database;
use function App\Includes\require_role;
use function App\Includes\get_csrf_token;
use function App\Includes\verify_csrf_token;

// Limit operations boundaries directly to designated Security Managers
require_role('security manager');
$db = Database::getInstance();

// Intercept inline record purging postbacks
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_fb') {
    if (verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $purgeId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($purgeId) {
            try {
                $stmt = $db->prepare("DELETE FROM feedback WHERE id = :id");
                $stmt->execute([':id' => $purgeId]);
            } catch (\Exception $e) {
                error_log("Feedback purge error: " . $e->getMessage());
            }
        }
    }
    header("Location: /security_manager.php");
    exit();
}

$feedbacks = [];
try {
    $stmt = $db->query("SELECT * FROM feedback ORDER BY id DESC");
    $feedbacks = $stmt->fetchAll();
} catch (\Exception $e) {
    error_log("Feedback inspection compilation interface fault: " . $e->getMessage());
}

$page_title = "Security Operations Evaluation Monitoring Desk";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="card border-0 shadow-sm p-4">
    <h3 class="fw-bold mb-4"><i class="fas fa-shield-alt text-success me-2"></i> Security Manager Monitoring Stack</h3>
    <p class="text-muted small mb-4">Evaluate field feedback structural records directly pushed to system logging pipelines.</p>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle border">
            <thead class="table-dark">
                <tr>
                    <th>Entry Node</th>
                    <th>Classification Category</th>
                    <th>Context Base Details</th>
                    <th>Timestamp</th>
                    <th>Triggers</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($feedbacks)): ?>
                    <tr><td colspan="5" class="text-center text-muted">Feedback processing logs currently evaluate unpopulated.</td></tr>
                <?php else: ?>
                    <?php foreach ($feedbacks as $row): ?>
                        <tr>
                            <td class="fw-bold text-center"><?= htmlspecialchars((string)$row['id']) ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($row['feedback_type']) ?></span></td>
                            <td class="small text-break"><?= htmlspecialchars($row['feedback_content']) ?></td>
                            <td class="small text-muted text-nowrap"><?= htmlspecialchars($row['created_at']) ?></td>
                            <td class="text-center">
                                <form action="/security_manager.php" method="POST" onsubmit="return confirm('Purge selected monitoring record permanently?');" class="m-0">
                                    <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
                                    <input type="hidden" name="action" value="delete_fb">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars((string)$row['id']) ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Clear Record">
                                        <i class="fas fa-check-circle"></i> Resolve
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>