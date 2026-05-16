<?php
$page_title = "Homepage - Privacy Knowledge Base";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="text-center max-w-4xl mx-auto py-8">
    <h2 class="display-6 fw-bold mb-4">Welcome to the Privacy Knowledge Base</h2>
    <p class="lead text-muted mb-6">
        Explore up-to-date documentation, structured design solutions, and industry-standard best practices regarding privacy-preserving design architectures.
    </p>
    
    <div class="alert alert-warning d-inline-block shadow-sm my-4 text-start" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Disclaimer:</strong> Provided architectural strategies serve educational reference targets. Ensure distinct organizational context verification before field execution.
    </div>

    <div class="row g-4 mt-6 justify-content-center">
        <div class="col-md-5">
            <div class="card h-100 shadow-sm border-0 bg-light p-4">
                <i class="fas fa-search fa-3x text-primary mb-3"></i>
                <h4 class="fw-bold">Dynamic Searching</h4>
                <p class="text-muted small">Query operational guidelines instantly mapping GDPR and OWASP structural classifications.</p>
                <a href="/search.php" class="btn btn-outline-primary mt-auto">Open Index</a>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card h-100 shadow-sm border-0 bg-light p-4">
                <i class="fas fa-sliders-h fa-3x text-success mb-3"></i>
                <h4 class="fw-bold">Advanced Filtering</h4>
                <p class="text-muted small">Filter records by precise ISO design lifecycle target models and engineering principles.</p>
                <a href="/advanced-search.php" class="btn btn-outline-success mt-auto">Filter Engine</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>