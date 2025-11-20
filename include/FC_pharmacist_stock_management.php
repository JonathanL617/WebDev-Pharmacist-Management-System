<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "latest"; // Change to your DB

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Function to generate next medicine ID
function getNextMedicineID($conn) {
    $result = $conn->query("SELECT medicine_id FROM medicine_info ORDER BY medicine_id DESC LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $lastID = $result->fetch_assoc()['medicine_id'];
        $num = (int)substr($lastID, 1) + 1;
        return 'M' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }
    return 'M001';
}

// Handle Add
if(isset($_POST['add'])){
    $id = getNextMedicineID($conn);
    $name = $_POST['medicine_name'];
    $price = floatval($_POST['medicine_price']);
    $quantity = intval($_POST['medicine_quantity']);
    $desc = $_POST['medicine_description'];

    $stmt = $conn->prepare("INSERT INTO medicine_info (medicine_id, medicine_name, medicine_price, medicine_quantity, medicine_description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $id, $name, $price, $quantity, $desc);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit;
}

// Handle Update
if(isset($_POST['update'])){
    $id = $_POST['medicine_id'];
    $name = $_POST['medicine_name'];
    $price = floatval($_POST['medicine_price']);
    $quantity = intval($_POST['medicine_quantity']);
    $desc = $_POST['medicine_description'];

    $stmt = $conn->prepare("UPDATE medicine_info SET medicine_name=?, medicine_price=?, medicine_quantity=?, medicine_description=? WHERE medicine_id=?");
    $stmt->bind_param("sdiss", $name, $price, $quantity, $desc, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit;
}

// Handle Delete
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM medicine_info WHERE medicine_id=?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit;
}

// Handle Search
$search = "";
if(isset($_GET['search'])) {
    $search = $_GET['search'];
    $stmt = $conn->prepare("SELECT * FROM medicine_info WHERE medicine_id LIKE ? OR medicine_name LIKE ? ORDER BY medicine_id ASC");
    $likeSearch = "%".$search."%";
    $stmt->bind_param("ss", $likeSearch, $likeSearch);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM medicine_info ORDER BY medicine_id ASC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Medicine Info</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f2f2f2; }
.card { background-color: #e0e0e0; }
.btn-custom { background-color: #6c757d; color: white; }
.btn-custom:hover { background-color: #5a6268; }
</style>
</head>
<body>
<div class="container my-5">
<h2 class="mb-4">Medicine Information</h2>

<!-- Search Form -->
<form method="get" class="mb-3 row g-2">
    <div class="col-auto">
        <input type="text" class="form-control" name="search" placeholder="Search by ID or Name" value="<?php echo htmlspecialchars($search); ?>">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-custom">Search</button>
        <a href="index.php" class="btn btn-secondary">Reset</a>
    </div>
</form>

<!-- Add Medicine Button -->
<button class="btn btn-custom mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Add Medicine</button>

<!-- Medicine Table -->
<table class="table table-bordered table-striped">
<thead class="table-dark">
<tr>
<th>ID</th>
<th>Name</th>
<th>Price</th>
<th>Quantity</th>
<th>Description</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php while($medicine = $result->fetch_assoc()): ?>
<tr>
<td><?php echo $medicine['medicine_id']; ?></td>
<td><?php echo $medicine['medicine_name']; ?></td>
<td><?php echo $medicine['medicine_price']; ?></td>
<td><?php echo $medicine['medicine_quantity']; ?></td>
<td><?php echo $medicine['medicine_description']; ?></td>
<td>
<button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $medicine['medicine_id']; ?>">Edit</button>
<a href="index.php?delete=<?php echo $medicine['medicine_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this medicine?')">Delete</a>
</td>
</tr>

<!-- Edit Modal -->
<div class="modal fade" id="editModal<?php echo $medicine['medicine_id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post">
    <input type="hidden" name="medicine_id" value="<?php echo $medicine['medicine_id']; ?>">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Medicine <?php echo $medicine['medicine_id']; ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Medicine Name</label>
          <input type="text" class="form-control" name="medicine_name" value="<?php echo $medicine['medicine_name']; ?>" required>
        </div>
        <div class="mb-3">
          <label>Price</label>
          <input type="number" step="0.01" class="form-control" name="medicine_price" value="<?php echo $medicine['medicine_price']; ?>" required>
        </div>
        <div class="mb-3">
          <label>Quantity</label>
          <input type="number" class="form-control" name="medicine_quantity" value="<?php echo $medicine['medicine_quantity']; ?>" required>
        </div>
        <div class="mb-3">
          <label>Description</label>
          <input type="text" class="form-control" name="medicine_description" value="<?php echo $medicine['medicine_description']; ?>">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="update" class="btn btn-custom">Update</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
    </form>
  </div>
</div>

<?php endwhile; ?>
</tbody>
</table>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Medicine</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Medicine Name</label>
          <input type="text" class="form-control" name="medicine_name" required>
        </div>
        <div class="mb-3">
          <label>Price</label>
          <input type="number" step="0.01" class="form-control" name="medicine_price" required>
        </div>
        <div class="mb-3">
          <label>Quantity</label>
          <input type="number" class="form-control" name="medicine_quantity" required>
        </div>
        <div class="mb-3">
          <label>Description</label>
          <input type="text" class="form-control" name="medicine_description">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="add" class="btn btn-custom">Add</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
