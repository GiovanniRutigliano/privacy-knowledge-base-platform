window.addEventListener('pageshow', event => {
    if (event.persisted) window.location.reload();
});

const input = document.querySelector('#searchInput');
const results = document.querySelector('#searchResults');
const status = document.querySelector('#searchStatus');

if (input && results && status) {
    let timer;
    let controller;
    input.addEventListener('input', () => {
        clearTimeout(timer);
        controller?.abort();
        timer = setTimeout(() => search(input.value.trim()), 250);
    });

    async function search(query) {
        if (!query) {
            results.replaceChildren();
            status.textContent = 'Submit an empty search to browse all patterns.';
            return;
        }

        const current = new AbortController();
        controller = current;
        status.textContent = 'Searching…';
        try {
            const params = new URLSearchParams({ ajax: 'true', q: query });
            const response = await fetch(`/search.php?${params}`, { signal: current.signal });
            if (!response.ok) throw new Error('Search unavailable');
            const matches = await response.json();
            if (current.signal.aborted) return;
            const items = matches.map(pattern => {
                const item = document.createElement('li');
                const heading = document.createElement('h2');
                const link = document.createElement('a');
                const summary = document.createElement('p');
                link.href = `/pattern.php?id=${encodeURIComponent(pattern.id)}`;
                link.textContent = pattern.name;
                summary.textContent = [pattern.strategies, pattern.iso_phase, pattern.collocazione_mvc]
                    .filter(Boolean).join(' · ');
                heading.append(link);
                item.append(heading, summary);
                return item;
            });
            results.replaceChildren(...items);
            status.textContent = items.length ? `${items.length} matching patterns` : 'No matching patterns.';
        } catch {
            if (current.signal.aborted) return;
            results.replaceChildren();
            status.textContent = 'Search is temporarily unavailable.';
        }
    }
}
