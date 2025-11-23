<?php
require_once __DIR__ . "/../config/conn.php";

class AdminController {
    private $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

    public function getStaff() {
        $sql = "SELECT * FROM staff ORDER BY staff_id";
        $result = $this->conn->query($sql);
        echo json_encode($result->fetch_all(MYSQLI_ASSOC));
    }

    public function getPatients() {
        $sql = "SELECT * FROM patient ORDER BY patient_id";
        $result = $this->conn->query($sql);
        echo json_encode($result->fetch_all(MYSQLI_ASSOC));
    }

    public function getStaffById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM staff WHERE staff_id = ?");
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        echo json_encode($result);
    }

    public function getPatientById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM patient WHERE patient_id = ?");
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        echo json_encode($result);
    }

    public function updateStaff() {
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->conn->prepare("
            UPDATE staff SET 
                staff_name = ?, 
                staff_dob = ?, 
                staff_specialization = ?, 
                staff_role = ?, 
                staff_phone = ?, 
                staff_email = ?, 
                staff_status = ?
            WHERE staff_id = ?
        ");
        $stmt->bind_param('ssssssss', 
            $data['staff_name'],
            $data['staff_dob'],
            $data['staff_specialization'],
            $data['staff_role'],
            $data['staff_phone'],
            $data['staff_email'],
            $data['staff_status'],
            $data['staff_id']
        );
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
    }

    public function generatePatientId() {
        $result = $this->conn->query("SELECT patient_id FROM patient ORDER BY patient_id DESC LIMIT 1");
        if ($result && $result->num_rows > 0) {
            $lastID = $result->fetch_assoc()['patient_id'];
            $num = (int)substr($lastID, 1) + 1;
            $newID = 'P' . str_pad($num, 3, '0', STR_PAD_LEFT);
        } else {
            $newID = 'P001';
        }
        echo json_encode(['id' => $newID]);
    }

    public function registerPatient() {
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->conn->prepare("INSERT INTO patient (patient_id, patient_name, patient_date_of_birth, patient_phone, patient_gender, patient_email, patient_address, registered_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssssss', 
            $data['patient_id'],
            $data['patient_name'],
            $data['patient_date_of_birth'],
            $data['patient_phone'],
            $data['patient_gender'],
            $data['patient_email'],
            $data['patient_address'],
            $data['registered_by']
        );
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
    }

    public function updatePatient() {
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->conn->prepare("
            UPDATE patient SET 
                patient_name = ?, 
                patient_date_of_birth = ?, 
                patient_phone = ?, 
                patient_gender = ?, 
                patient_email = ?, 
                patient_address = ?
            WHERE patient_id = ?
        ");
        $stmt->bind_param('sssssss', 
            $data['patient_name'],
            $data['patient_date_of_birth'],
            $data['patient_phone'],
            $data['patient_gender'],
            $data['patient_email'],
            $data['patient_address'],
            $data['patient_id']
        );
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
    }
}

// Router
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';

$controller = new AdminController($conn);

match ($action) {
    'getStaff' => $controller->getStaff(),
    'getPatients' => $controller->getPatients(),
    'getStaffById' => $controller->getStaffById($id),
    'getPatientById' => $controller->getPatientById($id),
    'updateStaff' => $controller->updateStaff(),
    'updatePatient' => $controller->updatePatient(),
    'generatePatientId' => $controller->generatePatientId(),
    'registerPatient' => $controller->registerPatient(),
    default => (function() { echo json_encode(['error' => 'Invalid action']); })()
};
?>
