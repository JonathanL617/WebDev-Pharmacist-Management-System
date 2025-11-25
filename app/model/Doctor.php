<?php
require_once '../config/conn.php';

class DoctorModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllPatients($search = '') {
        $sql = "SELECT * FROM patient";
        if ($search) {
            $sql .= " WHERE patient_name LIKE ? OR patient_id LIKE ?";
        }
        $sql .= " ORDER BY patient_name ASC";

        $stmt = $this->conn->prepare($sql);
        if ($search) {
            $term = "%$search%";
            $stmt->bind_param("ss", $term, $term);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getPatientHistory($patientId) {
        $stmt = $this->conn->prepare("
            SELECT o.order_id, o.order_date, o.status_id, 
                   GROUP_CONCAT(m.medicine_name SEPARATOR ', ') as medicines
            FROM `order` o
            JOIN order_details od ON o.order_id = od.order_id
            JOIN medicine_info m ON od.medicine_id = m.medicine_id
            WHERE o.patient_id = ?
            GROUP BY o.order_id
            ORDER BY o.order_date DESC
        ");
        $stmt->bind_param("s", $patientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllPrescriptions($search = '') {
    $sql = "SELECT o.order_id, o.order_date, p.patient_name, o.status_id, s.staff_name
            FROM `order` o 
            JOIN patient p ON o.patient_id = p.patient_id
            JOIN staff s ON o.staff_id = s.staff_id";
    
    if ($search) {
        $sql .= " WHERE p.patient_name LIKE ? OR o.order_id LIKE ?";
    }
    $sql .= " ORDER BY o.order_date DESC";

    $stmt = $this->conn->prepare($sql);
    if ($search) {
        $term = "%$search%";
        $stmt->bind_param("ss", $term, $term);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function createPrescription($patientId, $staffId, $medicines) {
    // $medicines is array of {id, qty}
    
    $this->conn->begin_transaction();
    try {
        // Generate Order ID (OR001 format)
        $res = $this->conn->query("SELECT order_id FROM `order` ORDER BY order_id DESC LIMIT 1");
        
        if (!$res) {
            throw new Exception("Query failed: " . $this->conn->error);
        }
        
        if ($res->num_rows > 0) {
            $lastId = $res->fetch_assoc()['order_id'];
            
            // Extract number from OR001 format
            $num = (int)substr($lastId, 2) + 1; // Start from position 2 (after "OR")
            $orderId = 'O' . str_pad($num, 3, '0', STR_PAD_LEFT);
        } else {
            $orderId = 'O001'; // First order
        }
        
        $date = date('Y-m-d H:i:s');
        $status = 'Pending'; // Default status

        // Insert Order
        $stmt = $this->conn->prepare("INSERT INTO `order` (order_id, patient_id, staff_id, order_date, status_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $orderId, $patientId, $staffId, $date, $status);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert order: " . $stmt->error);
        }

        // Generate Order Detail IDs (OD001 format) and Insert Details
        $resDetail = $this->conn->query("SELECT order_detail_id FROM order_details ORDER BY order_detail_id DESC LIMIT 1");
        
        $lastDetailId = 'OD000'; // Default if no order details exist
        if ($resDetail && $resDetail->num_rows > 0) {
            $lastDetailId = $resDetail->fetch_assoc()['order_detail_id'];
        }
        
        // Extract number from OD001 format
        $detailNum = (int)substr($lastDetailId, 2) + 1; // Start from position 2 (after "OD")
        
        $stmtDetail = $this->conn->prepare("INSERT INTO order_details (order_detail_id, order_id, medicine_id, medicine_quantity, medicine_price) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($medicines as $med) {
            // Generate unique order_detail_id for each medicine
            $orderDetailId = 'OD' . str_pad($detailNum, 3, '0', STR_PAD_LEFT);
            $detailNum++; // Increment for next medicine
            
            // Get price
            $priceStmt = $this->conn->prepare("SELECT medicine_price FROM medicine_info WHERE medicine_id = ?");
            $priceStmt->bind_param("s", $med['id']);
            $priceStmt->execute();
            $priceResult = $priceStmt->get_result();
            
            if ($priceResult->num_rows > 0) {
                $price = $priceResult->fetch_assoc()['medicine_price'];
            } else {
                $price = 0; // Default price if medicine not found
            }
            
            $stmtDetail->bind_param("sssid", $orderDetailId, $orderId, $med['id'], $med['qty'], $price);
            if (!$stmtDetail->execute()) {
                throw new Exception("Failed to insert order details: " . $stmtDetail->error);
            }
        }

        $this->conn->commit();
        return ['success' => true, 'msg' => 'Prescription created successfully', 'order_id' => $orderId];

    } catch (Exception $e) {
        $this->conn->rollback();
        return ['success' => false, 'msg' => $e->getMessage()];
    }
}
    public function getAllMedicines() {
        $result = $this->conn->query("SELECT * FROM medicine_info ORDER BY medicine_name ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getPrescriptionsByStaff($staffId, $search = '') {
    $sql = "SELECT o.order_id, o.order_date, p.patient_name, o.status_id, 
                   s.staff_name
            FROM `order` o 
            JOIN patient p ON o.patient_id = p.patient_id
            JOIN staff s ON o.staff_id = s.staff_id
            WHERE o.staff_id = ?";
    
    $params = [$staffId];
    $types = "s";
    
    if ($search) {
        $sql .= " AND (p.patient_name LIKE ? OR o.order_id LIKE ? OR o.status_id LIKE ?)";
        $term = "%$search%";
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
        $types .= "sss";
    }
    
    $sql .= " ORDER BY o.order_date DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

    public function getDashboardStats() {
    $stats = [];
    
    // Total Patients (ALL patients)
    $sql = "SELECT COUNT(*) as total FROM patient";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $stats['total_patients'] = $stmt->get_result()->fetch_assoc()['total'];
    
    // Recent Consultations (last 7 days - ALL doctors)
    $sql = "SELECT COUNT(*) as total FROM `order` WHERE order_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $stats['recent_consultations'] = $stmt->get_result()->fetch_assoc()['total'];
    
    // Pending Prescriptions (ALL doctors)
    $sql = "SELECT COUNT(*) as total FROM `order` WHERE status_id = 'Pending'";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $stats['pending_prescriptions'] = $stmt->get_result()->fetch_assoc()['total'];
    
    // Today's Appointments (ALL doctors)
    $sql = "SELECT COUNT(*) as total FROM `order` WHERE DATE(order_date) = CURDATE()";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $stats['todays_appointments'] = $stmt->get_result()->fetch_assoc()['total'];
    
    return $stats;
    }

    public function getDoctorStats($staffId) {
    $stats = [];
    
    // Total Prescriptions for this doctor
    $sql = "SELECT COUNT(*) as total FROM `order` WHERE staff_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("s", $staffId);
    $stmt->execute();
    $stats['total_prescriptions'] = $stmt->get_result()->fetch_assoc()['total'];
    
    // Pending Prescriptions for this doctor
    $sql = "SELECT COUNT(*) as total FROM `order` WHERE staff_id = ? AND status_id = 'Pending'";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("s", $staffId);
    $stmt->execute();
    $stats['pending_prescriptions'] = $stmt->get_result()->fetch_assoc()['total'];
    
    // Approved Prescriptions for this doctor
    $sql = "SELECT COUNT(*) as total FROM `order` WHERE staff_id = ? AND status_id = 'Approved'";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("s", $staffId);
    $stmt->execute();
    $stats['approved_prescriptions'] = $stmt->get_result()->fetch_assoc()['total'];
    
        // Rejected Prescriptions for this doctor
    $sql = "SELECT COUNT(*) as total FROM `order` WHERE staff_id = ? AND status_id = 'Rejected'";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("s", $staffId);
    $stmt->execute();
    $stats['rejected_prescriptions'] = $stmt->get_result()->fetch_assoc()['total'];
    
    return $stats;
    }

    public function getPrescriptionDetails($orderId) {
    try {
        // Get basic prescription info
        $sql = "SELECT o.*, p.patient_name, s.staff_name 
                FROM `order` o 
                JOIN patient p ON o.patient_id = p.patient_id 
                JOIN staff s ON o.staff_id = s.staff_id 
                WHERE o.order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $orderId);
        $stmt->execute();
        $prescription = $stmt->get_result()->fetch_assoc();
        
        if (!$prescription) {
            return ['success' => false, 'msg' => 'Prescription not found'];
        }
        
        // Get medicine details
        $sql = "SELECT od.*, m.medicine_name 
                FROM order_details od 
                JOIN medicine_info m ON od.medicine_id = m.medicine_id 
                WHERE od.order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $orderId);
        $stmt->execute();
        $medicines = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        return [
            'success' => true,
            'prescription' => $prescription,
            'medicines' => $medicines
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'msg' => $e->getMessage()];
    }
    }
}
?>
