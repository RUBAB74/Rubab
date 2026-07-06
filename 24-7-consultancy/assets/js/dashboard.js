document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.getElementById("dashTableBody");
    const postForm = document.getElementById("postIntakeForm");

    const loadTableData = () => {
        if (!tableBody) return;
        const currentData = JSON.parse(localStorage.getItem('globalIntakes')) || [];
        tableBody.innerHTML = "";
        currentData.forEach(row => {
            tableBody.innerHTML += `
                <tr>
                    <td style="font-weight:700; color:var(--accent-vibrant)">${row.id}</td>
                    <td>${row.title} at <strong>${row.university}</strong></td>
                    <td>${row.region}</td>
                    <td><span style="background:#e0f2fe; color:#0369a1; padding:4px 8px; border-radius:4px; font-size:0.8rem; font-weight:600;">${row.status}</span></td>
                </tr>
            `;
        });
    };

    if (postForm) {
        postForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const currentData = JSON.parse(localStorage.getItem('globalIntakes')) || [];
            
            const newIntake = {
                id: "TK-" + Math.floor(1000 + Math.random() * 9000),
                university: document.getElementById("intakeUni").value,
                title: document.getElementById("intakeTitle").value,
                region: document.getElementById("intakeRegion").value,
                status: "Active Intake"
            };

            currentData.unshift(newIntake);
            localStorage.setItem('globalIntakes', JSON.stringify(currentData));
            window.location.href = "index.html"; // Back to main overview table
        });
    }

    loadTableData();
});
