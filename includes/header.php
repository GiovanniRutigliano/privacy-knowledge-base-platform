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
    <header class="main-header">
        <h1>Privacy Knowledge Base</h1>
        <nav>
            <ul>
                <?php if (isset($_SESSION['role'])): ?>
                    
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li><a href="/admin.php" class="nav-link <?= $current_page === 'admin.php' ? 'active' : '' ?>"><i class="fas fa-user-shield"></i> Admin Panel</a></li>
                    
                    <?php elseif ($_SESSION['role'] === 'security manager'): ?>
                        <li><a href="/search.php" class="nav-link <?= $current_page === 'search.php' ? 'active' : '' ?>"><i class="fas fa-search"></i> Search</a></li>
                        <li><a href="/advanced-search.php" class="nav-link <?= $current_page === 'advanced-search.php' ? 'active' : '' ?>"><i class="fas fa-sliders-h"></i> Advanced</a></li>
                        <li><a href="/security_manager.php" class="nav-link <?= $current_page === 'security_manager.php' ? 'active' : '' ?>"><i class="fas fa-shield-alt"></i> SM Dashboard</a></li>
                    <?php endif; ?>

                    <li><a href="/login.php?action=logout" class="nav-link btn-danger text-white"><i class="fas fa-sign-out-alt"></i> Logout</a></li>

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
    <main class="container my-4">