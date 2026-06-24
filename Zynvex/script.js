
// RENDER LIVE JOBS FROM MEMORY

document.addEventListener('DOMContentLoaded', () => {
    const listingsContainer = document.querySelector('.job-listings');

    if (listingsContainer) {
        const customJobs = JSON.parse(localStorage.getItem('customJobs')) || [];
        
        customJobs.forEach(job => {
            // Check krna k badge color full-time rakhna hay ya intern
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
            
            // Recent Openings ki heading (h2) k theek baad naya card inject krna
            const heading = listingsContainer.querySelector('h2');
            if (heading) {
                heading.insertAdjacentHTML('afterend', jobCardHTML);
            }
        });
    }
});
// Elements ko handle krny k leye select krna
const modal = document.getElementById('applyModal');
const closeModalBtn = document.getElementById('closeModal');
const applyButtons = document.querySelectorAll('.btn-apply'); // Aap k design k sab "Apply Now" buttons
const applyForm = document.getElementById('applyForm');

// 1. Jab kisi bhi "Apply Now" button par click ho toh modal dikhao
applyButtons.forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault(); // Page reload hony se rokna
        modal.style.display = 'flex'; // Dikhaye ga popup
    });
});

// 2. Jab 'X' button par click ho toh modal hide kr do
closeModalBtn.addEventListener('click', () => {
    modal.style.display = 'none';
});

// 3. Agar user popup k baahir background par click kray toh bhi bnd ho jaye
window.addEventListener('click', (e) => {
    if (e.target === modal) {
        modal.style.display = 'none';
    }
});

// 4. Form submit krny pr feedback
applyForm.addEventListener('submit', (e) => {
    e.preventDefault();
    alert('Application Submitted Successfully! 🚀');
    modal.style.display = 'none'; // Close modal
    applyForm.reset(); // Clear fields
});

// SEARCH & CHECKBOX FILTER LOGIC


// Elements ko select krna
const searchTitleInput = document.getElementById('search-title');
const searchLocationInput = document.getElementById('search-location');
const searchButton = document.querySelector('.btn-search');
const filterCheckboxes = document.querySelectorAll('.filter-checkbox');
const allJobCards = document.querySelectorAll('.job-card');

// Main function jo cards ko filter kray ga
function filterJobs() {
    const titleValue = searchTitleInput ? searchTitleInput.value.toLowerCase() : '';
    const locationValue = searchLocationInput ? searchLocationInput.value.toLowerCase() : '';

    // Checked checkboxes ki list banana
    const activeFilters = [];
    filterCheckboxes.forEach(cb => {
        if (cb.checked) {
            // Checkbox k barabar likha hua text (e.g., "Full-time") layout se lena
            activeFilters.push(cb.parentElement.textContent.trim().toLowerCase());
        }
    });

    // Ek ek job card par loop chalana aur check krna
    allJobCards.forEach(card => {
        const jobTitle = card.querySelector('h3').textContent.toLowerCase();
        const companyName = card.querySelector('.company-name').textContent.toLowerCase();
        
        // Card k andar se badge text aur baqi elements lena
        const cardText = card.textContent.toLowerCase();

        // 1. Title aur Company name match krna
        const matchesTitle = jobTitle.includes(titleValue) || companyName.includes(titleValue);
        
        // 2. Location match krna
        const matchesLocation = cardText.includes(locationValue);

        // 3. Checkboxes filters match krna
        let matchesCheckboxes = true;
        if (activeFilters.length > 0) {
            // Agar koi checkbox selected hai, toh check kro k card mein us ka text majood hai ya nahi
            matchesCheckboxes = activeFilters.some(filter => cardText.includes(filter));
        }

        // Agar teeno conditions sach hain, toh card dikhao, warna chupa do
        if (matchesTitle && matchesLocation && matchesCheckboxes) {
            card.style.display = 'block'; // Show card
        } else {
            card.style.display = 'none';  // Hide card
        }
    });
}

// Event Listeners: Jab user input box mein kuch type kray ga, automatic filter chalay ga
if (searchTitleInput) searchTitleInput.addEventListener('input', filterJobs);
if (searchLocationInput) searchLocationInput.addEventListener('input', filterJobs);

// Checkboxes pr tick lagane pr automatic filter chalana
filterCheckboxes.forEach(cb => cb.addEventListener('change', filterJobs));

// Search button pr click krne pr bhi filter chalana
if (searchButton) {
    searchButton.addEventListener('click', (e) => {
        e.preventDefault();
        filterJobs();
    });
}