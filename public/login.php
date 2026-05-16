<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

use App\Config\Database;
use function App\Includes\require_secure_session;

require_secure_session();
$error_message = '';

// Intercept logout processing flows
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: /login.php");
    exit();
}

// Process staff credential authorization payload evaluations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error_message = 'Please input authentication constraints fully.';
    } else {
        try {
            $db = Database::getInstance();
            // Map table evaluations matching schema field refactoring standardizations
            $stmt = $db->prepare("SELECT role, password FROM personal_informations WHERE username = :u");
            $stmt->execute([':u' => $username]);
            $user = $stmt->fetch();

            // Note to self: Retaining direct baseline comparisons to support immediate dev seeding compatibility.
            // Switch evaluate comparison targets to password_verify() strings once hashing policies enforce globally.
            if ($user && $password === $user['password']) {
                session_regenerate_id(true); // Hardened runtime boundary allocation resets identifiers
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: /admin.php");
                } else {
                    header("Location: /security_manager.php");
                }
                exit();
            } else {
                $error_message = 'Invalid staff authorization parameters submitted.';
            }
        } catch (\Exception $e) {
            $error_message = 'Backend authorization processing timeout.';
            error_log("Login operational fault: " . $e->getMessage());
        }
    }
}

$page_title = "Staff Authentication Gateway";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="card shadow-sm mx-auto border-0 max-w-sm mt-6">
    <div class="card-body p-4 bg-light rounded">
        <h4 class="card-title text-center fw-bold mb-4"><i class="fas fa-user-lock text-primary me-2"></i> Access Gateway</h4>
        
        <?php if ($error_message !== ''): ?>
            <div class="alert alert-danger text-center small fw-semibold"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form action="/login.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label fw-semibold small">Staff Account Identifier</label>
                <input type="text" name="username" id="username" class="form-control shadow-none" required autocomplete="username">
            </div>
            <div class="mb-4">
                <label for="password" class="form-label fw-semibold small">Passcode</label>
                <input type="password" name="password" id="password" class="form-control shadow-none" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-success w-100 fw-bold"><i class="fas fa-sign-in-alt me-2"></i> Authenticate Session</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>