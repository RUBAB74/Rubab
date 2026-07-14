// Data Repository Layout initialization using LocalStorage
const defaultIntakes = [
    { id: "TK-9021", university: "University of Manchester", title: "MSc Advanced Computational Science", region: "United Kingdom", status: "Verified Routing" },
    { id: "TK-4412", university: "Technical University of Munich", title: "BSc Data Engineering Track", region: "Germany", status: "Pipeline Evaluation" },
    { id: "TK-7781", university: "Stanford Digital Infrastructure", title: "Post-Doc Artificial Intelligence Systems", region: "United States", status: "Active Intake" }
];

if (!localStorage.getItem('globalIntakes')) {
    localStorage.setItem('globalIntakes', JSON.stringify(defaultIntakes));
}

document.addEventListener("DOMContentLoaded", () => {
    const target = document.getElementById("listingsTarget");
    const modal = document.getElementById("applyModal");
    const overlay = document.getElementById("modalOverlay");
    const closeBtn = document.getElementById("closeModalBtn");
    const applyForm = document.getElementById("studentApplyForm");
    let selectedProgram = ""; // Variable to hold the program name student is applying for

    if (!target) return; // Exit if not on listings page

    const dataset = JSON.parse(localStorage.getItem('globalIntakes'));
    
    // Render Job Cards with "Apply Route" Interaction Trigger
    const renderData = (array) => {
        target.innerHTML = "";
        if(array.length === 0) {
            target.innerHTML = `<p style="grid-column: 1/-1; text-align:center; color:#64748b;">No active intake streams found matching queries.</p>`;
            return;
        }
        array.forEach(item => {
            target.innerHTML += `
                <div class="job-card" style="cursor: pointer;" onclick="openApplyModal('${item.title} at ${item.university}')">
                    <div>
                        <div class="job-badge"><i class="bi bi-geo-alt-fill"></i> ${item.region}</div>
                        <h3 class="job-title">${item.title}</h3>
                        <p class="job-meta"><i class="bi bi-building"></i> ${item.university}</p>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:15px; padding-top:10px; border-top:1px solid rgba(0,0,0,0.05)">
                        <span style="font-size:0.85rem; font-weight:600; color:var(--airmail-red)">
                            <i class="bi bi-send-fill"></i> Click to Apply Route
                        </span>
                        <i class="bi bi-arrow-right-short" style="font-size:1.5rem; color:var(--airmail-red)"></i>
                    </div>
                </div>
            `;
        });
    };

    renderData(dataset);

    // Search and Filter Events
    const search = document.getElementById("searchBar");
    const filter = document.getElementById("regionFilter");

    const performFilter = () => {
        const query = search.value.toLowerCase();
        const region = filter.value;

        const filtered = dataset.filter(data => {
            const matchesQuery = data.title.toLowerCase().includes(query) || data.university.toLowerCase().includes(query);
            const matchesRegion = region === "all" || data.region === region;
            return matchesQuery && matchesRegion;
        });
        renderData(filtered);
    };

    if(search) search.addEventListener("input", performFilter);
    if(filter) filter.addEventListener("change", performFilter);

    // Modal Display Functions Attached globally
    window.openApplyModal = (programDetails) => {
        selectedProgram = programDetails; // Store the program
        modal.style.display = "block";
        overlay.style.display = "block";
    };

    const closeModal = () => {
        modal.style.display = "none";
        overlay.style.display = "none";
        applyForm.reset();
    };

    if(closeBtn) closeBtn.addEventListener("click", closeModal);
    if(overlay) overlay.addEventListener("click", closeModal);

    // Handle Student Application Submission Form
    if (applyForm) {
        applyForm.addEventListener("submit", (e) => {
            e.preventDefault();
            
            // Get Student Inputs
            const sName = document.getElementById("studentName").value;
            const sEmail = document.getElementById("studentEmail").value;
            const sEdu = document.getElementById("studentEdu").value;

            // Retrieve existing applications or start fresh
            const existingApps = JSON.parse(localStorage.getItem('studentApplications')) || [];

            // Add new application object
            const newApplication = {
                id: "APP-" + Math.floor(1000 + Math.random() * 9000),
                name: sName,
                email: sEmail,
                qualification: sEdu,
                program: selectedProgram,
                date: new Date().toLocaleDateString()
            };

            existingApps.unshift(newApplication);
            localStorage.setItem('studentApplications', JSON.stringify(existingApps));

            alert(`Success! Your application for "${selectedProgram}" has been saved and dispatched to the control panel.`);
            closeModal();
        });
    }
});

// Authentication Identity Verification Check
const loginForm = document.getElementById("loginForm");
if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
        e.preventDefault();
        window.location.href = "dashboard/index.html"; // Open admin desk panel dashboard
    });
}