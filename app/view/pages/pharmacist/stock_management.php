<?php include 'controller.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Medicine Info</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/stock_management.css">
</head>
<body>

<div class="container my-5">
<h2 class="mb-4">Medicine Information</h2>

<!-- Search Form -->
<form method="get" action="controller.php" class="mb-3 row g-2">
    <div class="col-auto">
        <input type="text" class="form-control" name="search" placeholder="Search by ID or Name"
               value="<?php echo htmlspecialchars($search); ?>">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-custom">Search</button>
        <a href="index.php" class="btn btn-secondary">Reset</a>
    </div>
</form>

<!-- Add Medicine Button -->
<button class="btn btn-custom mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Add Medicine</button>

<!-- Table -->
<table class="table table-bordered table-striped">
<thead class="table-dark">
<tr>
<th>ID</th><th>Name</th><th>Price</th><th>Quantity</th><th>Description</th><th>Actions</th>
</tr>
</thead>
<tbody>

<?php while ($medicine = $medicines->fetch_assoc()): ?>
<tr>
<td><?php echo $medicine['medicine_id']; ?></td>
<td><?php echo $medicine['medicine_name']; ?></td>
<td><?php echo $medicine['medicine_price']; ?></td>
<td><?php echo $medicine['medicine_quantity']; ?></td>
<td><?php echo $medicine['medicine_description']; ?></td>
<td>
<button class="btn btn-sm btn-warning" data-bs-toggle="modal"
        data-bs-target="#editModal<?php echo $medicine['medicine_id']; ?>">Edit</button>

<a href="controller.php?delete=<?php echo $medicine['medicine_id']; ?>"
   class="btn btn-sm btn-danger" onclick="return confirm('Delete this medicine?')">Delete</a>
</td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" action="controller.php" onsubmit="return validateForm(this)">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Add Medicine</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-3"><label>Name</label><input type="text" class="form-control" name="medicine_name" required></div>
        <div class="mb-3"><label>Price</label><input type="number" step="0.01" class="form-control" name="medicine_price" required></div>
        <div class="mb-3"><label>Quantity</label><input type="number" class="form-control" name="medicine_quantity" required></div>
        <div class="mb-3"><label>Description</label><input type="text" class="form-control" name="medicine_description"></div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="add" class="btn btn-custom">Add</button>
      </div>
    </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/pharmacist_stock_management.js"></script>
</body>
</html>
