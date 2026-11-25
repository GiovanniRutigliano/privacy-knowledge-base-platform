<p class="result-status" id="searchStatus" role="status">
    <?= $error ? 'Search is temporarily unavailable.' : ($results === [] ? 'No matching patterns.' : count($results) . ' matching patterns') ?>
</p>
<ul class="pattern-list" id="searchResults">
<?php foreach ($results as $row): ?>
    <li>
        <h2><a href="/pattern.php?id=<?= (int)$row['id'] ?>"><?= htmlspecialchars($row['name']) ?></a></h2>
        <p><?= htmlspecialchars(implode(' · ', array_filter([$row['strategies'], $row['iso_phase'], $row['collocazione_mvc']]))) ?></p>
    </li>
<?php endforeach; ?>
</ul>
