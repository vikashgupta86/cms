document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const downloadsList = document.getElementById('downloadsList');
    const noResults = document.getElementById('noResults');
    const items = document.querySelectorAll('.download-item');
    
    if (searchInput && downloadsList && noResults && items.length > 0) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            let visibleCount = 0;
            
            items.forEach(item => {
                const title = item.getAttribute('data-title')?.toLowerCase() || '';
                const desc = item.getAttribute('data-desc')?.toLowerCase() || '';
                const date = item.getAttribute('data-date')?.toLowerCase() || '';
                
                if (title.includes(searchTerm) || desc.includes(searchTerm) || date.includes(searchTerm)) {
                    item.style.display = 'flex';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
            
            if (visibleCount === 0) {
                downloadsList.style.display = 'none';
                noResults.style.display = 'block';
            } else {
                downloadsList.style.display = 'grid';
                noResults.style.display = 'none';
            }
        });
    }
});

