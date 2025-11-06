

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Patient Orders</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS (your 5 classes) -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold text-primary">Patient Orders</h1>
  </div>

  <!-- Search -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-2">
        <div class="col-md-8">
          <input type="text" class="form-control" id="searchPatient" placeholder="Search by Patient ID...">
        </div>
        <div class="col-md-4">
          <button class="btn btn-outline-secondary w-100" id="clearSearch">Clear</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Patients Table -->
  <div class="card" id="patientsCard">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">Patients</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>ID</th><th>Name</th><th>DOB</th><th>Phone</th><th>Gender</th><th>Action</th>
            </tr>
          </thead>
          <tbody id="patientsTableBody">
            <tr><td colspan="6" class="text-center py-4">Loading...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Order History (hidden) -->
  <section id="orderHistorySection" class="mt-4" style="display:none;">
    <div class="card bg-primary text-white mb-4">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h4 id="bannerPatientName">-</h4>
          <p id="bannerPatientId" class="mb-0">Patient ID: -</p>
        </div>
        <button class="btn btn-light" id="backToPatients">Back</button>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">Patient Info</div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <p><strong>DOB:</strong> <span id="detailDob">-</span></p>
            <p><strong>Phone:</strong> <span id="detailPhone">-</span></p>
            <p><strong>Gender:</strong> <span id="detailGender">-</span></p>
          </div>
          <div class="col-md-6">
            <p><strong>Email:</strong> <span id="detailEmail">-</span></p>
            <p><strong>Address:</strong> <span id="detailAddress">-</span></p>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">Order History</div>
      <div class="card-body" id="ordersContainer"></div>
    </div>
  </section>

</div>

<!-- JavaScript (external) -->
<script src="assets/js/main.js"></script>
</body>
</html>