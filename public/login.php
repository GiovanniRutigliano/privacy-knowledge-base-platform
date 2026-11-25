<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

use App\Config\Database;
use function App\Includes\require_secure_session;

require_secure_session();
$error_message = '';

// Intercept logout processing flows
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'logout') {
    \App\Includes\require_csrf_token($_POST['csrf_token'] ?? null);
    \App\Includes\end_session();
    header('Location: /login.php', true, 303);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    \App\Includes\require_csrf_token($_POST['csrf_token'] ?? null);
    $username = trim(filter_input(INPUT_POST, 'username') ?? '');
    $password = filter_input(INPUT_POST, 'password') ?? '';

    if ($username === '' || $password === '') {
        $error_message = 'Enter your username and password.';
    } else {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT role, password FROM personal_informations WHERE username = :u");
            $stmt->execute([':u' => $username]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION = [];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: /admin.php");
                } else {
                    header("Location: /security_manager.php");
                }
                exit();
            } else {
                $error_message = 'The username or password is incorrect.';
            }
        } catch (\Exception $e) {
            $error_message = 'Sign-in is temporarily unavailable.';
            error_log("Login operational fault: " . $e->getMessage());
        }
    }
}

$page_title = "Staff Authentication Gateway";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="card shadow-sm mx-auto border-0 max-w-sm mt-6">
    <div class="card-body p-4 bg-light rounded">
        <h4 class="card-title text-center fw-bold mb-4"><i class="fas fa-user-lock text-primary me-2"></i> Staff sign-in</h4>
        
        <?php if ($error_message !== ''): ?>
            <div class="alert alert-danger text-center small fw-semibold"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form action="/login.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= \App\Includes\get_csrf_token() ?>">
            <div class="mb-3">
                <label for="username" class="form-label fw-semibold small">Username</label>
                <input type="text" name="username" id="username" class="form-control shadow-none" required autocomplete="username">
            </div>
            <div class="mb-4">
                <label for="password" class="form-label fw-semibold small">Password</label>
                <input type="password" name="password" id="password" class="form-control shadow-none" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-success w-100 fw-bold"><i class="fas fa-sign-in-alt me-2"></i> Sign in</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>