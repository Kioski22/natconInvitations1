<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require 'db.php';
require_once __DIR__ . '/helpers/csrf.php';

// Tracking totals
$totals = [
    'total_sent' => 0,
    'total_opened' => 0,
    'total_replied' => 0,
    'total_bounced' => 0,
    'total_clicked' => 0
];
$result_totals = $conn->query(
    "SELECT
        SUM(sent_at IS NOT NULL) AS total_sent,
        SUM(opened_at IS NOT NULL) AS total_opened,
        SUM(replied_at IS NOT NULL) AS total_replied,
        SUM(bounced_at IS NOT NULL) AS total_bounced,
        SUM(clicked_at IS NOT NULL) AS total_clicked
     FROM email_messages"
);
if ($result_totals) {
    $row_totals = $result_totals->fetch_assoc();
    if ($row_totals) {
        $totals = array_merge($totals, $row_totals);
    }
}

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

// Company invitation requests from supervisor form
$companyInvites = [];
$result_company_invites = $conn->query(
    "SELECT
        s.id,
        s.supervisor_name,
        s.company,
        s.company_address,
        s.designation,
        s.email,
        s.status,
        s.created_at,
        em.message_id,
        em.sent_at,
        em.status AS email_status
     FROM supervisor_invitations s
     LEFT JOIN (
        SELECT source_id, MAX(id) AS last_email_id
        FROM email_messages
        WHERE source_type = 'company'
        GROUP BY source_id
     ) em_last ON em_last.source_id = s.id
     LEFT JOIN email_messages em ON em.id = em_last.last_email_id
     ORDER BY s.created_at DESC"
);
if ($result_company_invites && $result_company_invites->num_rows > 0) {
    while ($row = $result_company_invites->fetch_assoc()) {
        $companyInvites[] = $row;
    }
}

// Get SOA Released count (modify this based on actual table structure)
$sql_soa = "SELECT COUNT(*) as total_soa FROM soa_sequence";
$result_soa = $conn->query($sql_soa);
$row_soa = $result_soa->fetch_assoc();
$total_soa = $row_soa['total_soa'];

// Bulk invitation queue
$bulkInvites = [];
$result_bulk = $conn->query("SELECT * FROM invitation_queue ORDER BY created_at DESC");
if ($result_bulk && $result_bulk->num_rows > 0) {
    while ($row = $result_bulk->fetch_assoc()) {
        $bulkInvites[] = $row;
    }
}

