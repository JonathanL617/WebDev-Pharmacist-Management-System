<?php
require_once '../config/config.php';
require_once '../model/Doctor.php';

// Database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'msg' => 'Database connection failed']));
}

$model = new DoctorModel($conn);

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_GET['action'] ?? '';

header('Content-Type: application/json');

if ($action === 'getPatients') {
    $search = $_GET['search'] ?? '';
    $patients = $model->getAllPatients($search);
    echo json_encode(['success' => true, 'patients' => $patients]);
    exit;
}

if ($action === 'getPatientHistory') {
    $id = $_GET['id'] ?? '';
    $history = $model->getPatientHistory($id);
    echo json_encode(['success' => true, 'history' => $history]);
    exit;
}

if ($action === 'getPrescriptions') {
    $search = $_GET['search'] ?? '';
    $prescriptions = $model->getAllPrescriptions($search);
    echo json_encode(['success' => true, 'prescriptions' => $prescriptions]);
    exit;
}

if ($action === 'getMedicines') {
    $medicines = $model->getAllMedicines();
    echo json_encode(['success' => true, 'medicines' => $medicines]);
    exit;
}

if ($action === 'createPrescription') {
    $patientId = $input['patient_id'] ?? '';
    $staffId = $input['staff_id'] ?? 'DOC001'; // Default or from session
    $medicines = $input['medicines'] ?? []; // Array of {id, qty}

    if (empty($patientId) || empty($medicines)) {
        echo json_encode(['success' => false, 'msg' => 'Missing required fields']);
        exit;
    }

    $res = $model->createPrescription($patientId, $staffId, $medicines);
    echo json_encode($res);
    exit;
}

echo json_encode(['success' => false, 'msg' => 'Invalid action']);
?>
