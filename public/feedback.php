<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

use App\Config\Database;
use function App\Includes\get_csrf_token;
use function App\Includes\verify_csrf_token;

$message = null;
$status  = null;

// Handle pure feedback form submittal logic synchronously
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $status = 'danger';
        $message = 'Session verification invalid. Please resubmit the payload.';
    } else {
        $type = trim(filter_input(INPUT_POST, 'feedback_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
        $content = trim(filter_input(INPUT_POST, 'feedback_content', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');

        if ($type === '' || $content === '') {
            $status = 'warning';
            $message = 'Incomplete structural transmission parameters.';
        } else {
            try {
                $db = Database::getInstance();
                $stmt = $db->prepare("INSERT INTO feedback (feedback_type, feedback_content) VALUES (:t, :c)");
                $stmt->execute([':t' => $type, ':c' => $content]);
                
                $status = 'success';
                $message = 'Report securely stored to systems evaluation stack.';
            } catch (\Exception $e) {
                $status = 'danger';
                $message = 'Storage backend integration fault.';
                error_log("Feedback registration failure: " . $e->getMessage());
            }
        }
    }
}

$page_title = "Submit Security Feedback";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="card p-4 shadow-sm mx-auto border-0 max-w-xl">
    <h3 class="text-center fw-bold mb-3"><i class="fas fa-comment-dots text-primary me-2"></i> Feedback & Reporting</h3>
    <p class="text-muted text-center small mb-4">
        Report external pattern vulnerability configurations or provide operational structural suggestions. Logs interface directly with assigned Security Managers.
    </p>

    <?php if ($message): ?>
        <div class="alert alert-<?= $status ?> text-center small fw-semibold"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form action="/feedback.php" method="POST" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
        
        <div class="col-12">
            <label for="feedback_type" class="form-label fw-semibold small">Reporting Classification</label>
            <input type="text" name="feedback_type" id="feedback_type" class="form-control shadow-none" placeholder="e.g. Broken Authentication, UX Flaw, Model Extension" required>
        </div>

        <div class="col-12">
            <label for="feedback_content" class="form-label fw-semibold small">Context Description</label>
            <textarea name="feedback_content" id="feedback_content" rows="4" class="form-control shadow-none" placeholder="Elaborate operational symptoms or reference target criteria thoroughly..." required></textarea>
        </div>

        <div class="col-12 text-center mt-4">
            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-paper-plane me-2"></i> Push Record</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>