// Email tracking
$trackingRows = [];
$result_tracking = $conn->query("SELECT * FROM email_messages ORDER BY created_at DESC");
if ($result_tracking && $result_tracking->num_rows > 0) {
    while ($row = $result_tracking->fetch_assoc()) {
        $trackingRows[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>PSME Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
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

        .dataTables_wrapper .dt-buttons {
            margin-bottom: 12px;
        }

        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 12px;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--medium-gray);
            border-radius: var(--border-radius);
            padding: 8px 12px;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid var(--medium-gray);
            border-radius: var(--border-radius);
            padding: 6px 32px 6px 12px;
        }

        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 12px;
        }

        .dt-button.btn {
            margin-right: 8px;
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
            <span>Individual Invitations</span>
        </a>
        <a href="javascript:void(0);" onclick="showTab('company')">
            <i class="bi bi-building"></i>
            <span>Company</span>
        </a>
        <a href="javascript:void(0);" onclick="showTab('companyInvites')">
            <i class="bi bi-envelope-paper"></i>
            <span>Company Invitations</span>
        </a>
        <a href="javascript:void(0);" onclick="showTab('bulk')">
            <i class="bi bi-upload"></i>
            <span>Bulk Invitations</span>
        </a>
        <a href="javascript:void(0);" onclick="showTab('tracking')">
            <i class="bi bi-activity"></i>
            <span>Tracking</span>
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
        <div class="row g-3">
            <div class="col-md-6 col-lg-3">
                <div class="card status-card">
                    <div class="card-header">
                        <i class="bi bi-envelope-fill me-2"></i>Sent
                    </div>
                    <div class="card-body text-center">
                        <h1><?= (int)$totals['total_sent'] ?></h1>
                        <p class="mb-0">Emails sent</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card status-card">
                    <div class="card-header">
                        <i class="bi bi-envelope-open me-2"></i>Opened
                    </div>
                    <div class="card-body text-center">
                        <h1><?= (int)$totals['total_opened'] ?></h1>
                        <p class="mb-0">Opens recorded</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card status-card">
                    <div class="card-header">
                        <i class="bi bi-reply-fill me-2"></i>Replied
                    </div>
                    <div class="card-body text-center">
                        <h1><?= (int)$totals['total_replied'] ?></h1>
                        <p class="mb-0">Replies detected</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card status-card">
                    <div class="card-header">
                        <i class="bi bi-x-octagon-fill me-2"></i>Bounced
                    </div>
                    <div class="card-body text-center">
                        <h1><?= (int)$totals['total_bounced'] ?></h1>
                        <p class="mb-0">Bounces detected</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card status-card">
                    <div class="card-header">
                        <i class="bi bi-cursor-fill me-2"></i>Clicked
                    </div>
                    <div class="card-body text-center">
                        <h1><?= (int)$totals['total_clicked'] ?></h1>
                        <p class="mb-0">Clicks recorded</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Individual Tab -->
    <div id="individualTab" class="tab-content" style="display:none;">
        <h4 class="section-title">Individual Invitations</h4>
        <div class="table-responsive">
            <table class="table table-hover admin-data-table" id="individualTable" data-export-type="individual">
                <thead>
                    <tr>
                        <th>Event</th><th>Company</th><th>Email</th><th>Name</th><th>Designation</th><th>Address</th><th>Status</th>
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
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="7" class="text-center">No records found.</td></tr>
            <?php endif; ?>
            </tbody>
            </table>
        </div>
    </div>

    <!-- Company Tab -->
    <div id="companyTab" class="tab-content" style="display:none;">
        <h4 class="section-title">Company Summary</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle admin-data-table" id="companyTable" data-export-type="company">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Company Name</th>
                        <th>Address</th>
                        <th>Excel Filename</th>
                    </tr>
                </thead>
                <tbody id="companyTableBody">
                    <?php if (!empty($companies)): ?>
                        <?php foreach ($companies as $comp): ?>
                            <tr>
                                <td><?= htmlspecialchars($comp['company_id']) ?></td>
                                <td><?= htmlspecialchars($comp['company_name']) ?></td>
                                <td><?= htmlspecialchars($comp['company_address']) ?></td>
                                <td><?= htmlspecialchars($comp['excel_filename']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Company Invitations Tab -->
    <div id="companyInvitesTab" class="tab-content" style="display:none;">
        <h4 class="section-title">Company Invitation Requests</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle admin-data-table" id="companyInvitesTable" data-export-type="company_invites">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Supervisor</th>
                        <th>Designation</th>
                        <th>Company</th>
                        <th>Address</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Requested At</th>
                        <th>Email Status</th>
                        <th>Sent At</th>
                        <th>Message ID</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($companyInvites)): ?>
                        <?php foreach ($companyInvites as $invite): ?>
                            <tr>
                                <td><?= htmlspecialchars($invite['id']) ?></td>
                                <td><?= htmlspecialchars($invite['supervisor_name']) ?></td>
                                <td><?= htmlspecialchars($invite['designation']) ?></td>
                                <td><?= htmlspecialchars($invite['company']) ?></td>
                                <td><?= htmlspecialchars($invite['company_address']) ?></td>
                                <td><?= htmlspecialchars($invite['email']) ?></td>
                                <td><?= htmlspecialchars($invite['status']) ?></td>
                                <td><?= htmlspecialchars($invite['created_at']) ?></td>
                                <td><?= htmlspecialchars($invite['email_status']) ?></td>
                                <td><?= htmlspecialchars($invite['sent_at']) ?></td>
                                <td><?= htmlspecialchars($invite['message_id']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bulk Invitations Tab -->
    <div id="bulkTab" class="tab-content" style="display:none;">
        <h4 class="section-title">Bulk Invitations</h4>
        <div class="mb-4">
            <p class="text-muted mb-2">Upload a CSV with these columns:</p>
            <div class="small text-muted">type, email, full_name, designation, company, address, salutation (optional), hr_email (optional)</div>
            <div class="small text-muted">type values: company or individual</div>
            <div class="mt-2">
                <a id="downloadBulkTemplate" class="btn btn-sm btn-outline-secondary" href="#" download="bulk_invitation_template.csv">
                    <i class="bi bi-download me-1"></i>Download CSV Template
                </a>
            </div>
        </div>
        <form id="bulkInviteForm" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">CSV File</label>
                    <input type="file" id="bulkCsvFile" name="csv_file" accept=".csv" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-send me-2"></i>Upload & Queue
                    </button>
                </div>
            </div>
        </form>

        <div id="bulkPreview" class="mt-4"></div>
        <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
            <button type="button" class="btn btn-outline-primary btn-sm" id="runQueueBtn">
                <i class="bi bi-play-circle me-1"></i>Run Queue Now
            </button>
            <button type="button" class="btn btn-outline-danger btn-sm" id="retryFailedBtn">
                <i class="bi bi-arrow-clockwise me-1"></i>Retry Failed
            </button>
            <span id="runQueueStatus" class="small text-muted"></span>
            <span id="retryFailedStatus" class="small text-muted"></span>
        </div>
        <div id="bulkInviteResult" class="mt-3"></div>

        <div class="table-responsive mt-4">
            <table class="table table-bordered table-hover align-middle admin-data-table" id="bulkTable" data-export-type="bulk">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Sent At</th>
                        <th>Error</th>
                    </tr>
                </thead>
                <tbody id="bulkInviteTableBody">
                    <?php if (!empty($bulkInvites)): ?>
                        <?php foreach ($bulkInvites as $invite): ?>
                            <tr>
                                <td><?= htmlspecialchars($invite['id']) ?></td>
                                <td><?= htmlspecialchars($invite['type']) ?></td>
                                <td><?= htmlspecialchars($invite['email']) ?></td>
                                <td><?= htmlspecialchars($invite['full_name']) ?></td>
                                <td><?= htmlspecialchars($invite['company']) ?></td>
                                <td><?= htmlspecialchars($invite['status']) ?></td>
                                <td><?= htmlspecialchars($invite['sent_at']) ?></td>
                                <td><?= htmlspecialchars($invite['error_message']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tracking Tab -->
    <div id="trackingTab" class="tab-content" style="display:none;">
        <h4 class="section-title">Email Tracking</h4>
        <div class="mb-3">
            <button type="button" class="btn btn-outline-primary" id="syncRepliesBtn">
                <i class="bi bi-arrow-repeat me-2"></i>Sync Tracking
            </button>
            <span id="syncRepliesStatus" class="small text-muted ms-2"></span>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle admin-data-table" id="trackingTable" data-export-type="tracking">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Source</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Sent At</th>
                        <th>Opened At</th>
                        <th>Clicked At</th>
                        <th>Replied At</th>
                        <th>Bounced At</th>
                        <th>Last Event</th>
                    </tr>
                </thead>
                <tbody id="trackingTableBody">
                    <?php if (!empty($trackingRows)): ?>
                        <?php foreach ($trackingRows as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['source_type']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td><?= htmlspecialchars($row['sent_at']) ?></td>
                                <td><?= htmlspecialchars($row['opened_at']) ?></td>
                                <td><?= htmlspecialchars($row['clicked_at']) ?></td>
                                <td><?= htmlspecialchars($row['replied_at']) ?></td>
                                <td><?= htmlspecialchars($row['bounced_at']) ?></td>
                                <td><?= htmlspecialchars($row['last_event_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
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
        <table class="table table-bordered admin-data-table" id="soaReleasedTable" data-export-type="soa_released">
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
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script>
function getExportDateStamp() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    return year + '-' + month + '-' + day;
}

function normalizeExportSegment(value, fallbackValue) {
    const normalized = String(value || '')
        .trim()
        .toLowerCase()
        .replace(/\s+/g, '_')
        .replace(/[^a-z0-9_-]/g, '');

    return normalized || fallbackValue;
}

function getAdminTableInstance(selector) {
    if (typeof $.fn.DataTable !== 'function' || !$(selector).length || !$.fn.dataTable.isDataTable(selector)) {
        return null;
    }

    return $(selector).DataTable();
}

function refreshVisibleAdminTables() {
    if (typeof $.fn.DataTable !== 'function') {
        return;
    }

    $('.admin-data-table:visible').each(function() {
        if ($.fn.dataTable.isDataTable(this)) {
            $(this).DataTable().columns.adjust();
        }
    });
}

function setTableRows(selector, rows, emptyRowHtml) {
    const tableInstance = getAdminTableInstance(selector);
    const tbody = document.querySelector(selector + ' tbody');

    if (!tbody) {
        return;
    }

    if (tableInstance) {
        tableInstance.clear();
        if (rows.length > 0) {
            tableInstance.rows.add(rows).draw();
        } else {
            tableInstance.draw();
            tbody.innerHTML = emptyRowHtml;
        }
        tableInstance.columns.adjust();
        return;
    }

    tbody.innerHTML = rows.length > 0 ? rows.join('') : emptyRowHtml;
}

function initializeAdminDataTables() {
    if (typeof $.fn.DataTable !== 'function') {
        return;
    }

    $('.admin-data-table').each(function() {
        const table = $(this);
        if ($.fn.dataTable.isDataTable(this)) {
            return;
        }

        const exportType = normalizeExportSegment(table.data('export-type'), 'table');

        table.DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            order: [],
            language: {
                emptyTable: 'No records found.'
            },
            dom: "<'row align-items-center mb-3'<'col-md-6'B><'col-md-6'f>>" +
                "<'row'<'col-12'tr>>" +
                "<'row align-items-center mt-3'<'col-md-5'i><'col-md-7'p>>",
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="bi bi-file-earmark-excel me-1"></i>Export Excel',
                className: 'btn btn-success btn-sm',
                filename: function() {
                    const instance = table.DataTable();
                    const filterText = normalizeExportSegment(instance.search(), 'all');
                    return exportType + '_' + getExportDateStamp() + '_' + filterText;
                },
                title: null,
                exportOptions: {
                    search: 'applied',
                    order: 'applied'
                }
            }]
        });
    });
}

function showTab(tab) {
    // Hide all tabs first
    const tabs = ['dashboardTab', 'individualTab', 'companyTab', 'companyInvitesTab', 'bulkTab', 'trackingTab', 'soaGeneratorTab', 'soaReleasedTab'];
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
    refreshVisibleAdminTables();
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

document.addEventListener('DOMContentLoaded', function() {
    initializeAdminDataTables();
});

const bulkForm = document.getElementById('bulkInviteForm');
const bulkFileInput = document.getElementById('bulkCsvFile');
const bulkPreview = document.getElementById('bulkPreview');
const downloadBulkTemplate = document.getElementById('downloadBulkTemplate');
const bulkColumns = ['type', 'email', 'full_name', 'designation', 'company', 'address', 'salutation', 'hr_email'];
let bulkPreviewRows = [];

function parseCsvText(text) {
    const rows = [];
    let current = '';
    let inQuotes = false;
    const pushCell = (row, cell) => row.push(cell);
    let row = [];

    for (let i = 0; i < text.length; i++) {
        const char = text[i];
        if (char === '"') {
            if (inQuotes && text[i + 1] === '"') {
                current += '"';
                i++;
            } else {
                inQuotes = !inQuotes;
            }
        } else if (char === ',' && !inQuotes) {
            pushCell(row, current);
            current = '';
        } else if ((char === '\n' || char === '\r') && !inQuotes) {
            if (char === '\r' && text[i + 1] === '\n') {
                i++;
            }
            pushCell(row, current);
            current = '';
            if (row.length > 1 || (row.length === 1 && row[0].trim() !== '')) {
                rows.push(row);
            }
            row = [];
        } else {
            current += char;
        }
    }

    if (current.length > 0 || row.length > 0) {
        pushCell(row, current);
        rows.push(row);
    }

    return rows;
}

function buildCsvText(headers, rows) {
    const escapeCell = (value) => {
        const str = String(value ?? '');
        if (str.includes('"') || str.includes(',') || str.includes('\n') || str.includes('\r')) {
            return '"' + str.replace(/"/g, '""') + '"';
        }
        return str;
    };

    const lines = [];
    lines.push(headers.map(escapeCell).join(','));
    rows.forEach(row => {
        const line = headers.map((header) => escapeCell(row[header] ?? '')).join(',');
        lines.push(line);
    });
    return lines.join('\r\n');
}

function renderBulkPreview(headers, rows) {
    if (!bulkPreview) {
        return;
    }

    if (rows.length === 0) {
        bulkPreview.innerHTML = '<div class="text-muted">No rows found to preview.</div>';
        return;
    }

    let html = '<div class="alert alert-info">Preview the CSV before sending. You can adjust the type per row.</div>';
    html += '<div class="table-responsive"><table class="table table-bordered table-sm align-middle">';
    html += '<thead class="table-light"><tr>';
    headers.forEach(col => {
        html += '<th>' + col + '</th>';
    });
    html += '</tr></thead><tbody>';

    rows.forEach((row, index) => {
        html += '<tr data-row-index="' + index + '">';
        headers.forEach(col => {
            if (col === 'type') {
                const current = (row[col] || 'individual').toLowerCase();
                html += '<td>' +
                    '<select class="form-select form-select-sm bulk-type" data-col="type">' +
                        '<option value="individual"' + (current === 'individual' ? ' selected' : '') + '>individual</option>' +
                        '<option value="company"' + (current === 'company' ? ' selected' : '') + '>company</option>' +
                    '</select>' +
                '</td>';
            } else {
                const value = row[col] ?? '';
                html += '<td><input type="text" class="form-control form-control-sm bulk-cell" data-col="' + col + '" value="' +
                    String(value).replace(/"/g, '&quot;') + '"></td>';
            }
        });
        html += '</tr>';
    });

    html += '</tbody></table></div>';
    bulkPreview.innerHTML = html;
}

function setBulkPreviewFromFile(file) {
    const reader = new FileReader();
    reader.onload = function(event) {
        const text = event.target.result || '';
        const rows = parseCsvText(text);
        if (rows.length === 0) {
            bulkPreviewRows = [];
            renderBulkPreview(bulkColumns, []);
            return;
        }

        const headerRow = rows.shift().map(col => col.trim().toLowerCase());
        if (headerRow.length > 0) {
            headerRow[0] = headerRow[0].replace(/^\ufeff/, '');
        }
        const headers = headerRow.length ? headerRow : bulkColumns;
        const normalizedRows = rows.map(row => {
            const data = {};
            headers.forEach((header, idx) => {
                data[header] = row[idx] ?? '';
            });
            return data;
        });

        bulkPreviewRows = normalizedRows;
        renderBulkPreview(headers, normalizedRows);
    };
    reader.readAsText(file);
}

function collectPreviewRows(headers) {
    const collected = [];
    const rows = bulkPreview.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const data = {};
        headers.forEach(header => {
            if (header === 'type') {
                const select = row.querySelector('select[data-col="type"]');
                data.type = select ? select.value : 'individual';
            } else {
                const input = row.querySelector('input[data-col="' + header + '"]');
                data[header] = input ? input.value : '';
            }
        });
        collected.push(data);
    });
    return collected;
}

if (downloadBulkTemplate) {
    const templateRow = {
        type: 'individual',
        email: 'jane.doe@example.com',
        full_name: 'Jane Doe',
        designation: 'Mechanical Engineer',
        company: 'Example Corp',
        address: '123 Main St, City',
        salutation: 'Engr.',
        hr_email: 'hr@example.com'
    };
    const templateCsv = buildCsvText(bulkColumns, [templateRow]);
    const blob = new Blob([templateCsv], { type: 'text/csv' });
    downloadBulkTemplate.href = URL.createObjectURL(blob);
}

if (bulkFileInput) {
    bulkFileInput.addEventListener('change', function() {
        const file = bulkFileInput.files && bulkFileInput.files[0];
        if (!file) {
            bulkPreviewRows = [];
            bulkPreview.innerHTML = '';
            return;
        }
        setBulkPreviewFromFile(file);
    });
}

if (bulkForm) {
    bulkForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(bulkForm);
        const resultEl = document.getElementById('bulkInviteResult');
        if (!bulkPreview || bulkPreviewRows.length === 0) {
            resultEl.innerHTML = '<div class="alert alert-warning">Please upload a CSV and preview it before sending.</div>';
            return;
        }
        const previewHeaders = bulkPreview.querySelectorAll('thead th');
        const headers = previewHeaders.length
            ? Array.from(previewHeaders).map(th => th.textContent.trim())
            : bulkColumns;
        const updatedRows = collectPreviewRows(headers);
        const previewCsv = buildCsvText(headers, updatedRows);
        formData.set('csv_file', new Blob([previewCsv], { type: 'text/csv' }), 'bulk_preview.csv');
        resultEl.innerHTML = '<div class="text-muted">Uploading and sending...</div>';

        try {
            const response = await fetch('process_bulk_invites.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.error) {
                resultEl.innerHTML = '<div class="alert alert-danger">' + data.error + '</div>';
                return;
            }
            let html = '<div class="alert alert-success">' +
                'Batch ' + (data.batch_id || '-') + ': ' +
                (data.queued ?? 0) + ' queued, ' + data.failed + ' failed, ' + data.total + ' total.' +
                '</div>';
            if (data.errors && data.errors.length > 0) {
                html += '<div class="alert alert-warning"><strong>Errors:</strong><ul>';
                data.errors.forEach(err => {
                    html += '<li>Row ' + err.row + ': ' + err.error + '</li>';
                });
                html += '</ul></div>';
            }
            resultEl.innerHTML = html;
        } catch (err) {
            resultEl.innerHTML = '<div class="alert alert-danger">Upload failed. Please try again.</div>';
        }
    });
}

