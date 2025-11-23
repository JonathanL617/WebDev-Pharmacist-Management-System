<?php
// controller/APIController.php
// THIS FILE MUST OUTPUT ONLY JSON — NOTHING ELSE!

// Prevent any output before headers
ob_clean();

// Only respond if ?name is provided
if (!isset($_GET['name']) || trim($_GET['name']) === '') {
    header("Content-Type: application/json");
    echo json_encode(["error" => "No drug name provided."]);
    exit;
}

require_once __DIR__ . '/../model/API.php';

header("Content-Type: application/json");

$name = trim($_GET['name']);

$model = new DrugModel();
$result = $model->searchDrug($name);

echo json_encode($result);
exit; // This stops everything