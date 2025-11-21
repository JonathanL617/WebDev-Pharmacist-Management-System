<?php
header("Content-Type: application/json");
require_once "API.php";

if (!isset($_GET['name'])) {
    echo json_encode(["error" => "No drug name provided."]);
    exit;
}

$name = trim($_GET['name']);
if ($name === "") {
    echo json_encode(["error" => "Please enter a drug name."]);
    exit;
}

$model = new DrugModel();
$response = $model->searchDrug($name);

echo json_encode($response);
?>
