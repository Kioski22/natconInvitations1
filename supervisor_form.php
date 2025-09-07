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

    .btn-success {
      background-color: #000e6c !important;
    }

    .btn-success:hover {
      background-color: #0006ec !important;
    }

    .btn-primary:hover {
      background-color: #0006ec !important;
    }

    .logo {
      width: 100px;
    }

    .header-section {
      background-color: rgba(255, 255, 255, 0.84);
      color: #000e6c;
      min-height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 2rem 1rem;
      border-radius: 0;
      box-shadow: 0 8px 15px rgba(6, 0, 176, 1);
    }

    .logo {
      width: clamp(80px, 15vw, 300px); /* Min 80px, scales with viewport, max 150px */
    }

    .form-section {
      padding: 2rem;
    }

    .card {
      overflow: hidden;
    }

    .modal-header {
      background-color: #000e6c;
    }

    @media (max-width: 768px) {
      .header-section {
        min-height: auto;
        padding: 1.5rem 1rem;
        border-radius: 0;
        box-shadow: 0 8px 15px rgba(6, 0, 176, 0.5);
      }
      
      .form-section {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>

<div class="container py-5">
  <div class="row justify-content-center mt-5">
    <div class="col-lg-10 col-md-12">
      <div class="card shadow-lg border-0 rounded-4">
        <div class="row g-0 h-100">
          
          <!-- Left Side: Header with Logo and Title -->
          <div class="col-md-5">
            <div class="header-section text-center rounded-start-4">
              <img class="logo mb-3" src="https://natcon.psmeinc.org.ph/wp-content/uploads/2025/06/NatCon-1-600x600.png" alt="Natcon Logo">
              <h4 class="mb-0 fw-bold"> 73rd NatCon Company <br> Invitation Request</h4>
            </div>
          </div>

          <!-- Right Side: Form -->
          <div class="col-md-7">
            <div class="form-section">

              <form action="process_supervisor_request.php" method="POST">
                
                <!-- Supervisor Name -->
                <div class="mb-3">
                  <label for="supervisor_name" class="form-label fw-semibold">Name of the Supervisor <i>(include salutations Ex. Engr. Juan)</i> </label>
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

                <br>

                <!-- Submit Button -->
                <div class="d-grid">
                  <button type="button" class="btn btn-primary btn-lg rounded-3" onclick="submitForm()">Send Invitation</button>
                </div>

              </form>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-white">
        <h5 class="modal-title" id="successModalLabel">
          <i class="bi bi-check-circle me-2"></i>Success!
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center py-4">
        <div class="mb-3">
          <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
        </div>
        <h6 class="mb-0">Invitation sent to your company successfully.</h6>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function submitForm() {
  // Get form elements
  const form = document.querySelector('form');
  const inputs = form.querySelectorAll('input[required], textarea[required]');
  
  // Check if all required fields are filled
  let allValid = true;
  inputs.forEach(input => {
    if (!input.value.trim()) {
      allValid = false;
      input.classList.add('is-invalid');
    } else {
      input.classList.remove('is-invalid');
    }
  });
  
  // If all fields are valid, show success modal
  if (allValid) {
    // You can submit the form here if needed
    // form.submit();
    
    // Show success modal
    const modal = new bootstrap.Modal(document.getElementById('successModal'));
    modal.show();
    
    // Optionally clear the form
    form.reset();
  } else {
    // If validation fails, you could show an error message
    alert('Please fill in all required fields.');
  }
}
</script>

</body>
</html>