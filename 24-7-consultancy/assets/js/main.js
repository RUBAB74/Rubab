// Day 5 & 6: Data Repository Layout initialization using LocalStorage
const defaultIntakes = [
    { id: "TK-9021", university: "University of Manchester", title: "MSc Advanced Computational Science", region: "United Kingdom", status: "Verified Routing" },
    { id: "TK-4412", university: "Technical University of Munich", title: "BSc Data Engineering Track", region: "Germany", status: "Pipeline Evaluation" },
    { id: "TK-7781", university: "Stanford Digital Infrastructure", title: "Post-Doc Artificial Intelligence Systems", region: "United States", status: "Active Intake" }
];

if (!localStorage.getItem('globalIntakes')) {
    localStorage.setItem('globalIntakes', JSON.stringify(defaultIntakes));
}

// Day 7: Filter Engine Logic
document.addEventListener("DOMContentLoaded", () => {
    const target = document.getElementById("listingsTarget");
    if (!target) return; // Prevent script crash if not on listings screen

    const dataset = JSON.parse(localStorage.getItem('globalIntakes'));
    
    const renderData = (array) => {
        target.innerHTML = "";
        if(array.length === 0) {
            target.innerHTML = `<p style="grid-column: 1/-1; text-align:center; color:#64748b;">No active intake streams found matching queries.</p>`;
            return;
        }
        array.forEach(item => {
            target.innerHTML += `
                <div class="job-card">
                    <div>
                        <div class="job-badge">${item.region}</div>
                        <h3 class="job-title">${item.title}</h3>
                        <p class="job-meta">🏢 ${item.university}</p>
                    </div>
                    <span style="font-size:0.85rem; font-weight:600; color:var(--accent-vibrant)">System Route: ${item.status}</span>
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

    search.addEventListener("input", performFilter);
    filter.addEventListener("change", performFilter);
});

// Day 8: Authentication Identity Verification Check
const loginForm = document.getElementById("loginForm");
if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
        e.preventDefault();
        // Custom simple security check for demonstration
        window.location.href = "dashboard/index.html";
    });
}
