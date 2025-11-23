<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/conn.php';
require_once __DIR__ . '/../model/Pharmacist.php';

// Handle Add
if (isset($_POST['add'])) {
    addMedicine($conn, $_POST['medicine_name'], floatval($_POST['medicine_price']), intval($_POST['medicine_quantity']), $_POST['medicine_description']);
    header("Location: /WebDev-Pharmacist-Management-System/app/view/pages/pharmacist/stock_management.php");
    exit;
}

// Handle Update
if (isset($_POST['update'])) {
    updateMedicine($conn, $_POST['medicine_id'], $_POST['medicine_name'], floatval($_POST['medicine_price']), intval($_POST['medicine_quantity']), $_POST['medicine_description']);
    header("Location: /WebDev-Pharmacist-Management-System/app/view/pages/pharmacist/stock_management.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    deleteMedicine($conn, $_GET['delete']);
    header("Location: /WebDev-Pharmacist-Management-System/app/view/pages/pharmacist/stock_management.php");
    exit;
}

// Handle Search
$search = isset($_GET['search']) ? $_GET['search'] : '';
$medicines = $search ? searchMedicines($conn, $search) : getAllMedicines($conn);


