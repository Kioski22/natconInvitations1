<!DOCTYPE html>
<html>
<head>
    <title>PSME Invitation Request Form</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body, html {
            height: 100%;
            margin: 0;
        }

        .bg-blur {
            position: fixed;
            top: 0; left: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            z-index: -1;
        }

        .bg-blur img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: blur(10px) brightness(0.8);
            transform: scale(1.05);
        }

        .psme-logo {
            width: 80px;
        }

        .card {
            border: none;
            border-radius: 10px;
            background-color: rgba(255,255,255,0.95);
        }

        .card-header {
            background: linear-gradient(45deg, #004085, #007bff);
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .card-body {
            padding: 2rem;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0069d9;
        }

        /* Fullscreen overlay loader */
        .loading-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.85);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .spinner-border {
            width: 4rem;
            height: 4rem;
        }

        .spinner-text {
            margin-top: 1rem;
            font-weight: 600;
            color: #004085;
        }
    </style>
</head>
<body>

    <!-- Blurred background image -->
    <div class="bg-blur">
        <img src="img/natconlogo.jpg" alt="Background">
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="spinner-text">Sending Request...</div>
        </div>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <!-- Card -->
                <div class="card shadow">
                    <div class="card-header text-white text-center">
                        <img src="img/natconlogo.jpg" alt="PSME Logo" class="psme-logo mb-2">
                        <h4 class="mb-0">Invitation Request Form</h4>
                        <small>73rd PSME National Convention</small>
                    </div>

                    <div class="card-body">
                        <form id="invitationForm" action="process_request.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Event</label>
                                <input type="text" name="event" class="form-control" value="73rd PSME National Convention" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <button type="button" class="btn btn-outline-primary btn-sm mb-2" id="toggleHrEmail">
                                    <span id="toggleIcon">＋</span> Add HR Email Address
                                </button>

                                <div id="hrEmailSection" style="display: none;">
                                    <label class="form-label">HR Email Address</label>
                                    <input type="email" name="hr_email" class="form-control">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Designation</label>
                                <input type="text" name="designation" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Company</label>
                                <input type="text" name="company" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" rows="3" class="form-control" required></textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Submit Request</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title">Success</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            Your invitation request has been submitted successfully.
          </div>
        </div>
      </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">Error</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            There was an error submitting your request. Please try again.
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS + jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- AJAX form submission with loader and modal auto-close -->
    <script>
    $(document).ready(function(){
        $('#invitationForm').on('submit', function(e){
            e.preventDefault();
            $('#loadingOverlay').fadeIn();

            $.ajax({
                url: 'process_request.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response){
                    console.log(response);
                    $('#loadingOverlay').fadeOut();

                    if(response.includes('success')){
                        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                        successModal.show();
                        $('#invitationForm')[0].reset();
                        setTimeout(() => { successModal.hide(); }, 5000);
                    } else {
                        var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                        errorModal.show();
                        setTimeout(() => { errorModal.hide(); }, 5000);
                    }
                },
                error: function(xhr, status, error){
                    console.log(error);
                    $('#loadingOverlay').fadeOut();
                    var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModal.show();
                    setTimeout(() => { errorModal.hide(); }, 5000);
                }
            });
        });
    });
    </script>

    <script>
        $(document).ready(function() {
            $('#toggleHrEmail').on('click', function() {
                $('#hrEmailSection').slideToggle();
                const icon = $('#toggleIcon');
                icon.text(icon.text() === '＋' ? '−' : '＋');
            });
        });
    </script>

</body>
</html>