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
    public function generateStaffId() {
        $role = $_GET['role'] ?? '';
        $prefix = ($role === 'doctor') ? 'D' : (($role === 'pharmacist') ? 'P' : 'S');
        
        $sql = "SELECT staff_id FROM staff WHERE staff_id LIKE ? ORDER BY staff_id DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $likeParam = $prefix . '%';
        $stmt->bind_param('s', $likeParam);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $lastID = $result->fetch_assoc()['staff_id'];
            $num = (int)substr($lastID, 1) + 1;
            $newID = $prefix . str_pad($num, 3, '0', STR_PAD_LEFT);
        } else {
            $newID = $prefix . '001';
        }
        echo json_encode(['id' => $newID]);
    }

    public function createStaffRequest() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Basic validation
        if (empty($data['staff_id']) || empty($data['staff_email'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            return;
        }

        // Check for duplicates
        $check = $this->conn->prepare("SELECT staff_id FROM staff WHERE staff_email = ?");
        $check->bind_param('s', $data['staff_email']);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Email already exists']);
            return;
        }

        // Insert with Pending status
        // Note: Password is not set here, it will be set by the user upon first login or by superadmin? 
        // For now, let's assume a default password or handle it later. 
        // Actually, the form doesn't have password. Let's set a default one or leave it empty if allowed.
        // Looking at previous code, password is required. Let's set a default 'password123' hashed.
        $defaultPass = password_hash('password123', PASSWORD_DEFAULT);
        $status = 'Pending';

        $stmt = $this->conn->prepare("INSERT INTO staff (staff_id, staff_name, staff_dob, staff_specialization, staff_role, staff_phone, staff_email, staff_status, staff_password, registered_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param('ssssssssss', 
            $data['staff_id'],
            $data['staff_name'],
            $data['staff_dob'],
            $data['staff_specialization'],
            $data['staff_role'],
            $data['staff_phone'],
            $data['staff_email'],
            $status,
            $defaultPass,
            $data['registered_by']
        );
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
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
    'generateStaffId' => $controller->generateStaffId(),
    'createStaffRequest' => $controller->createStaffRequest(),
    default => (function() { echo json_encode(['error' => 'Invalid action']); })()
};
?>
