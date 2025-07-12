<?php
require 'db.php'; // your database connection

// Get count of sent invitations
$sql_sent = "SELECT COUNT(*) as total_sent FROM invitations WHERE status='sent'";
$result_sent = $conn->query($sql_sent);
$row_sent = $result_sent->fetch_assoc();
$total_sent = $row_sent['total_sent'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>PSME Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            overflow-x: hidden;
        }
        .sidebar {
            height: 100vh;
            background: #004085;
            color: white;
            padding-top: 20px;
            position: fixed;
            width: 220px;
        }
        .sidebar a {
            color: white;
            display: block;
            padding: 10px 20px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: #007bff;
            text-decoration: none;
        }
        .content {
            margin-left: 220px;
            padding: 20px;
        }
        .dashboard-header {
            background: linear-gradient(45deg, #004085, #007bff);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .dashboard-header h2 {
            margin: 0;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center">Admin Panel</h4>
    <hr style="background: white;">
    <a href="#dashboard" onclick="showTab('dashboard')">Dashboard</a>
    <a href="#individual" onclick="showTab('individual')">Individual</a>
    <a href="#company" onclick="showTab('company')">Company</a>
</div>

<!-- Main Content -->
<div class="content">

    <!-- Header -->
    <div class="dashboard-header text-center">
        <h2>PSME Invitation Admin Dashboard</h2>
    </div>

    <!-- Dashboard Tab -->
    <div id="dashboardTab">
        <h4>Overall Status</h4>
        <div class="row">
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Emails Sent</div>
                    <div class="card-body">
                        <h5 class="card-title text-center" style="font-size:2rem;"><?= $total_sent ?></h5>
                        <p class="card-text text-center">Total number of invitations sent.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Individual Tab -->
    <div id="individualTab" style="display: none;">
        <h4>Individual Invitations</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Event</th>
                        <th>Company</th>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th>SOA</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT * FROM invitations ORDER BY event, company";
                $result = $conn->query($sql);
                if ($result->num_rows > 0):
                    while($row = $result->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['event']) ?></td>
                        <td><?= htmlspecialchars($row['company']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['designation']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <a href="generate_soa.php?email=<?= urlencode($row['email']) ?>" class="btn btn-sm btn-success">Generate SOA</a>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No records found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Company Tab (left blank) -->
    <div id="companyTab" style="display: none;">
        <h4>Company Summary</h4>
        <p>This section is under development.</p>
    </div>

</div>

<script>
function showTab(tab) {
    document.getElementById('dashboardTab').style.display = (tab === 'dashboard') ? 'block' : 'none';
    document.getElementById('individualTab').style.display = (tab === 'individual') ? 'block' : 'none';
    document.getElementById('companyTab').style.display = (tab === 'company') ? 'block' : 'none';
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php $conn->close(); ?>