<?php
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $correctPassword = 'psmenatcon2025';

    if ($password === $correctPassword) {
        // Set session but delay redirect via JS
        $_SESSION['user'] = 'admin';
        $showLoading = true;
    } else {
        $error = "Incorrect password.";
        $showLoading = false;
    }
} else {
    $showLoading = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PSME Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #007bff, #001f54);
            overflow: hidden;
        }
        .login-card {
            background: #fff;
            padding: 3rem 2rem;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 2;
        }
        .brand-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .brand-header img {
            width: 80px;
            margin-bottom: 10px;
        }
        .brand-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #001f54;
        }
        .btn-gradient {
            background: linear-gradient(to right, #007bff, #0056b3);
            color: #fff;
            font-weight: 600;
            border: none;
        }
        .btn-gradient:hover {
            background: linear-gradient(to right, #0056b3, #003f7f);
        }
        .error-message {
            color: #dc3545;
            font-size: 0.95rem;
            text-align: center;
            margin-bottom: 1rem;
        }

        /* Loading Overlay */
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.85);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            font-size: 2rem;
            display: none;
        }
        .spinner-border {
            width: 3rem;
            height: 3rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-header">
        <img src="img/natconlogo.jpg" alt="PSME Logo">
        <h2>PSME Admin Login</h2>
    </div>

    <?php if (!empty($error)): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <div class="input-group">
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
                <span class="input-group-text" id="togglePassword">
                    <i class="bi bi-eye-slash" id="eyeIcon"></i>
                </span>
            </div>
        </div>

        <button type="submit" class="btn btn-gradient w-100 mt-3">Login</button>
    </form>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay">
    <div class="spinner-border text-light" role="status"></div>
    <div id="loadingText">Syncing data... 0%</div>
</div>

<!-- Toggle Password Script -->
<script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    togglePassword.addEventListener('click', () => {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        eyeIcon.classList.toggle('bi-eye');
        eyeIcon.classList.toggle('bi-eye-slash');
    });

    // Show loading if login is successful
    <?php if ($showLoading): ?>
    const overlay = document.getElementById('loadingOverlay');
    const text = document.getElementById('loadingText');
    overlay.style.display = 'flex';
    
    let percent = 0;
    const interval = setInterval(() => {
        percent++;
        text.innerText = `Syncing data... ${percent}%`;
        if (percent >= 100) {
            clearInterval(interval);
            window.location.href = "admin_dashboard.php";
        }
    }, 20); // 100% in 2 seconds
    <?php endif; ?>
</script>

</body>
</html>