document.getElementById("drugForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const name = document.getElementById("drugName").value.trim();
    const result = document.getElementById("result");

    if (!name) {
        result.innerHTML = `<div class="alert alert-warning">Please enter a drug name.</div>`;
        return;
    }

    result.innerHTML = `<div class="text-center p-3">Searching...</div>`;

    const res = await fetch("drug_controller.php?name=" + encodeURIComponent(name));
    const data = await res.json();

    if (data.error) {
        result.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
        return;
    }

    let html = `
        <div class="result-box">
            <h4>${data.name}</h4>
            <div><strong>RxNorm ID:</strong> ${data.rxcui}</div>
            <div><strong>Dose Form:</strong> ${data.doseForm}</div>
            <div><strong>Strength:</strong> ${data.strength}</div>
    `;

    if (data.brands.length > 0) {
        html += `<strong>Brands:</strong> ${data.brands.join(", ")}`;
    }

    html += `</div>`;

    result.innerHTML = html;
});
