document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.getElementById("dashTableBody");
    const appsTableBody = document.getElementById("appsTableBody");
    const postForm = document.getElementById("postIntakeForm");

    // 1. Load Intake Slots Table
    const loadTableData = () => {
        if (!tableBody) return;
        const currentData = JSON.parse(localStorage.getItem('globalIntakes')) || [];
        tableBody.innerHTML = "";
        
        if(currentData.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="4" style="text-align:center; color:var(--muted)">No intake slots registered yet.</td></tr>`;
            return;
        }

        currentData.forEach(row => {
            tableBody.innerHTML += `
                <tr>
                    <td style="font-weight:700; color:var(--airmail-red)">${row.id}</td>
                    <td>${row.title} at <strong>${row.university}</strong></td>
                    <td>${row.region}</td>
                    <td><span style="background:var(--gold-soft); color:var(--gold); padding:4px 10px; border-radius:3px; font-size:0.8rem; font-weight:600; border: 1px dashed var(--gold)">${row.status}</span></td>
                </tr>
            `;
        });
    };

    // 2. Load Student Applications Table
    const loadStudentApps = () => {
        if (!appsTableBody) return;
        const appData = JSON.parse(localStorage.getItem('studentApplications')) || [];
        appsTableBody.innerHTML = "";

        if (appData.length === 0) {
            appsTableBody.innerHTML = `<tr><td colspan="5" style="text-align:center; color:var(--muted)">No student application routes received yet.</td></tr>`;
            return;
        }

        appData.forEach(app => {
            appsTableBody.innerHTML += `
                <tr>
                    <td style="font-weight:700; color:var(--gold)">${app.id}</td>
                    <td>
                        <strong>${app.name}</strong><br>
                        <span style="font-size:0.8rem; color:var(--muted);">${app.email}</span>
                    </td>
                    <td>${app.program}</td>
                    <td><span style="background:#e0f2fe; color:#0369a1; padding:3px 8px; border-radius:3px; font-size:0.8rem; font-weight:600;">${app.qualification}</span></td>
                    <td style="font-size:0.85rem; color:var(--muted);">${app.date}</td>
                </tr>
            `;
        });
    };

    // 3. Handle Intake Submission
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
            
            // Redirect back to Dashboard Home
            window.location.href = "index.html"; 
        });
    }

    loadTableData();
    loadStudentApps();
});