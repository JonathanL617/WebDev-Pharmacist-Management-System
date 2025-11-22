<?php
require_once 'config.php';

// Generate next medicine ID
function getNextMedicineID($conn) {
    $result = $conn->query("SELECT medicine_id FROM medicine_info ORDER BY medicine_id DESC LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $lastID = $result->fetch_assoc()['medicine_id'];
        $num = (int)substr($lastID, 1) + 1;
        return 'M' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }
    return 'M001';
}

// CRUD operations
function addMedicine($conn, $name, $price, $quantity, $desc){
    $id = getNextMedicineID($conn);
    $stmt = $conn->prepare("INSERT INTO medicine_info (medicine_id, medicine_name, medicine_price, medicine_quantity, medicine_description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $id, $name, $price, $quantity, $desc);
    $stmt->execute();
    $stmt->close();
}

function updateMedicine($conn, $id, $name, $price, $quantity, $desc){
    $stmt = $conn->prepare("UPDATE medicine_info SET medicine_name=?, medicine_price=?, medicine_quantity=?, medicine_description=? WHERE medicine_id=?");
    $stmt->bind_param("sdiss", $name, $price, $quantity, $desc, $id);
    $stmt->execute();
    $stmt->close();
}

function deleteMedicine($conn, $id){
    $stmt = $conn->prepare("DELETE FROM medicine_info WHERE medicine_id=?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->close();
}

function searchMedicines($conn, $search){
    $stmt = $conn->prepare("SELECT * FROM medicine_info WHERE medicine_id LIKE ? OR medicine_name LIKE ? ORDER BY medicine_id ASC");
    $likeSearch = "%".$search."%";
    $stmt->bind_param("ss", $likeSearch, $likeSearch);
    $stmt->execute();
    return $stmt->get_result();
}

function getAllMedicines($conn){
    return $conn->query("SELECT * FROM medicine_info ORDER BY medicine_id ASC");
}
?>



<?php
require 'config.php';

class PharmacyModel {
    private $conn;
    public function __construct($conn){ $this->conn = $conn; }

    public function getNextApprovalID() {
        $res = $this->conn->query("SELECT approval_id FROM order_approval ORDER BY approval_id DESC LIMIT 1");
        if ($res && $res->num_rows > 0) {
            $last = $res->fetch_assoc()['approval_id'];
            $num = (int)substr($last,2)+1;
            return 'AP'.str_pad($num,3,'0',STR_PAD_LEFT);
        }
        return 'AP001';
    }

    public function getOrder($order_id){
        $stmt=$this->conn->prepare("SELECT o.order_id,o.order_date,o.status_id,p.patient_name,p.patient_phone,p.patient_address,COALESCE(s.staff_name,o.staff_id) AS staff_name FROM `order` o LEFT JOIN patient p ON o.patient_id=p.patient_id LEFT JOIN staff s ON o.staff_id=s.staff_id WHERE o.order_id=?");
        $stmt->bind_param("s",$order_id);
        $stmt->execute();
        $order=$stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $order;
    }

    public function getOrderDetails($order_id){
        $stmt=$this->conn->prepare("SELECT od.medicine_id,od.medicine_quantity AS ordered_qty,od.medicine_price,m.medicine_name,m.medicine_quantity AS stock_qty FROM order_details od JOIN medicine_info m ON od.medicine_id=m.medicine_id WHERE od.order_id=?");
        $stmt->bind_param("s",$order_id);
        $stmt->execute();
        $details=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $details;
    }

    public function updateOrderStatus($order_id,$status,$approver_id='pharm001',$comment=''){
        $this->conn->begin_transaction();
        try{
            $medicines=$this->getOrderDetails($order_id);

            if($status==='Approved'){
                foreach($medicines as $med){
                    if($med['ordered_qty']>$med['stock_qty']) throw new Exception("Not enough stock for {$med['medicine_name']}");
                    $stmt=$this->conn->prepare("UPDATE medicine_info SET medicine_quantity=medicine_quantity-? WHERE medicine_id=?");
                    $stmt->bind_param("is",$med['ordered_qty'],$med['medicine_id']);
                    $stmt->execute(); $stmt->close();
                }
                $approval_id=$this->getNextApprovalID();
                $approval_date=date("Y-m-d H:i:s");
                $stmt=$this->conn->prepare("INSERT INTO order_approval(approval_id,order_id,approver_id,approval_date,approval_status,approval_comment) VALUES(?,?,?,?,?,?)");
                $stmt->bind_param("ssssss",$approval_id,$order_id,$approver_id,$approval_date,$status,$comment);
                $stmt->execute(); $stmt->close();
            }

            $stmt2=$this->conn->prepare("UPDATE `order` SET status_id=? WHERE order_id=?");
            $stmt2->bind_param("ss",$status,$order_id);
            $stmt2->execute(); $stmt2->close();

            $this->conn->commit();
            return ['success'=>true,'msg'=>"Order $order_id $status successfully"];
        }catch(Exception $e){
            $this->conn->rollback();
            return ['success'=>false,'msg'=>$e->getMessage()];
        }
    }

    public function getAllOrders($search='',$filter=''){
        if($filter!==''){
            $stmt=$this->conn->prepare("SELECT o.order_id,o.order_date,p.patient_name,s.staff_name,o.status_id FROM `order` o LEFT JOIN patient p ON o.patient_id=p.patient_id LEFT JOIN staff s ON o.staff_id=s.staff_id WHERE o.status_id=? ORDER BY o.order_date DESC");
            $stmt->bind_param("s",$filter);
            $stmt->execute();
            $result=$stmt->get_result();
        }else if($search!==''){
            $like="%$search%";
            $stmt=$this->conn->prepare("SELECT o.order_id,o.order_date,p.patient_name,s.staff_name,o.status_id FROM `order` o LEFT JOIN patient p ON o.patient_id=p.patient_id LEFT JOIN staff s ON o.staff_id=s.staff_id WHERE o.order_id LIKE ? OR p.patient_name LIKE ? OR s.staff_name LIKE ? OR o.status_id LIKE ? ORDER BY o.order_date DESC");
            $stmt->bind_param("ssss",$like,$like,$like,$like);
            $stmt->execute();
            $result=$stmt->get_result();
        }else{
            $result=$this->conn->query("SELECT o.order_id,o.order_date,p.patient_name,s.staff_name,o.status_id FROM `order` o LEFT JOIN patient p ON o.patient_id=p.patient_id LEFT JOIN staff s ON o.staff_id=s.staff_id ORDER BY o.order_date DESC");
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
