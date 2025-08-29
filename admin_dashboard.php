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

// --- Manual SOA Generator: Get next SOA number ---
$manual_next_soa = '';
$sql = "SELECT soa_number FROM soa_sequence ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $last = $row['soa_number'];
    if (is_numeric($last)) {
        $manual_next_soa = str_pad($last + 1, 6, '0', STR_PAD_LEFT);
    } else if (preg_match('/(\d+)$/', $last, $m)) {
        $manual_next_soa = 'SOA-' . str_pad($m[1] + 1, 6, '0', STR_PAD_LEFT);
    } else {
        $manual_next_soa = 'SOA-000001';
    }
} else {
    $manual_next_soa = 'SOA-000001';
}
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
            --primary-color: #004085;
            --primary-light: #0056b3;
            --secondary-color: #6c757d;
            --accent-color: #007bff;
            --bg-light: #f8f9fa;
            --border-color: #e9ecef;
            --text-muted: #6c757d;
            --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-light);
            overflow-x: hidden;
            color: #2c3e50;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background: #fff;
            border-right: 1px solid var(--border-color);
            box-shadow: var(--shadow-md);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            background: var(--primary-color);
            color: white;
        }

        .sidebar-header h4 {
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.5rem;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            font-weight: 500;
        }

        .sidebar a i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1rem;
        }

        .sidebar a:hover {
            background: #f8f9fa;
            color: var(--primary-color);
        }

        .sidebar a.active-link {
            background: var(--primary-color);
            color: white;
            position: relative;
        }

        .sidebar a.active-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: var(--accent-color);
        }

        .content {
            margin-left: 260px;
            padding: 2rem;
            min-height: 100vh;
        }

        .page-header {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }

        .page-header h2 {
            font-weight: 600;
            color: var(--primary-color);
            margin: 0;
            font-size: 1.75rem;
        }

        .page-header p {
            color: var(--text-muted);
            margin: 0.5rem 0 0 0;
            font-size: 0.95rem;
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .stats-card .icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stats-card.primary .icon {
            background: rgba(0, 64, 133, 0.1);
            color: var(--primary-color);
        }

        .stats-card.success .icon {
            background: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .stats-card h3 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            color: #2c3e50;
        }

        .stats-card p {
            color: var(--text-muted);
            margin: 0.25rem 0 0 0;
            font-size: 0.9rem;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .table {
            margin: 0;
        }

        .table th {
            background: var(--bg-light);
            color: var(--primary-color);
            font-weight: 600;
            border: none;
            padding: 1rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .table td {
            padding: 1rem;
            border-top: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.625rem 1.25rem;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background: var(--primary-light);
            border-color: var(--primary-light);
            transform: translateY(-1px);
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 0.75rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 64, 133, 0.25);
        }

        .pagination .page-link {
            border-radius: 8px;
            margin: 0 0.125rem;
            border: 1px solid var(--border-color);
            color: var(--primary-color);
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .section-header {
            margin-bottom: 1.5rem;
        }

        .section-header h4 {
            font-weight: 600;
            color: var(--primary-color);
            margin: 0;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .content {
                margin-left: 0;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h4><i class="bi bi-shield-check me-2"></i>Admin Panel</h4>
    </div>
    <nav class="sidebar-nav">
        <a href="javascript:void(0);" onclick="showTab('dashboard')" class="active-link">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="javascript:void(0);" onclick="showTab('individual')">
            <i class="bi bi-person-lines-fill"></i> Individual
        </a>
        <a href="javascript:void(0);" onclick="showTab('company')">
            <i class="bi bi-building"></i> Company
        </a>
        <a href="javascript:void(0);" onclick="showTab('soaGenerator')">
            <i class="bi bi-file-earmark-pdf"></i> SOA Generator
        </a>
        <a href="javascript:void(0);" onclick="showTab('manualSoa')">
            <i class="bi bi-pencil-square"></i> Manual SOA
        </a>
        <a href="javascript:void(0);" onclick="showTab('soaReleased')">
            <i class="bi bi-check-circle"></i> SOA Released
        </a>
        <a href="logout.php" style="margin-top: auto; border-top: 1px solid var(--border-color);">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </nav>
</div>

<div class="content">
    <div class="page-header">
        <h2>PSME Invitation Management</h2>
        <p>Manage invitations, companies, and SOA generation for the 73rd PSME National Convention</p>
    </div>

    <!-- Dashboard Tab -->
    <div id="dashboardTab">
        <div class="section-header">
            <h4>Overview</h4>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="stats-card success">
                    <div class="icon">
                        <i class="bi bi-envelope-check"></i>
                    </div>
                    <h3><?= number_format($total_sent) ?></h3>
                    <p>Invitations Sent</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="stats-card primary">
                    <div class="icon">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </div>
                    <h3><?= number_format($total_soa) ?></h3>
                    <p>SOAs Released</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="stats-card primary">
                    <div class="icon">
                        <i class="bi bi-building"></i>
                    </div>
                    <h3><?= number_format(count($companies)) ?></h3>
                    <p>Companies</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Individual Tab -->
    <div id="individualTab" style="display:none;">
        <div class="section-header">
            <h4>Individual Invitations</h4>
        </div>
        
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-lines-fill me-2"></i>Invitation Records
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Company</th>
                            <th>Email</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Address</th>
                            <th>Status</th>
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
                            <td>
                                <span class="badge bg-<?= $row['status'] == 'sent' ? 'success' : 'warning' ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr>
                            <td colspan="7" class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p>No invitation records found.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination controls -->
        <?php if ($total_pages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page-1 ?>" onclick="showTab('individual'); return false;">
                            <i class="bi bi-chevron-left"></i> Previous
                        </a>
                    </li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>" onclick="showTab('individual'); return false;"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page+1 ?>" onclick="showTab('individual'); return false;">
                            Next <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>

    <!-- Company Tab -->
    <div id="companyTab" style="display:none;">
        <div class="section-header">
            <h4>Company Management</h4>
        </div>
        
        <div class="card">
            <div class="card-header">
                <i class="bi bi-building me-2"></i>Company Overview
            </div>
            <div class="card-body">
                <div class="empty-state">
                    <i class="bi bi-tools"></i>
                    <h5>Under Development</h5>
                    <p>This section is currently being developed and will be available soon.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- SOA Generator Tab -->
    <div id="soaGeneratorTab" style="display:none;">
        <div class="section-header">
            <h4>SOA Generator</h4>
            <p class="text-muted mb-0">Generate professional Statement of Account documents</p>
        </div>
        
        <div class="row g-4">
            <!-- Company Information Card -->
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-building me-2"></i>
                            <h6 class="mb-0">Company Information</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="soaForm" action="generate_soa_manual.php" method="POST" target="_blank">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-search me-1"></i>Select Company
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="company_id" id="companySelect" class="form-select form-select-lg" required>
                                        <option value="" disabled selected>
                                            <i class="bi bi-building"></i> Choose a company...
                                        </option>
                                        <?php foreach ($companies as $comp): ?>
                                           <option 
                                                value="<?= htmlspecialchars($comp['company_id']) ?>" 
                                                data-address="<?= htmlspecialchars($comp['company_address']) ?>">
                                                <?= htmlspecialchars($comp['company_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Select the company to generate SOA for</div>
                                </div>
                                
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-geo-alt me-1"></i>Company Address
                                    </label>
                                    <textarea name="company_address" id="companyAddress" class="form-control" rows="2" readonly required 
                                              placeholder="Address will be auto-filled when you select a company"></textarea>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-receipt me-1"></i>TIN Number
                                    </label>
                                    <input type="text" name="tin" class="form-control" placeholder="e.g., 123-456-789-000">
                                    <div class="form-text">Tax Identification Number</div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-briefcase me-1"></i>Business Style
                                    </label>
                                    <input type="text" name="business_style" class="form-control" placeholder="e.g., Corporation, Partnership">
                                    <div class="form-text">Type of business organization</div>
                                </div>
                                
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-card-text me-1"></i>Particulars
                                    </label>
                                    <textarea name="particulars" class="form-control" rows="3" 
                                              placeholder="Enter description of services or event details..."></textarea>
                                    <div class="form-text">Brief description of the service or event</div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions Card -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-lightning me-2"></i>
                            <h6 class="mb-0">Quick Actions</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary" id="previewBtn" disabled>
                                <i class="bi bi-eye me-2"></i>Preview SOA
                            </button>
                            <button type="submit" form="soaForm" class="btn btn-primary" id="generateBtn" disabled>
                                <i class="bi bi-file-earmark-pdf me-2"></i>Generate PDF
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="clearBtn">
                                <i class="bi bi-arrow-clockwise me-2"></i>Clear Form
                            </button>
                        </div>
                        
                        <hr>
                        
                        <div class="text-center">
                            <div class="d-flex align-items-center justify-content-center text-muted">
                                <i class="bi bi-info-circle me-2"></i>
                                <small>Select a company to load participants</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Summary Card -->
                <div class="card mt-3">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calculator me-2"></i>
                            <h6 class="mb-0">Summary</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="mb-1" id="participantCount">0</h4>
                                    <small class="text-muted">Participants</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="mb-1" id="totalAmount">₱0.00</h4>
                                <small class="text-muted">Total Amount</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Participants Section -->
        <div class="card mt-4">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-people me-2"></i>
                        <h6 class="mb-0">Participants</h6>
                    </div>
                    <div id="loadingIndicator" style="display: none;">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <small class="text-muted ms-2">Loading participants...</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="participantsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th style="width: 120px;">Item No.</th>
                                <th>Participant Name</th>
                                <th style="width: 200px;">Membership Type</th>
                                <th style="width: 130px;">Registration Fee</th>
                                <th style="width: 130px;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-person-plus fs-1 text-muted mb-3"></i>
                                        <h6 class="text-muted">No Company Selected</h6>
                                        <p class="text-muted mb-0">Please select a company to load participants</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- SOA Released Tab -->
    <div id="soaReleasedTab" style="display:none;">
        <div class="section-header">
            <h4>SOA Released</h4>
        </div>
        
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="stats-card primary">
                    <div class="icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <h3><?= number_format($total_soa) ?></h3>
                    <p>SOAs Released</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="bi bi-file-earmark-check me-2"></i>Released SOA Records
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
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
                            <td>
                                <strong><?= htmlspecialchars($row['soa_number']) ?></strong>
                            </td>
                            <td><?= date('M d, Y g:i A', strtotime($row['created_at'])) ?></td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr>
                            <td colspan="2" class="empty-state">
                                <i class="bi bi-file-earmark-x"></i>
                                <p>No SOA records found.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Manual SOA Generator Tab -->
    <div id="manualSoaTab" style="display:none;">
        <div class="section-header mb-4">
            <h4><i class="bi bi-file-earmark-pdf me-2"></i>Manual SOA Generator</h4>
            <p class="text-muted mb-0">Manually create a Statement of Account with custom participants</p>
        </div>
        <form id="manualSoaForm" action="generate_soa_manual.php" method="POST" target="_blank">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="bi bi-building me-2"></i>Company Information
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">SOA Number</label>
                                    <input type="text" class="form-control" name="soa_number" value="<?= htmlspecialchars($manual_next_soa) ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date</label>
                                    <input type="text" class="form-control" value="<?= date('Y-m-d') ?>" readonly>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Company Name</label>
                                    <input type="text" class="form-control" name="company_name" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Company Address</label>
                                    <input type="text" class="form-control" name="company_address" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">TIN</label>
                                    <input type="text" class="form-control" name="tin">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Business Style</label>
                                    <input type="text" class="form-control" name="business_style">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Particulars</label>
                                    <textarea class="form-control" name="particulars" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="bi bi-people me-2"></i>Participants
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="manualParticipantsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 60px;">#</th>
                                            <th>Name</th>
                                            <th style="width: 200px;">Membership Type</th>
                                            <th style="width: 130px;">Reg. Fee</th>
                                            <th style="width: 130px;">Amount</th>
                                            <th style="width: 60px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="empty-row">
                                            <td colspan="6" class="text-center py-4">
                                                <div class="empty-state">
                                                    <i class="bi bi-person-plus"></i>
                                                    <div>Add participants below</div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addManualParticipantBtn">
                                <i class="bi bi-plus-circle me-1"></i>Add Participant
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-lightning me-2"></i>
                                <h6 class="mb-0">Quick Actions</h6>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-outline-primary" id="manualPreviewBtn">
                                    <i class="bi bi-eye me-2"></i>Preview SOA
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-file-earmark-pdf me-2"></i>Generate PDF
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="clearManualBtn">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Clear Form
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="summary-card p-4 mt-3 mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-calculator me-2 fs-4 text-primary"></i>
                            <h6 class="mb-0">Summary</h6>
                        </div>
                        <div class="row text-center">
                            <div class="col-6">
                                <h4 class="mb-1" id="manualParticipantCount">0</h4>
                                <small class="text-muted">Participants</small>
                            </div>
                            <div class="col-6">
                                <h4 class="mb-1" id="manualTotalAmount">₱0.00</h4>
                                <small class="text-muted">Total Amount</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function showTab(tab) {
    const tabs = ['dashboardTab', 'individualTab', 'companyTab', 'soaGeneratorTab', 'soaReleasedTab', 'manualSoaTab'];
    tabs.forEach(id => document.getElementById(id).style.display = (id === tab + 'Tab') ? 'block' : 'none');

    const links = document.querySelectorAll('.sidebar a');
    links.forEach(link => link.classList.remove('active-link'));
    links.forEach(link => {
        if (link.getAttribute('onclick').includes(tab)) {
            link.classList.add('active-link');
        }
    });
}

$('#companySelect').on('change', function(){
    var address = $(this).find(':selected').data('address');
    $('#companyAddress').val(address);

    var company_id = $(this).val();
    console.log('Selected company_id:', company_id); // Debug log
    
    // Show loading indicator and reset counters
    $('#loadingIndicator').show();
    $('#participantCount').text('0');
    $('#totalAmount').text('₱0.00');
    $('#previewBtn, #generateBtn').prop('disabled', true);

    $.ajax({
        url: 'get_participants.php',
        type: 'POST',
        data: { company_id: company_id },
        dataType: 'json',
        success: function(data){
            console.log('Received data:', data); // Debug log
            $('#loadingIndicator').hide();
            
            var tbody = $('#participantsTable tbody');
            tbody.empty();
            
            // Check if response contains an error
            if (data.error) {
                tbody.append(`
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="empty-state">
                                <i class="bi bi-exclamation-triangle fs-1 text-danger mb-3"></i>
                                <h6 class="text-danger">Error Loading Participants</h6>
                                <p class="text-muted mb-0">${data.message}</p>
                            </div>
                        </td>
                    </tr>
                `);
                return;
            }
            
            if(data.length > 0){
                $.each(data, function(i, name){
                    tbody.append(`
                        <tr class="participant-row">
                            <td class="text-center fw-bold">${i+1}</td>
                            <td>
                                <select name="item_number[]" class="form-select form-select-sm" required onchange="updateMembershipType(this)">
                                    <option value="">Select Item</option>
                                    <option value="0001">0001</option>
                                    <option value="0002">0002</option>
                                    <option value="0003">0003</option>
                                    <option value="0004">0004</option>
                                    <option value="0005">0005</option>
                                    <option value="0006">0006</option>
                                    <option value="0007">0007</option>
                                    <option value="0008">0008</option>
                                    <option value="0009">0009</option>
                                    <option value="0010">0010</option>
                                    <option value="0011">0011</option>
                                    <option value="0012">0012</option>
                                    <option value="0013">0013</option>
                                    <option value="0014">0014</option>
                                </select>
                            </td>
                            <td>
                                <input type="hidden" name="participants[]" value="${name}">
                                <div class="fw-semibold">${name}</div>
                            </td>
                            <td>
                                <input type="text" name="membership_type[]" class="form-control form-control-sm" readonly>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" name="registration_fee[]" step="0.01" class="form-control amount-input" required>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" name="amount[]" step="0.01" class="form-control amount-input" required>
                                </div>
                            </td>
                        </tr>
                    `);
                });
                
                // Update participant count and enable buttons
                $('#participantCount').text(data.length);
                $('#previewBtn, #generateBtn').prop('disabled', false);
                addAmountChangeListeners();
                
            } else {
                tbody.append(`
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="empty-state">
                                <i class="bi bi-person-x fs-1 text-muted mb-3"></i>
                                <h6 class="text-muted">No Participants Found</h6>
                                <p class="text-muted mb-0">This company has no registered participants</p>
                            </div>
                        </td>
                    </tr>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', {
                status: status,
                error: error,
                response: xhr.responseText
            });
            $('#loadingIndicator').hide();
            
            var tbody = $('#participantsTable tbody');
            tbody.empty();
            tbody.append(`
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="empty-state">
                            <i class="bi bi-wifi-off fs-1 text-danger mb-3"></i>
                            <h6 class="text-danger">Connection Error</h6>
                            <p class="text-muted mb-0">Unable to load participants: ${error}</p>
                        </div>
                    </td>
                </tr>
            `);
        }
    });
});

// Add amount change listeners for real-time total calculation
function addAmountChangeListeners() {
    $('.amount-input').on('input', function() {
        calculateTotal();
    });
}

// Calculate and display total amount
function calculateTotal() {
    let total = 0;
    $('input[name="amount[]"]').each(function() {
        const value = parseFloat($(this).val()) || 0;
        total += value;
    });
    $('#totalAmount').text('₱' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
}

// Clear form functionality
$('#clearBtn').on('click', function() {
    if (confirm('Are you sure you want to clear all form data?')) {
        $('#soaForm')[0].reset();
        $('#companyAddress').val('');
        $('#participantsTable tbody').html(`
            <tr>
                <td colspan="6" class="text-center py-5">
                    <div class="empty-state">
                        <i class="bi bi-person-plus fs-1 text-muted mb-3"></i>
                        <h6 class="text-muted">No Company Selected</h6>
                        <p class="text-muted mb-0">Please select a company to load participants</p>
                    </div>
                </td>
            </tr>
        `);
        $('#participantCount').text('0');
        $('#totalAmount').text('₱0.00');
        $('#previewBtn, #generateBtn').prop('disabled', true);
    }
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
    
    // Add visual feedback
    if (data.type) {
        $(select).removeClass('is-invalid').addClass('is-valid');
    }
    
    // Recalculate total
    calculateTotal();
}

// Ensure correct tab is shown on page reload with pagination
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.search.includes('page=')) {
        showTab('individual');
    }
});

// Manual SOA Tab logic
function updateManualSummary() {
    let count = 0, total = 0;
    $('#manualParticipantsTable tbody tr').each(function() {
        if (!$(this).hasClass('empty-row')) {
            count++;
            const amt = parseFloat($(this).find('input[name="amount[]"]').val()) || 0;
            total += amt;
        }
    });
    $('#manualParticipantCount').text(count);
    $('#manualTotalAmount').text('₱' + total.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2}));
}

function addManualParticipantRow() {
    if ($('#manualParticipantsTable .empty-row').length) $('#manualParticipantsTable tbody').empty();
    const idx = $('#manualParticipantsTable tbody tr').length;
    $('#manualParticipantsTable tbody').append(`
        <tr>
            <td class="text-center fw-bold">${idx + 1}</td>
            <td><input type="text" name="participant_name[]" class="form-control form-control-sm" required></td>
            <td><input type="text" name="membership_type[]" class="form-control form-control-sm" required></td>
            <td><input type="number" name="registration_fee[]" class="form-control form-control-sm amount-input" min="0" step="0.01" required></td>
            <td><input type="number" name="amount[]" class="form-control form-control-sm amount-input" min="0" step="0.01" required></td>
            <td><button type="button" class="btn btn-link text-danger p-0 removeManualParticipantBtn"><i class="bi bi-trash"></i></button></td>
        </tr>
    `);
    updateManualParticipantNumbers();
    updateManualSummary();
}

function updateManualParticipantNumbers() {
    $('#manualParticipantsTable tbody tr').each(function(i) {
        $(this).find('td:first').text(i + 1);
    });
}

$('#addManualParticipantBtn').on('click', addManualParticipantRow);
$('#manualParticipantsTable').on('input', '.amount-input', updateManualSummary);
$('#manualParticipantsTable').on('click', '.removeManualParticipantBtn', function() {
    $(this).closest('tr').remove();
    if ($('#manualParticipantsTable tbody tr').length === 0) {
        $('#manualParticipantsTable tbody').html(`
            <tr class="empty-row">
                <td colspan="6" class="text-center py-4">
                    <div class="empty-state">
                        <i class="bi bi-person-plus"></i>
                        <div>Add participants below</div>
                    </div>
                </td>
            </tr>
        `);
    }
    updateManualParticipantNumbers();
    updateManualSummary();
});
$('#clearManualBtn').on('click', function() {
    if (confirm('Clear all form data?')) {
        $('#manualSoaForm')[0].reset();
        $('#manualParticipantsTable tbody').html(`
            <tr class="empty-row">
                <td colspan="6" class="text-center py-4">
                    <div class="empty-state">
                        <i class="bi bi-person-plus"></i>
                        <div>Add participants below</div>
                    </div>
                </td>
            </tr>
        `);
        updateManualSummary();
    }
});

$('#manualPreviewBtn').on('click', function() {
    var form = $('#manualSoaForm');
    var tempForm = form.clone();
    tempForm.attr('target', '_blank');
    tempForm.attr('action', 'generate_soa_manual.php');
    tempForm.attr('method', 'POST');
    tempForm.find('button[type="submit"], #manualPreviewBtn, #clearManualBtn').remove();
    tempForm.css('display', 'none');
    $('body').append(tempForm);
    tempForm.submit();
    tempForm.remove();
});
</script>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>