const retryFailedBtn = document.getElementById('retryFailedBtn');
const retryFailedStatus = document.getElementById('retryFailedStatus');
const runQueueBtn = document.getElementById('runQueueBtn');
const runQueueStatus = document.getElementById('runQueueStatus');
const csrfInput = document.querySelector('input[name="csrf_token"]');
const getCsrfToken = () => (csrfInput ? csrfInput.value : '');

if (runQueueBtn) {
    runQueueBtn.addEventListener('click', async function() {
        const csrfToken = getCsrfToken();

        if (runQueueStatus) {
            runQueueStatus.textContent = 'Running queue...';
        }

        try {
            const response = await fetch('run_queue.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'csrf_token=' + encodeURIComponent(csrfToken)
            });
            const data = await response.json();
            if (data.error) {
                if (runQueueStatus) {
                    runQueueStatus.textContent = data.error;
                }
                return;
            }
            if (runQueueStatus) {
                runQueueStatus.textContent = 'Sent: ' + (data.sent ?? 0) + ', Failed: ' + (data.failed ?? 0) + '.';
            }
        } catch (err) {
            if (runQueueStatus) {
                runQueueStatus.textContent = 'Queue run failed. Please try again.';
            }
        }
    });
}
if (retryFailedBtn) {
    retryFailedBtn.addEventListener('click', async function() {
        const csrfToken = getCsrfToken();

        if (retryFailedStatus) {
            retryFailedStatus.textContent = 'Retrying failed items...';
        }

        try {
            const response = await fetch('retry_failed.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'csrf_token=' + encodeURIComponent(csrfToken)
            });
            const data = await response.json();
            if (data.error) {
                if (retryFailedStatus) {
                    retryFailedStatus.textContent = data.error;
                }
                return;
            }
            if (retryFailedStatus) {
                retryFailedStatus.textContent = 'Reset: ' + (data.reset ?? 0) + ' failed item(s).';
            }
        } catch (err) {
            if (retryFailedStatus) {
                retryFailedStatus.textContent = 'Retry failed. Please try again.';
            }
        }
    });
}

