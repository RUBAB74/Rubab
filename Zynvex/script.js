
// 1. RENDER LIVE JOBS FROM MEMORY & UPDATE COUNTERS

document.addEventListener('DOMContentLoaded', () => {
    const listingsContainer = document.querySelector('.job-listings');

    if (listingsContainer) {
        const customJobs = JSON.parse(localStorage.getItem('customJobs')) || [];
        
        // Custom Jobs ko list mein render karne ka logic
        customJobs.forEach(job => {
            const isIntern = job.badge.toLowerCase().includes('intern');
            const badgeClass = isIntern ? 'badge-intern' : 'badge-full';
            
            const jobCardHTML = `
                <article class="job-card featured">
                    <div class="job-card-header">
                        <div>
                            <span class="company-name">${job.company}</span>
                            <h3>${job.title}</h3>
                        </div>
                        <span class="badge ${badgeClass}">${job.badge}</span>
                    </div>
                    <p class="job-desc">${job.desc}</p>
                    <div class="job-card-footer">
                        <span class="job-meta">📍 ${job.location}</span>
                        <span class="job-meta">💰 ${job.salary}</span>
                        <button class="btn-apply">Apply Now</button>
                    </div>
                </article>
            `;
            
            const heading = listingsContainer.querySelector('h2');
            if (heading) {
                heading.insertAdjacentHTML('afterend', jobCardHTML);
            }
        });

        // CRUCIAL BUG FIX: Micro-delay ke sath counter chalana taake DOM cards load ho sakein
        setTimeout(() => {
            const totalJobsCounter = document.getElementById('total-job-count') || document.getElementById('total-jobs-count');
            if (totalJobsCounter) {
                const totalCardsOnScreen = document.querySelectorAll('.job-card').length;
                totalJobsCounter.textContent = totalCardsOnScreen;
            }
        }, 50); // 50ms ka safe rendering browser break window
    }

    // Elements Selection Inside DOMContentLoaded (Safe Practice)
    const modal = document.getElementById('applyModal');
    const closeModalBtn = document.getElementById('closeModal');
    const applyForm = document.getElementById('applyForm');
    let selectedJobTitle = "Frontend Developer Intern"; // Default fallback

    // Event Delegation (Dono static aur dynamic cards ke liye handle karega)
    document.addEventListener('click', (e) => {
        if (e.target && e.target.classList.contains('btn-apply')) {
            e.preventDefault();
            
            const jobCard = e.target.closest('.job-card');
            if (jobCard) {
                const titleElement = jobCard.querySelector('h3');
                if (titleElement) {
                    selectedJobTitle = titleElement.textContent.trim();
                }
            }
            
            if (modal) modal.style.display = 'flex'; // Popup dikhao
        }
    });

    // Close modal handles
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', () => {
            if (modal) modal.style.display = 'none';
        });
    }

    window.addEventListener('click', (e) => {
        if (modal && e.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Form submit storage logic
   if (applyForm) {
    applyForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const nameInput = applyForm.querySelector('input[type="text"]').value;
        const options = { month: 'long', day: 'numeric', year: 'numeric' };
        const today = new Date().toLocaleDateString('en-US', options);

        const newApplication = {
            name: nameInput,
            position: selectedJobTitle,
            date: today,
            status: 'Under Review'
        };

        // Local Storage processing
        let savedApps = JSON.parse(localStorage.getItem('jobApplications')) || [];
        savedApps.unshift(newApplication);
        localStorage.setItem('jobApplications', JSON.stringify(savedApps));
        
        let currentTotal = parseInt(localStorage.getItem('totalApplications')) || 48;
        localStorage.setItem('totalApplications', currentTotal + 1);

        // UI ANIMATION INTERACTION (v0 + Lottie Vibe)
    
        applyForm.style.display = 'none';
        const successWrapper = document.getElementById('successAnimationWrapper');
        if (successWrapper) successWrapper.style.display = 'block';

        // 3 Seconds baad automatically popup bundle close ho jaye aur reset ho
        setTimeout(() => {
            if (modal) modal.style.display = 'none';
            applyForm.reset();
            
            // State ko reset karna taake agli application ke liye form wapis dikhe
            applyForm.style.display = 'block';
            if (successWrapper) successWrapper.style.display = 'none';
            
            // Dashboard values updates reload automatically
            window.location.reload();
        }, 3200); 
    });
}

   
    // 2. SEARCH & CHECKBOX FILTER LOGIC
    
    const searchTitleInput = document.getElementById('search-title');
    const searchLocationInput = document.getElementById('search-location');
    const searchButton = document.querySelector('.btn-search');
    const filterCheckboxes = document.querySelectorAll('.filter-checkbox');

    function filterJobs() {
        const titleValue = searchTitleInput ? searchTitleInput.value.toLowerCase() : '';
        const locationValue = searchLocationInput ? searchLocationInput.value.toLowerCase() : '';

        const activeFilters = [];
        filterCheckboxes.forEach(cb => {
            if (cb.checked) {
                activeFilters.push(cb.parentElement.textContent.trim().toLowerCase());
            }
        });

        // Loop chalane ke liye saare cards (existing + new) function ke andar select honge
        const allJobCards = document.querySelectorAll('.job-card');

        allJobCards.forEach(card => {
            const jobTitle = card.querySelector('h3').textContent.toLowerCase();
            const companyName = card.querySelector('.company-name').textContent.toLowerCase();
            const cardText = card.textContent.toLowerCase();

            const matchesTitle = jobTitle.includes(titleValue) || companyName.includes(titleValue);
            const matchesLocation = cardText.includes(locationValue);

            let matchesCheckboxes = true;
            if (activeFilters.length > 0) {
                matchesCheckboxes = activeFilters.some(filter => cardText.includes(filter));
            }

            if (matchesTitle && matchesLocation && matchesCheckboxes) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Filter Event Listeners
    if (searchTitleInput) searchTitleInput.addEventListener('input', filterJobs);
    if (searchLocationInput) searchLocationInput.addEventListener('input', filterJobs);
    filterCheckboxes.forEach(cb => cb.addEventListener('change', filterJobs));

    if (searchButton) {
        searchButton.addEventListener('click', (e) => {
            e.preventDefault();
            filterJobs();
        });
    }
});

// COMPANIES SEARCH & CATEGORY FILTER ENGINE

document.addEventListener('DOMContentLoaded', () => {
    // Companies page ke parameters check karna
    const companySearchInput = document.querySelector('input[placeholder*="company..."]') || document.getElementById('search-title'); 
    const companyCards = document.querySelectorAll('.job-card');

    // Agar hum companies.html page par hain
    if (companyCards.length > 0 && window.location.pathname.includes('companies.html')) {
        
        function filterCompanies() {
            const searchValue = companySearchInput ? companySearchInput.value.toLowerCase().trim() : '';
            
            companyCards.forEach(card => {
                const companyTitle = card.querySelector('h3').textContent.toLowerCase();
                const industryType = card.querySelector('.company-name').textContent.toLowerCase();
                const cardText = card.textContent.toLowerCase();

                // Match parameters
                const matchesSearch = companyTitle.includes(searchValue) || industryType.includes(searchValue);

                if (matchesSearch) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeIn 0.4s ease';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        if (companySearchInput) {
            companySearchInput.addEventListener('input', filterCompanies);
        }
    }
});