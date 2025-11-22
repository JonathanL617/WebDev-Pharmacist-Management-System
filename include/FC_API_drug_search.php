<?php
if (isset($_GET['name'])) {
    header("Content-Type: application/json");

    $name = trim($_GET['name']);
    if ($name === "") {
        echo json_encode(["error" => "Please enter a drug name."]);
        exit;
    }

    $encoded = urlencode($name);

    // Step 1: Get RxCUI and basic info
    $rxcuiUrl = "https://rxnav.nlm.nih.gov/REST/drugs.json?name=$encoded";
    $rxcuiResponse = file_get_contents($rxcuiUrl);

    if ($rxcuiResponse === FALSE) {
        echo json_encode(["error" => "Unable to connect to drug database."]);
        exit;
    }

    $rxcuiData = json_decode($rxcuiResponse, true);

    // Check if drug found
    if (empty($rxcuiData['drugGroup']['conceptGroup'])) {
        echo json_encode(["error" => "Drug not found. Try a different name."]);
        exit;
    }

    // Get the first valid concept group with drug info
    $drugFound = false;
    $drugInfo = null;
    $termType = '';

    foreach ($rxcuiData['drugGroup']['conceptGroup'] as $group) {
        if (isset($group['conceptProperties'][0])) {
            $drugInfo = $group['conceptProperties'][0];
            $termType = $group['tty'] ?? 'Unknown';
            $drugFound = true;
            break;
        }
    }

    if (!$drugFound) {
        echo json_encode(["error" => "Drug not found."]);
        exit;
    }

    $rxcui = $drugInfo['rxcui'];
    $drugName = $drugInfo['name'];

    // Clean product name
    $cleanProductName = $drugName;
    $cleanProductName = preg_replace('/\s*\[[^\]]*\]/', '', $cleanProductName);
    $cleanProductName = preg_replace('/\s*\{[^\}]*\}/', '', $cleanProductName);
    $cleanProductName = trim($cleanProductName);

    if ($cleanProductName === '') {
        $cleanProductName = $drugName;
    }

    // Get drug properties
    $doseForm = 'Not specified';
    $strength = 'Not specified';

    $propertiesUrl = "https://rxnav.nlm.nih.gov/REST/rxcui/$rxcui/properties.json";
    $propertiesResponse = @file_get_contents($propertiesUrl);

    if ($propertiesResponse !== FALSE) {
        $propertiesData = json_decode($propertiesResponse, true);
        if (!empty($propertiesData['properties'])) {
            $props = $propertiesData['properties'];
            $doseForm = $props['dosageForm'] ?? 'Not specified';
            $strength = $props['strength'] ?? 'Not specified';
        }
    }

    // Get ingredients and components
    $ingredients = [];
    $packComponents = [];

    $componentsUrl = "https://rxnav.nlm.nih.gov/REST/rxcui/$rxcui/allrelated.json";
    $componentsResponse = @file_get_contents($componentsUrl);

    if ($componentsResponse !== FALSE) {
        $componentsData = json_decode($componentsResponse, true);
        if (!empty($componentsData['allRelatedGroup']['conceptGroup'])) {
            foreach ($componentsData['allRelatedGroup']['conceptGroup'] as $group) {
                $tty = $group['tty'] ?? '';

                // Get ingredients for single drugs
                if ($tty === 'IN' && !empty($group['conceptProperties'])) {
                    foreach ($group['conceptProperties'] as $ingredient) {
                        $ingredients[] = $ingredient['name'];
                    }
                }

                // Get components for packs
                if ($tty === 'SCD' && !empty($group['conceptProperties'])) {
                    foreach ($group['conceptProperties'] as $component) {
                        $componentName = $component['name'];
                        $componentRxcui = $component['rxcui'];

                        // Extract quantity
                        $quantity = '';
                        if (preg_match('/^(\d+)\s/', $componentName, $matches)) {
                            $quantity = $matches[1];
                            $componentName = preg_replace('/^\d+\s+/', '', $componentName);
                        }

                        // Clean component name
                        $cleanComponentName = $componentName;
                        $cleanComponentName = preg_replace('/\s*\[[^\]]*\]/', '', $cleanComponentName);
                        $cleanComponentName = preg_replace('/\s*\{[^\}]*\}/', '', $cleanComponentName);
                        $cleanComponentName = trim($cleanComponentName);

                        if ($cleanComponentName === '') {
                            $cleanComponentName = $componentName;
                        }

                        // Get ingredients for this component
                        $componentIngredients = [];
                        $compIngredientsUrl = "https://rxnav.nlm.nih.gov/REST/rxcui/$componentRxcui/allrelated.json";
                        $compIngredientsResponse = @file_get_contents($compIngredientsUrl);

                        if ($compIngredientsResponse !== FALSE) {
                            $compIngredientsData = json_decode($compIngredientsResponse, true);
                            if (!empty($compIngredientsData['allRelatedGroup']['conceptGroup'])) {
                                foreach ($compIngredientsData['allRelatedGroup']['conceptGroup'] as $ingGroup) {
                                    if (($ingGroup['tty'] ?? '') === 'IN' && !empty($ingGroup['conceptProperties'])) {
                                        foreach ($ingGroup['conceptProperties'] as $ingredient) {
                                            $componentIngredients[] = $ingredient['name'];
                                        }
                                    }
                                }
                            }
                        }

                        $packComponents[] = [
                            'name' => $cleanComponentName,
                            'quantity' => $quantity,
                            'ingredients' => $componentIngredients
                        ];
                    }
                }
            }
        }
    }

    // Get brand names
    $brands = [];
    $brandUrl = "https://rxnav.nlm.nih.gov/REST/rxcui/$rxcui/related.json?tty=BN";
    $brandResponse = @file_get_contents($brandUrl);

    if ($brandResponse !== FALSE) {
        $brandData = json_decode($brandResponse, true);
        if (!empty($brandData['relatedGroup']['conceptGroup'])) {
            foreach ($brandData['relatedGroup']['conceptGroup'] as $group) {
                if (!empty($group['conceptProperties'])) {
                    foreach ($group['conceptProperties'] as $brand) {
                        $brands[] = $brand['name'];
                    }
                }
            }
        }
    }

    // Remove duplicates
    $brands = array_values(array_unique(array_filter($brands)));
    $ingredients = array_values(array_unique(array_filter($ingredients)));

    echo json_encode([
        "name" => $cleanProductName,
        "rxcui" => $rxcui,
        "termType" => $termType,
        "doseForm" => $doseForm,
        "strength" => $strength,
        "ingredients" => $ingredients,
        "packComponents" => $packComponents,
        "brands" => $brands,
        "isPack" => in_array($termType, ['BPCK', 'GPCK'])
    ]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drug Search</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 800px;
        }
        .search-box {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .result-box {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 15px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="text-center mb-4">
            <h1>Drug Search</h1>
            <p class="text-muted">Search for drug information</p>
        </div>

        <div class="search-box">
            <form id="drugForm">
                <div class="row">
                    <div class="col-md-8">
                        <label for="drugName" class="form-label">Drug Name</label>
                        <input type="text" id="drugName" class="form-control" placeholder="Enter drug name" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                </div>
            </form>
        </div>

        <div id="result"></div>
    </div>

    <script>
        document.getElementById("drugForm").addEventListener("submit", async (e) => {
            e.preventDefault();
            const name = document.getElementById("drugName").value.trim();
            const result = document.getElementById("result");

            if (!name) {
                result.innerHTML = '<div class="alert alert-warning">Please enter a drug name</div>';
                return;
            }

            result.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary mb-3"></div>
                    <p>Searching...</p>
                </div>`;

            try {
                const res = await fetch(`?name=${encodeURIComponent(name)}`);
                const data = await res.json();

                if (data.error) {
                    result.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                    return;
                }

                let html = `
                    <div class="result-box">
                        <h4>${data.name}</h4>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>RxNorm ID:</strong> ${data.rxcui}
                            </div>
                            <div class="col-md-6">
                                <strong>Type:</strong> ${data.isPack ? 'Drug Pack' : 'Single Drug'}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Dose Form:</strong> ${data.doseForm}
                            </div>
                            <div class="col-md-6">
                                <strong>Strength:</strong> ${data.strength}
                            </div>
                        </div>`;

                if (data.brands.length > 0) {
                    html += `<div class="mb-3"><strong>Brand Names:</strong> ${data.brands.join(", ")}</div>`;
                }

                html += `</div>`;

                // Single Drug Ingredients
                if (!data.isPack && data.ingredients.length > 0) {
                    html += `
                        <div class="result-box">
                            <h5>Active Ingredients</h5>
                            <div>${data.ingredients.join(", ")}</div>
                        </div>`;
                }

                // Pack Contents
                if (data.isPack && data.packComponents.length > 0) {
                    html += `<div class="result-box"><h5>Pack Contents</h5>`;

                    data.packComponents.forEach(component => {
                        html += `<div class="mb-3 p-3 border rounded">`;
                        if (component.quantity) {
                            html += `<strong>${component.quantity} of ${component.name}</strong>`;
                        } else {
                            html += `<strong>${component.name}</strong>`;
                        }
                        if (component.ingredients.length > 0) {
                            html += `<div class="mt-2">Ingredients: ${component.ingredients.join(", ")}</div>`;
                        }
                        html += `</div>`;
                    });

                    html += `</div>`;
                }

                result.innerHTML = html;

            } catch (err) {
                result.innerHTML = '<div class="alert alert-danger">Network error. Please try again.</div>';
            }
        });
    </script>
</body>
</html>