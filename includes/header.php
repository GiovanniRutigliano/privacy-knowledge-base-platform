<?php
require_once __DIR__ . '/auth.php';
\App\Includes\require_secure_session();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Privacy Knowledge Base') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
    <a class="visually-hidden-focusable skip-link" href="#main-content">Skip to content</a>
    <header class="main-header">
        <a class="site-title" href="/">Privacy Knowledge Base</a>
        <nav aria-label="Main navigation">
            <ul>
                <?php if (isset($_SESSION['role'])): ?>
                    
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li><a href="/admin.php" class="nav-link <?= $current_page === 'admin.php' ? 'active' : '' ?>"><i class="fas fa-user-shield"></i> Manage patterns</a></li>
                    
                    <?php elseif ($_SESSION['role'] === 'security manager'): ?>
                        <li><a href="/search.php" class="nav-link <?= $current_page === 'search.php' ? 'active' : '' ?>"><i class="fas fa-search"></i> Search</a></li>
                        <li><a href="/advanced-search.php" class="nav-link <?= $current_page === 'advanced-search.php' ? 'active' : '' ?>"><i class="fas fa-sliders-h"></i> Advanced</a></li>
                        <li><a href="/security_manager.php" class="nav-link <?= $current_page === 'security_manager.php' ? 'active' : '' ?>"><i class="fas fa-shield-alt"></i> Security review</a></li>
                    <?php endif; ?>

                    <li><form action="/login.php" method="post" class="m-0">
                        <input type="hidden" name="action" value="logout">
                        <input type="hidden" name="csrf_token" value="<?= \App\Includes\get_csrf_token() ?>">
                        <button class="btn btn-sm btn-outline-light" type="submit">Sign out</button>
                    </form></li>

                <?php else: ?>
                    <li><a href="/index.php" class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="/search.php" class="nav-link <?= $current_page === 'search.php' ? 'active' : '' ?>"><i class="fas fa-search"></i> Search</a></li>
                    <li><a href="/advanced-search.php" class="nav-link <?= $current_page === 'advanced-search.php' ? 'active' : '' ?>"><i class="fas fa-sliders-h"></i> Advanced</a></li>
                    <li><a href="/contact.php" class="nav-link <?= $current_page === 'contact.php' ? 'active' : '' ?>"><i class="fas fa-envelope"></i> Contact</a></li>
                    <li><a href="/feedback.php" class="nav-link <?= $current_page === 'feedback.php' ? 'active' : '' ?>"><i class="fas fa-comment-dots"></i> Feedback</a></li>
                    <li><a href="/login.php" class="nav-link <?= $current_page === 'login.php' ? 'active' : '' ?>"><i class="fas fa-lock"></i> Staff Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main id="main-content" tabindex="-1" class="container my-4">