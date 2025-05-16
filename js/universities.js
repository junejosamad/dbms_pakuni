document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-box input');
    const provinceFilter = document.querySelector('select[name="province"]');
    const programFilter = document.querySelector('select[name="program"]');
    const levelFilter = document.querySelector('select[name="level"]');
    const universityCards = document.querySelectorAll('.university-card');
    const pageNumbers = document.querySelector('.page-numbers');
    const prevBtn = document.querySelector('.page-btn.prev');
    const nextBtn = document.querySelector('.page-btn.next');

    let currentPage = 1;
    const cardsPerPage = 6;
    let filteredUniversities = Array.from(universityCards);

    // Function to filter universities based on search and filters
    function filterUniversities() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedProvince = provinceFilter.value;
        const selectedProgram = programFilter.value;
        const selectedLevel = levelFilter.value;

        filteredUniversities = Array.from(universityCards).filter(card => {
            const name = card.querySelector('h3').textContent.toLowerCase();
            const location = card.querySelector('.location').textContent.toLowerCase();
            const programs = Array.from(card.querySelectorAll('.program-tag'))
                .map(tag => tag.textContent.toLowerCase());

            const matchesSearch = name.includes(searchTerm) || 
                                location.includes(searchTerm) ||
                                programs.some(program => program.includes(searchTerm));
            
            const matchesProvince = selectedProvince === 'all' || 
                                  location.includes(selectedProvince.toLowerCase());
            
            const matchesProgram = selectedProgram === 'all' || 
                                 programs.some(program => program.includes(selectedProgram.toLowerCase()));
            
            const matchesLevel = selectedLevel === 'all' || 
                               programs.some(program => program.includes(selectedLevel.toLowerCase()));

            return matchesSearch && matchesProvince && matchesProgram && matchesLevel;
        });

        updatePagination();
        showPage(1);
    }

    // Function to show a specific page of universities
    function showPage(page) {
        const startIndex = (page - 1) * cardsPerPage;
        const endIndex = startIndex + cardsPerPage;

        universityCards.forEach(card => card.style.display = 'none');
        
        filteredUniversities.slice(startIndex, endIndex).forEach(card => {
            card.style.display = 'block';
        });

        updatePageNumbers();
    }

    // Function to update pagination controls
    function updatePagination() {
        const totalPages = Math.ceil(filteredUniversities.length / cardsPerPage);
        
        if (totalPages <= 1) {
            document.querySelector('.pagination').style.display = 'none';
        } else {
            document.querySelector('.pagination').style.display = 'flex';
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;
        }
    }

    // Function to update page numbers display
    function updatePageNumbers() {
        const totalPages = Math.ceil(filteredUniversities.length / cardsPerPage);
        pageNumbers.textContent = `Page ${currentPage} of ${totalPages}`;
    }

    // Event listeners
    searchInput.addEventListener('input', filterUniversities);
    provinceFilter.addEventListener('change', filterUniversities);
    programFilter.addEventListener('change', filterUniversities);
    levelFilter.addEventListener('change', filterUniversities);

    prevBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            showPage(currentPage);
        }
    });

    nextBtn.addEventListener('click', () => {
        const totalPages = Math.ceil(filteredUniversities.length / cardsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            showPage(currentPage);
        }
    });

    // Initialize the page
    filterUniversities();
}); 