<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require 'db.php';

// Pagination setup for invitations
$limit = 7; // records per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get count of sent invitations
$sql_sent = "SELECT COUNT(*) as total_sent FROM invitations WHERE status='sent'";
$result_sent = $conn->query($sql_sent);
$row_sent = $result_sent->fetch_assoc();
$total_sent = $row_sent['total_sent'];

// ✅ Define SQL for fetching companies
$sql_companies = "
    SELECT 
        c.company_id, 
        c.company_name, 
        c.company_address,  -- ✅ this line gets the real address
        c.excel_filename
    FROM companies c
    INNER JOIN delegates d ON c.company_id = d.company_id
    GROUP BY c.company_id, c.company_name, c.company_address, c.excel_filename
    ORDER BY c.company_name
";

// ✅ Run the query
$companies = [];
$result_companies = $conn->query($sql_companies);
if ($result_companies && $result_companies->num_rows > 0) {
    while ($row = $result_companies->fetch_assoc()) {
        $companies[] = $row;
    }
}

// Get SOA Released count (modify this based on actual table structure)
$sql_soa = "SELECT COUNT(*) as total_soa FROM soa_sequence";
$result_soa = $conn->query($sql_soa);
$row_soa = $result_soa->fetch_assoc();
$total_soa = $row_soa['total_soa'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>PSME Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --secondary-color: #64748b;
            --accent-color: #06b6d4;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --light-gray: #f8fafc;
            --medium-gray: #e2e8f0;
            --dark-gray: #475569;
            --white: #ffffff;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --border-radius: 8px;
            --border-radius-lg: 12px;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body { 
            overflow-x: hidden; 
            background: var(--light-gray);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: var(--dark-gray);
        }
        
        .sidebar {
            height: 100vh; 
            background: var(--white);
            color: var(--dark-gray);
            position: fixed; 
            width: 260px; 
            box-shadow: var(--shadow-lg);
            border-right: 1px solid var(--medium-gray);
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 24px 20px;
            border-bottom: 1px solid var(--medium-gray);
        }
        
        .sidebar-header h4 {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-color);
            margin: 0;
            text-align: center;
        }
        
        .sidebar-nav {
            padding: 16px 0;
        }
        
        .sidebar a {
            color: var(--secondary-color);
            display: flex;
            align-items: center;
            padding: 12px 20px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        
        .sidebar a i {
            width: 20px;
            margin-right: 12px;
            font-size: 16px;
        }
        
        .sidebar a:hover {
            background: var(--primary-light);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
        }
        
        .sidebar a.active-link {
            background: var(--primary-light);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
            font-weight: 600;
        }
        
        .content { 
            margin-left: 260px; 
            padding: 32px;
            min-height: 100vh;
        }
        
        .dashboard-header { 
            background: var(--white);
            padding: 24px 32px;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--medium-gray);
            margin-bottom: 32px;
        }
        
        .dashboard-header h2 {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
            margin: 0;
            text-align: center;
        }
        
        .card {
            background: var(--white);
            border: 1px solid var(--medium-gray);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }
        
        .card:hover {
            box-shadow: var(--shadow-md);
        }
        
        .card-header {
            background: var(--white);
            border-bottom: 1px solid var(--medium-gray);
            padding: 16px 20px;
            font-weight: 600;
            font-size: 14px;
            border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
        }
        
        .card-body {
            padding: 24px;
        }
        
        .status-card {
            border-left: 4px solid var(--success-color);
        }
        
        .status-card .card-header {
            color: var(--success-color);
        }
        
        .status-card h1 {
            font-size: 32px;
            font-weight: 700;
            color: var(--success-color);
            margin: 0;
        }
        
        .table {
            background: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--medium-gray);
        }
        
        .table th { 
            background: var(--primary-color);
            color: var(--white);
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 16px 12px;
            border: none;
        }
        
        .table td {
            padding: 12px;
            vertical-align: middle;
            border-color: var(--medium-gray);
        }
        
        .table-hover tbody tr:hover {
            background-color: var(--light-gray);
        }
        
        .btn {
            font-weight: 500;
            padding: 12px 24px;
            border-radius: var(--border-radius);
            border: none;
            transition: all 0.2s ease;
            font-size: 14px;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: var(--white);
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }
        
        .form-control, .form-select {
            border: 1px solid var(--medium-gray);
            border-radius: var(--border-radius);
            padding: 12px 16px;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px var(--primary-light);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark-gray);
            margin-bottom: 8px;
        }
        
        .pagination .page-link {
            border: 1px solid var(--medium-gray);
            color: var(--secondary-color);
            padding: 8px 12px;
            margin: 0 2px;
            border-radius: var(--border-radius);
        }
        
        .pagination .page-link:hover {
            background: var(--primary-light);
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .pagination .page-item.active .page-link {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .tab-content {
            background: var(--white);
            border-radius: var(--border-radius-lg);
            padding: 32px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--medium-gray);
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--primary-light);
        }
        
        .form-section {
            margin-bottom: 32px;
        }
        
        .table-responsive {
            border-radius: var(--border-radius);
            overflow: hidden;
        }
        
        .logout-link {
            margin-top: auto;
            border-top: 1px solid var(--medium-gray);
            padding-top: 16px;
        }
        
        .logout-link a {
            color: var(--danger-color) !important;
        }
        
        .logout-link a:hover {
            background: #fef2f2 !important;
            border-left-color: var(--danger-color) !important;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h4>PSME Admin</h4>
    </div>
    <nav class="sidebar-nav">
        <a href="javascript:void(0);" onclick="showTab('dashboard')" class="active-link">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
        <a href="javascript:void(0);" onclick="showTab('individual')">
            <i class="bi bi-person-lines-fill"></i>
            <span>Individual</span>
        </a>
        <a href="javascript:void(0);" onclick="showTab('company')">
            <i class="bi bi-building"></i>
            <span>Company</span>
        </a>
        <a href="javascript:void(0);" onclick="showTab('soaGenerator')">
            <i class="bi bi-file-earmark-pdf"></i>
            <span>Auto SOA Generator</span>
        </a>
        <a href="manual_soa.php">
            <i class="bi bi-file-earmark-text"></i>
            <span>Manual SOA Generator</span>
        </a>
        <a href="javascript:void(0);" onclick="showTab('soaReleased')">
            <i class="bi bi-check-circle"></i>
            <span>SOA Released</span>
        </a>
    </nav>
    <div class="logout-link">
        <a href="logout.php">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<div class="content">

    <div class="dashboard-header">
        <h2>PSME Invitation Dashboard</h2>
    </div>

    <!-- Dashboard Tab -->
    <div id="dashboardTab" class="tab-content">
        <h4 class="section-title">Overview</h4>
        <div class="card status-card">
            <div class="card-header">
                <i class="bi bi-envelope-fill me-2"></i>Emails Sent
            </div>
            <div class="card-body text-center">
                <h1><?= $total_sent ?></h1>
                <p class="mb-0">Total invitations sent</p>
            </div>
        </div>
    </div>

    <!-- Individual Tab -->
    <div id="individualTab" class="tab-content" style="display:none;">
        <h4 class="section-title">Individual Invitations</h4>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Event</th><th>Company</th><th>Email</th><th>Name</th><th>Designation</th><th>Address</th><th>Status</th>
                    </tr>
                </thead>
            <tbody>
            <?php
            // Get total records for pagination
            $sql_count = "SELECT COUNT(*) as total FROM invitations";
            $result_count = $conn->query($sql_count);
            $total_records = $result_count->fetch_assoc()['total'];
            $total_pages = ceil($total_records / $limit);

            $sql = "SELECT * FROM invitations ORDER BY event, company LIMIT $limit OFFSET $offset";
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
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="7" class="text-center">No records found.</td></tr>
            <?php endif; ?>
            </tbody>
            </table>
        </div>
        <!-- Pagination controls -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Showing <?= ($offset + 1) ?> to <?= min($offset + $limit, $total_records) ?> of <?= $total_records ?> entries
            </div>
            <nav aria-label="Individual pagination">
                <ul class="pagination pagination-sm mb-0">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=1" onclick="loadIndividualPage(1); return false;" aria-label="First">
                                <i class="bi bi-chevron-double-left"></i>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page-1 ?>" onclick="loadIndividualPage(<?= $page-1 ?>); return false;" aria-label="Previous">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php
                    // Show page numbers with ellipsis for large page counts
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    if ($start_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=1" onclick="loadIndividualPage(1); return false;">1</a>
                        </li>
                        <?php if ($start_page > 2): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>" onclick="loadIndividualPage(<?= $i ?>); return false;"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $total_pages ?>" onclick="loadIndividualPage(<?= $total_pages ?>); return false;"><?= $total_pages ?></a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page+1 ?>" onclick="loadIndividualPage(<?= $page+1 ?>); return false;" aria-label="Next">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $total_pages ?>" onclick="loadIndividualPage(<?= $total_pages ?>); return false;" aria-label="Last">
                                <i class="bi bi-chevron-double-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Company Tab -->
    <div id="companyTab" class="tab-content" style="display:none;">
        <h4 class="section-title">Company Summary</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Company Name</th>
                        <th>Address</th>
                        <th>Excel Filename</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($companies)): ?>
                        <?php foreach ($companies as $comp): ?>
                            <tr>
                                <td><?= htmlspecialchars($comp['company_id']) ?></td>
                                <td><?= htmlspecialchars($comp['company_name']) ?></td>
                                <td><?= htmlspecialchars($comp['company_address']) ?></td>
                                <td><?= htmlspecialchars($comp['excel_filename']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center text-muted">No companies found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- SOA Generator Tab -->
    <div id="soaGeneratorTab" style="display:none;">
        <h4>Automatic SOA Generator</h4>
        <form action="generate_soa_manual.php" method="POST" target="_blank">
            <div class="mb-3">
                <label>Select Company</label>
                <select name="company_id" id="companySelect" class="form-select" required>
                    <option value="" disabled selected>Select company</option>
                    <?php foreach ($companies as $comp): ?>
                       <option 
                            value="<?= htmlspecialchars($comp['company_id']) ?>" 
                            data-address="<?= htmlspecialchars($comp['company_address']) ?>">
                            <?= htmlspecialchars($comp['company_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Address</label>
                <input type="text" name="company_address" id="companyAddress" class="form-control" readonly required>
            </div>

            <div class="mb-3">
                <label>TIN</label>
                <input type="text" name="tin" class="form-control" placeholder="Enter TIN">
            </div>

            <div class="mb-3">
                <label>Business Style</label>
                <input type="text" name="business_style" class="form-control" placeholder="Enter business style">
            </div>

            <div class="mb-3">
                <label>Particulars</label>
                <textarea name="particulars" class="form-control" rows="2" placeholder="Enter particulars"></textarea>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="participantsTable">
                    <thead class="table-primary">
                        <tr>
                            <th>No.</th>
                            <th>Item No.</th>
                            <th>Participant Name</th>
                            <th>Type of Membership</th>
                            <th>Registration Fee</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Generate SOA PDF
                </button>
                <button type="button" class="btn btn-outline-secondary" id="clearAutoBtn">
                    <i class="bi bi-arrow-clockwise me-2"></i>Clear Form
                </button>
            </div>
        </form>
    </div>

    <!-- SOA Released Tab -->
    <div id="soaReleasedTab" style="display:none;">
        <h4>SOA Released</h4>
        <div class="card text-white bg-primary mb-3">
            <div class="card-header"><i class="bi bi-check-circle"></i> SOA Released</div>
            <div class="card-body text-center">
                <h1><?= $total_soa ?></h1>
                <p>Total SOAs released</p>
            </div>
        </div>

        <!-- Sample table of released SOAs -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>SOA Number</th>
                    <th>Date Released</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $sql = "SELECT soa_number, created_at FROM soa_sequence ORDER BY id DESC";
            $result = $conn->query($sql);
            if ($result->num_rows > 0):
                while($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['soa_number']) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="2" class="text-center">No SOA released records found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function showTab(tab) {
    // Hide all tabs first
    const tabs = ['dashboardTab', 'individualTab', 'companyTab', 'soaGeneratorTab', 'soaReleasedTab'];
    tabs.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.style.display = 'none';
        }
    });
    
    // Show the selected tab
    const targetTab = document.getElementById(tab + 'Tab');
    if (targetTab) {
        targetTab.style.display = 'block';
    }

    // Update active link in sidebar
    const links = document.querySelectorAll('.sidebar a');
    links.forEach(link => {
        link.classList.remove('active-link');
        const onclick = link.getAttribute('onclick');
        if (onclick && onclick.includes("'" + tab + "'")) {
            link.classList.add('active-link');
        }
    });
    
    console.log('Switched to tab:', tab);
}

$('#companySelect').on('change', function(){
    var address = $(this).find(':selected').data('address');
    $('#companyAddress').val(address);

    var company_id = $(this).val(); // use the selected value directly

    $.ajax({
        url: 'get_participants.php',
        type: 'POST',
        data: { company_id: company_id }, // ✅ fixed variable
        dataType: 'json',
        success: function(data){
            var tbody = $('#participantsTable tbody');
            tbody.empty();
            if(data.length > 0){
                $.each(data, function(i, name){
                    tbody.append(
                        '<tr>' +
                        '<td>'+(i+1)+'</td>' +
                        '<td><select name="item_number[]" class="form-select" required onchange="updateMembershipType(this)">' +
                            '<option value="">Select</option>' +
                            '<option value="0001">0001</option>' +
                            '<option value="0002">0002</option>' +
                            '<option value="0003">0003</option>' +
                            '<option value="0004">0004</option>' +
                            '<option value="0005">0005</option>' +
                            '<option value="0006">0006</option>' +
                            '<option value="0007">0007</option>' +
                            '<option value="0008">0008</option>' +
                            '<option value="0009">0009</option>' +
                            '<option value="0010">0010</option>' +
                            '<option value="0011">0011</option>' +
                            '<option value="0012">0012</option>' +
                            '<option value="0013">0013</option>' +
                            '<option value="0014">0014</option>' +
                        '</select></td>' +
                        '<td><input type="hidden" name="participants[]" value="'+name+'">'+name+'</td>' +
                        '<td><input type="text" name="membership_type[]" class="form-control" readonly></td>' +
                        '<td><input type="number" name="registration_fee[]" step="0.01" class="form-control" required></td>' +
                        '<td><input type="number" name="amount[]" step="0.01" class="form-control" required></td>' +
                        '</tr>'
                    );
                });
            } else {
                tbody.append('<tr><td colspan="6" class="text-center">No participants found.</td></tr>');
            }
        }
    });
});

function updateMembershipType(select){
    const mapping = {
        '0001': {type: 'Regular Member', price: 3700},
        '0002': {type: 'Reg. Mem. - Senior', price: 3000},
        '0003': {type: 'Reg. Mem. - PWD', price: 3000},
        '0004': {type: 'Life Member', price: 3000},
        '0005': {type: 'Non-Member', price: 4700},
        '0006': {type: 'Early Bird Reg. Member', price: 3200},
        '0007': {type: 'Associate Member', price: 3000},
        '0008': {type: 'EB Reg. Member with 3-D Meal Package', price: 4700},
        '0009': {type: 'Non-Member with 3-D Meal Package', price: 6200},
        '0010': {type: 'New Board Passer', price: 3000},
        '0011': {type: 'Reg. Mem. - Senior with 3-D Meal Package', price: 4500},
        '0012': {type: '3-D Meal Package', price: 1500},
        '0013': {type: 'Life Member with 3-D Meal Package', price: 4500},
        '0014': {type: 'Regular Member with 3-D Meal Package', price: 5200}
    };

    const data = mapping[select.value] || {type: '', price: ''};
    const row = $(select).closest('tr');
    row.find('input[name="membership_type[]"]').val(data.type);
    row.find('input[name="registration_fee[]"]').val(data.price);
    row.find('input[name="amount[]"]').val(data.price);
}

// Clear Automatic SOA Generator form - exactly like manual_soa.php
$('#clearAutoBtn').on('click', function() {
    if (confirm('Clear all form data?')) {
        // Reset the entire form
        document.querySelector('#soaGeneratorTab form').reset();
        
        // Reset company dropdown to first option (Select company)
        $('#autoCompanySelect').prop('selectedIndex', 0);
        
        // Clear company address field
        $('#autoCompanyAddress').val('');
        
        // Clear participants table completely and show empty state
        $('#autoParticipantsTable tbody').html(`
            <tr class="empty-row">
                <td colspan="6" class="text-center py-4">
                    <div class="empty-state">
                        <i class="bi bi-person-plus"></i>
                        <div>Select a company to load participants</div>
                    </div>
                </td>
            </tr>
        `);
    }
});

// Load individual page with AJAX
function loadIndividualPage(page) {
    // Just update the URL and reload to get new data
    const url = new URL(window.location);
    url.searchParams.set('page', page);
    window.location.href = url.toString();
}

// Ensure correct tab is shown on page reload with pagination
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.search.includes('page=')) {
        showTab('individual');
    }
});
</script>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>