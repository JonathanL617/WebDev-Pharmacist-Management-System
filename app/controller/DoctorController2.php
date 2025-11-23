<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/conn.php';
require_once __DIR__ . '/../model/Doctor2.php';

header("Content-Type: application/json; charset=utf-8");

// Only respond to AJAX requests
if (!isset($_GET['action'])) {
    echo json_encode(["status" => "error", "message" => "No action specified"]);
    exit;
}

$model = new Doctor2();

$action = $_GET['action'];

if ($action === "patients") {
    $patients = $model->getAllPatients();
    echo json_encode(["status" => "success", "data" => $patients]);
    exit;
}

if ($action === "orders") {
    if (!isset($_GET['patient_id']) || empty($_GET['patient_id'])) {
        echo json_encode(["status" => "error", "message" => "Missing patient ID"]);
        exit;
    }
    $orders = $model->getPatientOrders($_GET['patient_id']);
    echo json_encode(["status" => "success", "orders" => $orders]);
    exit;
}

echo json_encode(["status" => "error", "message" => "Invalid action"]);
exit;

