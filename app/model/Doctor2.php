<?php
class Doctor2
{
    private $conn;   // ← THIS LINE MUST BE THERE

    public function __construct()
    {
        // ← THIS LINE WAS MISSING IN YOUR FILE!
        $this->conn = new mysqli("localhost", "root", "", "latest");

        if ($this->conn->connect_error) {
            die(json_encode(["status" => "error", "message" => "Database connection failed"]));
        }
        $this->conn->set_charset("utf8mb4");
    }

    public function getAllPatients()
    {
        $sql = "SELECT patient_id, patient_name, patient_date_of_birth, patient_phone,
                       patient_gender, patient_email, patient_address
                FROM patient ORDER BY patient_name ASC";
        $result = $this->conn->query($sql);  // ← This was failing because $this->conn was null
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getPatientOrders($patient_id)
    {
        $orders = [];
        $stmt = $this->conn->prepare("SELECT order_id, order_date FROM `order` WHERE patient_id = ? ORDER BY order_date DESC");
        $stmt->bind_param("s", $patient_id);
        $stmt->execute();
        $res = $stmt->get_result();

        while ($order = $res->fetch_assoc()) {
            $order_id = $order['order_id'];

            $stmt2 = $this->conn->prepare("SELECT d.medicine_id, m.medicine_name, d.medicine_quantity, d.medicine_price
                                           FROM order_details d
                                           LEFT JOIN medicine_info m ON d.medicine_id = m.medicine_id
                                           WHERE d.order_id = ?");
            $stmt2->bind_param("i", $order_id);
            $stmt2->execute();
            $medRes = $stmt2->get_result();

            $medicines = [];
            while ($med = $medRes->fetch_assoc()) {
                $medicines[] = $med;
            }
            $order['medicines'] = $medicines;
            $orders[] = $order;
            $stmt2->close();
        }
        $stmt->close();
        return $orders;
    }

    public function __destruct()
    {
        if ($this->conn) $this->conn->close();
    }
}