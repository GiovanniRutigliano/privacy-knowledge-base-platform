<?php
$page_title = "Contact Support";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="card p-5 shadow-sm mx-auto border-0 bg-light text-center max-w-2xl">
    <i class="fas fa-headset fa-4x text-primary mb-4"></i>
    <h3 class="fw-bold mb-3">Service Support Desk</h3>
    <p class="text-muted mb-4">
        Encountering functional inconsistencies, access issues, or requesting organizational deployment verification assistance? Reach out to our engineering operations center directly.
    </p>
    
    <div class="p-4 bg-white rounded shadow-sm border d-inline-block mx-auto mb-4">
        <span class="small fw-bold text-uppercase text-muted d-block mb-1">Direct Operations Gateway</span>
        <a href="mailto:support@privacy-knowledgebase.internal" class="text-primary fw-bold text-decoration-none fs-5">
            <i class="fas fa-envelope me-2"></i>support@privacy-knowledgebase.internal
        </a>
    </div>

    <p class="small text-muted">Average response clearance turnaround evaluates within 24 standard maintenance operational hours.</p>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>