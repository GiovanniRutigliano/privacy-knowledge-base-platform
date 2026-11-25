<?php
$page_title = 'Privacy Knowledge Base';
require_once __DIR__ . '/../includes/header.php';
?>
<header class="knowledge-intro">
    <p class="eyebrow">Design reference</p>
    <h1>Privacy by design, pattern by pattern.</h1>
    <p class="lead">Explore 25 approaches to designing software that handles personal data carefully.</p>
    <p><a href="/search.php" class="btn btn-primary">Browse the patterns</a>
    <a href="/advanced-search.php" class="btn btn-outline-primary">Open filters</a></p>
</header>
<section class="source-notes" aria-labelledby="reference-title">
    <h2 id="reference-title">Using the catalog</h2>
    <p>Each pattern describes its purpose, use cases, design strategies, and academic mappings.
        Check the original sources before applying a mapping to a particular system.</p>
    <ul>
        <li><a href="https://eur-lex.europa.eu/eli/reg/2016/679/oj">EU General Data Protection Regulation</a></li>
        <li><a href="https://www.iso.org/standard/71670.html">ISO/IEC 27701:2019 reference record</a></li>
        <li><a href="https://owasp.org/Top10/2021/">OWASP Top 10 (2021)</a></li>
        <li><a href="https://cwe.mitre.org/">MITRE Common Weakness Enumeration</a></li>
    </ul>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
