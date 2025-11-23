<?php
session_start(); // required
header('Content-Type: application/json');

$loggedInUser = $_SESSION['user_id'] ?? 'P001'; // fallback
require_once '../model/Pharmacist.php';

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

$model = new PharmacyModel($conn);

if($action==='get_details'){
    $order_id = $input['order_id'] ?? '';
    $data = $model->getOrderDetails($order_id);
    if(!$data['order']) echo json_encode(['success'=>false,'msg'=>'Order not found']);
    else echo json_encode(['success'=>true,'order'=>$data['order'],'details'=>$data['details']]);
    exit;
}

if(in_array($action,['approve','reject','done'])){
    $order_id = $input['order_id'] ?? '';
    $approver_id = $loggedInUser;
    $comment = $input['comment'] ?? '';
    $res = $model->updateOrder($order_id,$action,$approver_id,$comment);
    echo json_encode($res);
    exit;
}

if($action==='list_orders'){
    $filter = $input['filter'] ?? '';
    $search = $input['search'] ?? '';
    $orders = $model->getAllOrders($search,$filter);
    echo json_encode(['success'=>true,'orders'=>$orders]);
    exit;
}

// Medicine Management
if($action==='list_medicines'){
    $search = $input['search'] ?? '';
    $medicines = $search ? $model->searchMedicines($search) : $model->getAllMedicines();
    echo json_encode(['success'=>true, 'medicines'=>$medicines]);
    exit;
}

if($action==='add_medicine'){
    $name = $input['name'] ?? '';
    $price = $input['price'] ?? 0;
    $qty = $input['quantity'] ?? 0;
    $desc = $input['description'] ?? '';
    $res = $model->addMedicine($name, $price, $qty, $desc);
    echo json_encode($res);
    exit;
}

if($action==='update_medicine'){
    $id = $input['id'] ?? '';
    $name = $input['name'] ?? '';
    $price = $input['price'] ?? 0;
    $qty = $input['quantity'] ?? 0;
    $desc = $input['description'] ?? '';
    $res = $model->updateMedicine($id, $name, $price, $qty, $desc);
    echo json_encode($res);
    exit;
}

if($action==='delete_medicine'){
    $id = $input['id'] ?? '';
    $res = $model->deleteMedicine($id);
    echo json_encode($res);
    exit;
}

echo json_encode(['success'=>false,'msg'=>'Unknown action']);
?>
