// Note to self: Retain robust client-side async live parameter querying logic without external jQuery requirements.
// Applying debounce handlers directly to minimize payload operations against internal database execution boundaries.

// --- BFCache Mitigation Strategy ---
// Enforce strict client-side state invalidation.
// If the browser attempts to restore a stale DOM snapshot from the Back-Forward Cache (bfcache),
// we force a hard reload to ensure server-side RBAC and session validation controllers are executed.
window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        window.location.reload();
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    if (searchInput && searchResults) {
        let debounceTimer;

        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            const query = searchInput.value.trim();

            if (query.length < 2) {
                searchResults.innerHTML = '';
                return;
            }

            // Execute network payload retrieval asynchronously
            debounceTimer = setTimeout(() => {
                fetch(`/search.php?ajax=true&q=${encodeURIComponent(query)}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Data processing interface timeout');
                        return response.json();
                    })
                    .then(data => renderResultsTable(data))
                    .catch(err => {
                        console.error('Data retrieval logic execution processing errors:', err);
                        searchResults.innerHTML = `<div class="alert alert-danger text-center small">Interface parsing fault evaluating data index.</div>`;
                    });
            }, 300);
        });
    }

    function renderResultsTable(items) {
        if (items.length === 0) {
            searchResults.innerHTML = `<div class="alert alert-warning text-center small m-0">No pattern records matching specified parameter constraint string.</div>`;
            return;
        }

        let html = `
            <div class="card border-0 shadow-sm p-4">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle border m-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>Classification Target</th>
                                <th>Privacy Strategy</th>
                                <th>Mapped GDPR Article</th>
                                <th>ISO Focus Stage</th>
                                <th>MVC Tier</th>
                            </tr>
                        </thead>
                        <tbody>
        `;

        items.forEach(node => {
            // Apply defensive HTML-escaping patterns strictly preventing malicious string evaluation anomalies
            // Note to self: Enforcing deep linking interface routing directly to designated node documentation views
            html += `
                <tr>
                    <td class="fw-bold">
                        <a href="/pattern.php?id=${encodeURIComponent(node.id)}" class="text-primary text-decoration-none">
                            ${escapeHtml(node.name)} <i class="fas fa-external-link-alt small ms-1"></i>
                        </a>
                    </td>
                    <td class="text-center"><span class="badge bg-secondary">${escapeHtml(node.strategies)}</span></td>
                    <td class="small">${escapeHtml(node.gdpr_article)}</td>
                    <td class="small text-center">${escapeHtml(node.iso_phase)}</td>
                    <td class="text-center"><span class="badge bg-info text-dark">${escapeHtml(node.collocazione_mvc)}</span></td>
                </tr>
            `;
        });

        html += `</tbody></table></div></div>`;
        searchResults.innerHTML = html;
    }

    function escapeHtml(rawStr) {
        if (!rawStr) return '';
        return String(rawStr).replace(/[&<>'"]/g, 
            matched => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[matched] || matched)
        );
    }
});