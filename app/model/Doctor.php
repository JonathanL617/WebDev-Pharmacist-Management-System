<?php
require_once '../config/config.php';

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
        // Assuming 'prescriptions' are just orders for now, or if there's a separate table.
        // Based on previous files, it seems orders are used.
        // Let's assume we want to see orders created by doctors or for patients.
        
        $sql = "SELECT o.order_id, o.order_date, p.patient_name, o.status_id 
                FROM `order` o 
                JOIN patient p ON o.patient_id = p.patient_id";
        
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
            // Generate Order ID
            $res = $this->conn->query("SELECT order_id FROM `order` ORDER BY order_id DESC LIMIT 1");
            $lastId = $res->num_rows > 0 ? $res->fetch_assoc()['order_id'] : 'O000';
            $num = (int)substr($lastId, 1) + 1;
            $orderId = 'O' . str_pad($num, 3, '0', STR_PAD_LEFT);
            
            $date = date('Y-m-d H:i:s');
            $status = 'Pending'; // Default status

            // Insert Order
            $stmt = $this->conn->prepare("INSERT INTO `order` (order_id, patient_id, staff_id, order_date, status_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $orderId, $patientId, $staffId, $date, $status);
            $stmt->execute();

            // Insert Details
            $stmtDetail = $this->conn->prepare("INSERT INTO order_details (order_id, medicine_id, medicine_quantity, medicine_price) VALUES (?, ?, ?, ?)");
            
            foreach ($medicines as $med) {
                // Get price
                $pRes = $this->conn->query("SELECT medicine_price FROM medicine_info WHERE medicine_id = '{$med['id']}'");
                $price = $pRes->fetch_assoc()['medicine_price'];
                
                $stmtDetail->bind_param("ssid", $orderId, $med['id'], $med['qty'], $price);
                $stmtDetail->execute();
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
}
?>