function escapeHtml(value) {
    const text = String(value ?? '');
    return text
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

async function refreshTrackingTable() {
    if (!document.getElementById('trackingTableBody')) {
        return;
    }

    try {
        const response = await fetch('get_tracking.php?ts=' + Date.now(), { cache: 'no-store' });
        const data = await response.json();
        if (!data || !Array.isArray(data.rows)) {
            return;
        }

        const rows = data.rows.map(row => {
            return '<tr>' +
                '<td>' + escapeHtml(row.id) + '</td>' +
                '<td>' + escapeHtml(row.source_type) + '</td>' +
                '<td>' + escapeHtml(row.email) + '</td>' +
                '<td>' + escapeHtml(row.status) + '</td>' +
                '<td>' + escapeHtml(row.sent_at) + '</td>' +
                '<td>' + escapeHtml(row.opened_at) + '</td>' +
                '<td>' + escapeHtml(row.clicked_at) + '</td>' +
                '<td>' + escapeHtml(row.replied_at) + '</td>' +
                '<td>' + escapeHtml(row.bounced_at) + '</td>' +
                '<td>' + escapeHtml(row.last_event_at) + '</td>' +
            '</tr>';
        });

        setTableRows(
            '#trackingTable',
            rows,
            '<tr><td colspan="10" class="text-center text-muted">No tracking records yet.</td></tr>'
        );
    } catch (err) {
        // Ignore transient polling errors.
    }
}

const syncRepliesBtn = document.getElementById('syncRepliesBtn');
const syncRepliesStatus = document.getElementById('syncRepliesStatus');
if (syncRepliesBtn) {
    syncRepliesBtn.addEventListener('click', async function() {
        if (syncRepliesStatus) {
            syncRepliesStatus.textContent = 'Syncing tracking data...';
        }

        try {
            const openResponse = await fetch('sync_opens.php?ts=' + Date.now(), { cache: 'no-store' });
            const openText = await openResponse.text();

            const replyResponse = await fetch('sync_replies.php?ts=' + Date.now(), { cache: 'no-store' });
            const replyText = await replyResponse.text();

            const bounceResponse = await fetch('sync_bounces.php?ts=' + Date.now(), { cache: 'no-store' });
            const bounceText = await bounceResponse.text();

            if (syncRepliesStatus) {
                syncRepliesStatus.textContent =
                    (openText.trim() || 'Open sync complete.') + ' | ' +
                    (replyText.trim() || 'Reply sync complete.') + ' | ' +
                    (bounceText.trim() || 'Bounce sync complete.');
            }
        } catch (err) {
            if (syncRepliesStatus) {
                syncRepliesStatus.textContent = 'Sync failed. Please try again.';
            }
        } finally {
            await refreshTrackingTable();
        }
    });
}
</script>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>