<?php
session_start();
$loggedInUser = $_SESSION['user_id'] ?? 'P001'; // fallback
?>
<!doctype html>
<html>
<head>
    <title>Pharmacy Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="prescription_queue.css">
    <script>
        const loggedInUserId = "<?= $loggedInUser ?>";
    </script>
</head>
<body>
<div class="container my-5">
<h3>Incoming Pharmacy Orders</h3>

<div class="mb-3">
    <button class="btn btn-secondary btn-sm" onclick="loadOrders()">All</button>
    <button class="btn btn-success btn-sm" onclick="loadOrders('Approved')">Approved</button>
    <button class="btn btn-danger btn-sm" onclick="loadOrders('Rejected')">Rejected</button>
    <button class="btn btn-warning btn-sm text-dark" onclick="loadOrders('Pending')">Pending</button>
    <button class="btn btn-info btn-sm text-dark" onclick="loadOrders('Done')">Done</button>
</div>

<table class="table table-bordered table-striped">
<thead>
<tr>
    <th>Order ID</th>
    <th>Date</th>
    <th>Patient</th>
    <th>Doctor</th>
    <th>Status</th>
    <th>Actions</th>
</tr>
</thead>
<tbody id="ordersTable">
<tr><td colspan="6" class="text-center">Loading...</td></tr>
</tbody>
</table>
</div>

<!-- Modals -->
<div class="modal fade" id="viewModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><h5>Order Details</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body" id="viewModalBody">Loading...</div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="confirmModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 id="confirmTitle"></h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" id="confirmOrderId">
        Comment:
        <textarea class="form-control" id="confirmComment"></textarea>
        <div id="confirmMsg" class="mt-2"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" id="confirmBtn">Confirm</button>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="prescription_queue.js"></script>
</body>
</html>
