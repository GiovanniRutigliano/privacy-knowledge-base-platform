<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

use App\Config\Database;
use function App\Includes\require_role;
use function App\Includes\get_csrf_token;
use function App\Includes\require_csrf_token;

// Limit operations boundaries directly to designated Security Managers
require_role('security manager');
$db = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? null) === 'mark_reviewed') {
    require_csrf_token($_POST['csrf_token'] ?? null);
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (is_int($id) && $id > 0) {
        try {
            $statement = $db->prepare('UPDATE feedback SET reviewed_at = CURRENT_TIMESTAMP WHERE id = ? AND reviewed_at IS NULL');
            $statement->execute([$id]);
        } catch (Throwable $exception) {
            error_log('Feedback review failed: ' . $exception->getMessage());
            header('Location: /security_manager.php?error=1', true, 303);
            exit;
        }
    }
    header('Location: /security_manager.php', true, 303);
    exit;
}

$feedbacks = [];
try {
    $stmt = $db->query("SELECT * FROM feedback ORDER BY id DESC");
    $feedbacks = $stmt->fetchAll();
} catch (\Exception $e) {
    error_log("Feedback inspection compilation interface fault: " . $e->getMessage());
}

$page_title = "Security review";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="card border-0 shadow-sm p-4">
    <h3 class="fw-bold mb-4"><i class="fas fa-shield-alt text-success me-2"></i> Security review</h3>
    <p class="text-muted small mb-4">Review submitted feedback and keep a record of items already handled.</p>

    <?php if (isset($_GET['error'])): ?><p class="alert alert-warning" role="alert">Could not save the review. Try again.</p><?php endif; ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle border">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Feedback</th>
                    <th>Timestamp</th>
                    <th>Review status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($feedbacks)): ?>
                    <tr><td colspan="5" class="text-center text-muted">No feedback submitted.</td></tr>
                <?php else: ?>
                    <?php foreach ($feedbacks as $row): ?>
                        <tr>
                            <td class="fw-bold text-center"><?= htmlspecialchars((string)$row['id']) ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($row['feedback_type']) ?></span></td>
                            <td class="small text-break"><?= htmlspecialchars($row['feedback_content']) ?></td>
                            <td class="small text-muted text-nowrap"><?= htmlspecialchars($row['created_at']) ?></td>
                            <td class="text-center">
                                <?php if ($row['reviewed_at']): ?>
                                <span class="badge bg-success">Reviewed</span>
                                <?php else: ?>
                                <form action="/security_manager.php" method="POST" class="m-0">
                                    <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
                                    <input type="hidden" name="action" value="mark_reviewed">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars((string)$row['id']) ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-check-circle"></i> Mark reviewed
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>