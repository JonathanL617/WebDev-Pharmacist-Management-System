document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("drugForm");
    const result = document.getElementById("result");

    // CLEAN DRUG NAME — removes {pack garbage} and [brackets]
    function cleanDrugName(raw) {
        if (!raw) return "Unknown Drug";
        let name = raw;
        // Remove {100 (....) / 24 (...)} Pack part
        name = name.replace(/^\{[^}]*\}\s*Pack\s*/i, '');
        // Remove anything in [square brackets] at the end
        name = name.replace(/\s*\[.*?\]$/, '');
        // Remove trailing dose info in parentheses (e.g. (500 MG))
        name = name.replace(/\s*\([^)]*MG[^)]*\)$/i, '');
        // Clean up extra spaces
        name = name.replace(/\s+/g, ' ').trim();
        return name || "Drug Pack";
    }

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const name = document.getElementById("drugName").value.trim();
        if (!name) {
            result.innerHTML = '<div class="alert alert-warning">Please enter a drug name</div>';
            return;
        }

        // Loading spinner
        result.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary mb-3" style="width:4rem;height:4rem;"></div>
                <p class="fs-4 text-muted">Searching RxNorm database...</p>
            </div>`;

        try {
            const response = await fetch(`${window.BASE_URL}/app/controller/APIController.php?name=${encodeURIComponent(name)}`);
            
            // BULLETPROOF: Read as text first → clean → parse
            const text = await response.text();
            const cleanJson = text.trim();
            const data = JSON.parse(cleanJson);

            if (data.error) {
                result.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                return;
            }

            // BEAUTIFUL RESULT — your exact design
            let html = `
                <div class="result-box">
                    <h4 class="mb-4 fw-bold">${cleanDrugName(data.name)}</h4>
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>RxNorm ID:</strong> <code>${data.rxcui}</code></div>
                        <div class="col-md-6"><strong>Type:</strong> <span class="badge bg-${data.isPack ? 'warning' : 'success'}">${data.isPack ? 'Drug Pack' : 'Single Drug'}</span></div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6"><strong>Dose Form:</strong> ${data.doseForm}</div>
                        <div class="col-md-6"><strong>Strength:</strong> ${data.strength}</div>
                    </div>`;

            if (data.brands && data.brands.length > 0) {
                html += `<div class="mb-4"><strong>Known Brands:</strong> <span class="text-primary">${data.brands.join(" • ")}</span></div>`;
            }

            html += `</div>`;

            // Active Ingredients (Single Drug)
            if (!data.isPack && data.ingredients && data.ingredients.length > 0) {
                html += `
                    <div class="result-box mt-4">
                        <h5 class="text-success mb-3"><i class="bi bi-capsule"></i> Active Ingredients</h5>
                        <div class="fs-5 fw-medium">${data.ingredients.join(" + ")}</div>
                    </div>`;
            }

            // Pack Contents
            if (data.isPack && data.packComponents && data.packComponents.length > 0) {
                html += `<div class="result-box mt-4"><h5 class="text-primary mb-3">Pack Contains:</h5>`;
                data.packComponents.forEach(c => {
                    const cleanCompName = cleanDrugName(c.name);
                    html += `<div class="mb-3 p-3 border rounded bg-light shadow-sm">
                        <strong class="text-dark">${c.quantity ? c.quantity + " × " : ""}${cleanCompName}</strong>
                        ${c.ingredients && c.ingredients.length > 0 ? 
                            `<div class="mt-2 text-muted"><em>→ ${c.ingredients.join(" + ")}</em></div>` : ''
                        }
                    </div>`;
                });
                html += `</div>`;
            }

            result.innerHTML = html;

        } catch (err) {
            console.error("Error:", err);
            result.innerHTML = '<div class="alert alert-danger">Failed to load. Please try again.</div>';
        }
    });

    // Clear result when typing new search
    document.getElementById("drugName").addEventListener("input", function () {
        if (!this.value.trim()) result.innerHTML = "";
    });
});