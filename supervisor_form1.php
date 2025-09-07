<?php
// supervisor_form.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Supervisor Invitation Request</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins';
      background-color: #000e6c;
    }

    .btn-primary{
      background-color: #000e6c !important;
    }

    .logo {
      width: 100px;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-primary text-white text-center rounded-top-4">
          <img class="logo" src="https://natcon.psmeinc.org.ph/wp-content/uploads/2025/06/NatCon-1-600x600.png" alt="Natcon Logo">
          <h4 class="mb-0">PSME Supervisor Invitation Request</h4>
        </div>
        <div class="card-body p-4">

          <form action="process_supervisor_request.php" method="POST">
            
            <!-- Supervisor Name -->
            <div class="mb-3">
              <label for="supervisor_name" class="form-label fw-semibold">Name of the Supervisor</label>
              <input type="text" class="form-control" id="supervisor_name" name="supervisor_name" placeholder="Enter full name" required>
            </div>

            <!-- Designation -->
            <div class="mb-3">
              <label for="designation" class="form-label fw-semibold">Designation / Position</label>
              <input type="text" class="form-control" id="designation" name="designation" placeholder="e.g. Plant Manager, Head Engineer" required>
            </div>

            <!-- Company -->
            <div class="mb-3">
              <label for="company" class="form-label fw-semibold">Company</label>
              <input type="text" class="form-control" id="company" name="company" placeholder="Enter company name" required>
            </div>

            <!-- Company Address -->
            <div class="mb-3">
              <label for="company_address" class="form-label fw-semibold">Company Address</label>
              <textarea class="form-control" id="company_address" name="company_address" rows="2" placeholder="Enter company address" required></textarea>
            </div>

            <!-- Email -->
            <div class="mb-3">
              <label for="email" class="form-label fw-semibold">Email Address</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="example@email.com" required>
            </div>

            <!-- Submit Button -->
            <div class="d-grid">
              <button type="submit" class="btn btn-primary btn-lg rounded-3">Send Invitation</button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
