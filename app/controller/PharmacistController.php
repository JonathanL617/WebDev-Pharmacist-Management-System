<?php
require_once 'Pharmacist.php';

// Handle Add
if (isset($_POST['add'])) {
    addMedicine($conn, $_POST['medicine_name'], floatval($_POST['medicine_price']), intval($_POST['medicine_quantity']), $_POST['medicine_description']);
    header("Location: index.php");
    exit;
}

// Handle Update
if (isset($_POST['update'])) {
    updateMedicine($conn, $_POST['medicine_id'], $_POST['medicine_name'], floatval($_POST['medicine_price']), intval($_POST['medicine_quantity']), $_POST['medicine_description']);
    header("Location: index.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    deleteMedicine($conn, $_GET['delete']);
    header("Location: index.php");
    exit;
}

// Handle Search
$search = isset($_GET['search']) ? $_GET['search'] : '';
$medicines = $search ? searchMedicines($conn, $search) : getAllMedicines($conn);
?>
