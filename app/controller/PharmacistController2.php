<?php
// File: app/controller/PharmacistController2.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/conn.php';
require_once __DIR__ . '/../model/Pharmacist2.php';

header('Content-Type: application/json');

// SUPER SMART LOGIN CHECK — works with old & new login systems
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'msg' => 'Please log in again.']);
    exit;
}

// Determine the correct approver_id (P001 style)
if (!empty($_SESSION['staff_id'])) {
    $approver_id = $_SESSION['staff_id'];           // New way → P001
} else {
    $approver_id = $_SESSION['user_id'];            // Old way → fallback
    // Optional: auto-upgrade session (remove later)
    $_SESSION['staff_id'] = $approver_id;
}

$input  = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $input['action'] ?? $_GET['action'] ?? '';

$model = new PharmacyModel($conn);

switch ($action) {

    case 'get_details':
        $order_id = trim($input['order_id'] ?? '');
        if (empty($order_id)) {
            echo json_encode(['success' => false, 'msg' => 'Order ID is required']);
            exit;
        }

        $data = $model->getOrderDetails($order_id);

        if ($data['order']) {
            echo json_encode([
                'success' => true,
                'order'   => $data['order'],
                'details' => $data['details'] ?? []
            ]);
        } else {
            echo json_encode(['success' => false, 'msg' => 'Order not found']);
        }
        break;


    case 'approve':
    case 'reject':
    case 'done':
        $order_id = trim($input['order_id'] ?? '');
        $comment  = $input['comment'] ?? '';

        if (empty($order_id)) {
            echo json_encode(['success' => false, 'msg' => 'Invalid order ID']);
            exit;
        }

        // CORRECT: Now using $approver_id = P001, P002 → NO MORE FOREIGN KEY ERROR!
        $result = $model->updateOrder($order_id, $action, $approver_id, $comment);
        echo json_encode($result);
        break;


    case 'list_orders':
        $filter = $input['filter'] ?? $_GET['filter'] ?? '';
        $search = $input['search'] ?? '';

        $orders = $model->getAllOrders($search, $filter);

        echo json_encode([
            'success' => true,
            'orders'  => $orders
        ]);
        break;


    default:
        echo json_encode(['success' => false, 'msg' => 'Unknown action']);
        break;
}

exit;